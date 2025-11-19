@extends('layouts.guest')

@section('title', 'Verify Two-Factor Authentication')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4 py-12">
    <div class="max-w-md w-full">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600 mb-4 animate-pulse">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi Two-Factor</h1>
            <p class="text-gray-600">Masukkan kode autentikasi untuk melanjutkan</p>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8" x-data="{ useRecovery: false, submitting: false }">
            <form method="POST" action="{{ route('2fa.verify.post') }}" @submit="submitting = true">
                @csrf

                {{-- Standard OTP Input --}}
                <div x-show="!useRecovery" x-transition>
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-3">
                            <i class="fas fa-mobile-alt text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Kode dari Authenticator</h2>
                        <p class="text-sm text-gray-600">Buka aplikasi Google Authenticator atau Authy Anda</p>
                    </div>

                    <div class="mb-6">
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                            Masukkan Kode 6 Digit
                        </label>
                        <input type="text" 
                               id="otp" 
                               name="otp" 
                               inputmode="numeric"
                               pattern="[0-9]{6}"
                               maxlength="6"
                               required
                               autofocus
                               class="block w-full px-4 py-4 text-center text-3xl font-mono tracking-widest border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('otp') border-red-300 @enderror"
                               placeholder="000000">
                        @error('otp')
                            <p class="mt-2 text-sm text-red-600 text-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Recovery Code Input --}}
                <div x-show="useRecovery" x-transition>
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-100 mb-3">
                            <i class="fas fa-key text-amber-600 text-xl"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Kode Recovery</h2>
                        <p class="text-sm text-gray-600">Masukkan salah satu kode backup Anda</p>
                    </div>

                    <div class="mb-6">
                        <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                            Format: XXXX-XXXX
                        </label>
                        <input type="text" 
                               id="recovery_code" 
                               name="otp" 
                               maxlength="9"
                               x-ref="recoveryInput"
                               class="block w-full px-4 py-4 text-center text-xl font-mono tracking-widest uppercase border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="XXXX-XXXX">
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        :disabled="submitting"
                        class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg disabled:bg-blue-300 disabled:cursor-not-allowed mb-4">
                    <span x-show="!submitting" class="inline-flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Verifikasi & Masuk</span>
                    </span>
                    <span x-show="submitting" x-cloak class="inline-flex items-center justify-center gap-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Memverifikasi...</span>
                    </span>
                </button>

                {{-- Toggle Recovery Code --}}
                <div class="text-center">
                    <button type="button" 
                            @click="useRecovery = !useRecovery; $nextTick(() => { if(useRecovery) $refs.recoveryInput.focus(); else document.getElementById('otp').focus(); })"
                            class="text-sm text-gray-600 hover:text-gray-900 underline">
                        <span x-show="!useRecovery">Gunakan Kode Recovery</span>
                        <span x-show="useRecovery">Gunakan Authenticator</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Help Card --}}
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-question-circle text-gray-500 mt-0.5"></i>
                <div class="text-sm text-gray-700">
                    <p class="font-medium mb-1">Tidak bisa mengakses aplikasi authenticator?</p>
                    <p class="text-xs text-gray-600">
                        Gunakan salah satu kode recovery 8-digit yang telah Anda simpan saat setup 2FA. 
                        Kode recovery hanya bisa digunakan sekali.
                    </p>
                </div>
            </div>
        </div>

        {{-- Logout Link --}}
        <div class="text-center mt-6">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.focus();
            
            // Only allow numbers for OTP
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
        
        // Format recovery code with dash
        const recoveryInput = document.getElementById('recovery_code');
        if (recoveryInput) {
            recoveryInput.addEventListener('input', function(e) {
                let value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                if (value.length > 4) {
                    value = value.substring(0, 4) + '-' + value.substring(4, 8);
                }
                this.value = value;
            });
        }
    });
</script>
@endpush
@endsection
