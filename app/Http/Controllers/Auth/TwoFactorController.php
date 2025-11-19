<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show the 2FA setup page
     */
    public function showSetup()
    {
        $user = Auth::user();
        

        if (!$user->two_factor_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->two_factor_secret = Crypt::encryptString($secret);
            $user->save();
        } else {
            $secret = Crypt::decryptString($user->two_factor_secret);
        }
        

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);
        
        return view('auth.two-factor-setup', compact('secret', 'qrCodeSvg'));
    }

    /**
     * Verify and enable 2FA
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'numeric'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.size' => 'Kode OTP harus 6 digit',
            'otp.numeric' => 'Kode OTP harus berupa angka',
        ]);

        $user = Auth::user();
        $secret = Crypt::decryptString($user->two_factor_secret);
        
        // Verify with window of 2 (allows 1 code before/after for time sync issues)
        $valid = $this->google2fa->verifyKey($secret, $request->otp, 2);
        
        if (!$valid) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah expired. Gunakan kode terbaru.']);
        }
        

        $recoveryCodes = $this->generateRecoveryCodes();
        
        $user->update([
            'is_2fa_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
        ]);
        
        // Log activity
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('Enabled Two-Factor Authentication');
        
        session(['2fa_verified' => true]);
        session(['recovery_codes' => $recoveryCodes]);
        
        return redirect()->route('2fa.recovery-codes');
    }

    /**
     * Show recovery codes
     */
    public function showRecoveryCodes()
    {
        $recoveryCodes = session('recovery_codes');
        
        if (!$recoveryCodes) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.two-factor-recovery-codes', compact('recoveryCodes'));
    }

    /**
     * Show 2FA verification page
     */
    public function showVerify()
    {
        return view('auth.two-factor-verify');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
        ]);

        $user = Auth::user();
        
        if (!$user->is_2fa_enabled) {
            return redirect()->route('dashboard');
        }

        // Try OTP first with window of 2 for time sync tolerance
        $secret = Crypt::decryptString($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->otp, 2);
        
        // If OTP fails, try recovery code
        if (!$valid) {
            $valid = $this->verifyRecoveryCode($user, $request->otp);
            
            if ($valid) {
                session()->flash('warning', 'Anda menggunakan kode recovery. Segera setup ulang 2FA untuk keamanan.');
            }
        }
        
        if (!$valid) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau expired. Gunakan kode terbaru dari aplikasi authenticator.'])->withInput();
        }
        
        session(['2fa_verified' => true]);
        
        // Log activity
        activity()
            ->causedBy($user)
            ->log('Verified Two-Factor Authentication');
        
        return redirect()->intended('dashboard');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Password wajib diisi',
            'password.current_password' => 'Password salah',
        ]);

        $user = Auth::user();
        
        $user->update([
            'is_2fa_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
        
        // Log activity
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('Disabled Two-Factor Authentication');
        
        return redirect()->route('profile.security')->with('success', '2FA berhasil dinonaktifkan');
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes($count = 8)
    {
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        
        return $codes;
    }

    /**
     * Verify recovery code
     */
    protected function verifyRecoveryCode($user, $code)
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }
        
        $recoveryCodes = json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true);
        
        if (in_array(strtoupper($code), $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_diff($recoveryCodes, [strtoupper($code)]);
            
            $user->update([
                'two_factor_recovery_codes' => Crypt::encryptString(json_encode(array_values($recoveryCodes))),
            ]);
            
            return true;
        }
        
        return false;
    }
}
