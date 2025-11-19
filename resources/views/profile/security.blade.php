@extends('layouts.app')

@section('title', 'Keamanan Akun')
@section('page-title', 'Keamanan Akun')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Keamanan Akun</h1>
        <p class="text-sm text-gray-600">Kelola pengaturan keamanan dan autentikasi akun Anda</p>
    </div>

    {{-- Success/Warning Messages --}}
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                <p class="text-sm font-medium text-amber-800">{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    {{-- Two-Factor Authentication Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg {{ auth()->user()->is_2fa_enabled ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <i class="fas fa-shield-alt text-xl {{ auth()->user()->is_2fa_enabled ? 'text-green-600' : 'text-gray-400' }}"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">Two-Factor Authentication (2FA)</h2>
                    <p class="text-sm text-gray-600 mb-3">
                        Tingkatkan keamanan akun dengan autentikasi dua faktor menggunakan aplikasi authenticator
                    </p>
                    
                    @if(auth()->user()->is_2fa_enabled)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                            <i class="fas fa-check-circle"></i>
                            <span>Aktif sejak {{ auth()->user()->two_factor_confirmed_at?->format('d M Y') }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-sm font-medium">
                            <i class="fas fa-times-circle"></i>
                            <span>Tidak Aktif</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(auth()->user()->is_2fa_enabled)
            {{-- 2FA is Enabled --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-green-600 mt-0.5"></i>
                        <div class="text-sm text-green-800">
                            <p class="font-medium mb-1">2FA Aktif</p>
                            <p class="text-xs">
                                Akun Anda dilindungi dengan autentikasi dua faktor. Anda akan diminta memasukkan kode 
                                dari aplikasi authenticator setiap kali login.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('2fa.disable') }}" 
                      x-data="{ showPasswordInput: false, submitting: false }"
                      @submit="submitting = true">
                    @csrf
                    
                    <button type="button" 
                            @click="showPasswordInput = !showPasswordInput"
                            x-show="!showPasswordInput"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times-circle"></i>
                        <span>Nonaktifkan 2FA</span>
                    </button>

                    <div x-show="showPasswordInput" x-transition class="max-w-md">
                        <p class="text-sm text-gray-700 mb-3">Masukkan password Anda untuk mengonfirmasi:</p>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <input type="password" 
                                       name="password" 
                                       required
                                       placeholder="Password"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                    :disabled="submitting"
                                    class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 disabled:bg-red-300 disabled:cursor-not-allowed transition-colors">
                                <span x-show="!submitting">Konfirmasi</span>
                                <span x-show="submitting"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                            <button type="button" 
                                    @click="showPasswordInput = false"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            {{-- 2FA is Disabled --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                        <div class="text-sm text-amber-800">
                            <p class="font-medium mb-1">Keamanan Dapat Ditingkatkan</p>
                            <p class="text-xs">
                                Aktifkan 2FA untuk lapisan keamanan tambahan. Ini akan memerlukan kode dari 
                                aplikasi authenticator setiap kali Anda login.
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('2fa.setup') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                    <i class="fas fa-shield-alt"></i>
                    <span>Aktifkan 2FA Sekarang</span>
                </a>

                <div class="mt-4 text-xs text-gray-500">
                    <p class="mb-1">Aplikasi yang didukung:</p>
                    <div class="flex gap-4">
                        <span><i class="fab fa-google"></i> Google Authenticator</span>
                        <span><i class="fas fa-mobile-alt"></i> Authy</span>
                        <span><i class="fas fa-key"></i> Microsoft Authenticator</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Password Change Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <i class="fas fa-key text-xl text-gray-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-1">Ubah Password</h2>
                <p class="text-sm text-gray-600">
                    Pastikan menggunakan password yang kuat dan unik untuk akun Anda
                </p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-4">
            <p class="text-sm text-gray-600 mb-4">
                Hubungi administrator untuk mengubah password Anda.
            </p>
        </div>
    </div>
</div>
@endsection
