<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TwoFactorController extends Controller
{
    public function setup(): \Illuminate\View\View
    {
        $user = Auth::user();
        if ($user->is_2fa_enabled) {
            return view('twofactor.enabled');
        }
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $qrUrl = $google2fa->getQRCodeUrl(
            'ParkingSystem',
            $user->email,
            $secret
        );
        session(['2fa_secret' => $secret]);
        return view('twofactor.setup', compact('qrUrl','secret'));
    }

    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required']);
        $google2fa = new Google2FA();
        $secret = session('2fa_secret');
        if (!$secret || !$google2fa->verifyKey($secret, $request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP salah']);
        }
        $user = Auth::user();
        $user->two_factor_secret = Crypt::encryptString($secret);
        $user->is_2fa_enabled = true;
        $user->save();
        activity()->causedBy($user)->performedOn($user)->log('Mengaktifkan 2FA');
        return redirect()->route('dashboard')->with('success','2FA berhasil diaktifkan.');
    }
}
