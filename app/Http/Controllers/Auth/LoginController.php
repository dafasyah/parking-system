<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);
        

        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            activity()
                ->withProperties(['ip' => $request->ip(), 'email' => $request->email, 'reason' => 'email_not_found'])
                ->log('Login gagal - Email tidak ditemukan: ' . $request->email);
            
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak terdaftar dalam sistem.']);
        }
        

        // if ($user->status === 'inactive') {
        //     return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        // }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $request->ip(), 'user_agent' => $request->userAgent()])
                ->log('User login berhasil: ' . auth()->user()->email);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . auth()->user()->name . '!');
        }
        
        // Password wrong
        activity()
            ->withProperties(['ip' => $request->ip(), 'email' => $request->email, 'reason' => 'wrong_password'])
            ->log('Login gagal - Password salah: ' . $request->email);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['password' => 'Password yang Anda masukkan salah.']);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        
        activity()
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('User logout: ' . $user->email);
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah logout.');
    }
}
