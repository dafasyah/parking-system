@extends('layouts.guest')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4 py-12">
    <div class="max-w-2xl w-full">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-600 mb-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Aktifkan Two-Factor Authentication</h1>
            <p class="text-gray-600">Tingkatkan keamanan akun Anda dengan autentikasi dua faktor</p>
        </div>

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-6">
            {{-- Instructions --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Langkah-langkah Setup:</h2>
                <ol class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                        <span>Install aplikasi <strong>Google Authenticator</strong> atau <strong>Authy</strong> di smartphone Anda</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                        <span>Scan QR Code di bawah ini dengan aplikasi authenticator</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                        <span>Masukkan kode 6 digit yang muncul di aplikasi untuk verifikasi</span>
                    </li>
                </ol>
            </div>

            <div class="border-t border-gray-200 pt-8">
                <div class="grid md:grid-cols-2 gap-8">
                    {{-- QR Code Section --}}
                    <div class="flex flex-col items-center">
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Scan QR Code</h3>
                        <div class="p-4 bg-white border-2 border-gray-300 rounded-xl shadow-sm">
                            {!! $qrCodeSvg !!}
                        </div>
                        <p class="mt-4 text-xs text-gray-500 text-center max-w-xs">
                            Gunakan kamera smartphone Anda untuk scan QR Code ini
                        </p>
                    </div>

                    {{-- Manual Entry Section --}}
                    <div class="flex flex-col">
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Atau Input Manual</h3>
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg mb-4">
                            <p class="text-xs text-gray-600 mb-2">Secret Key:</p>
                            <code class="block p-3 bg-white border border-gray-300 rounded text-sm font-mono text-gray-900 break-all select-all">
                                {{ $secret }}
                            </code>
                        </div>
                        <p class="text-xs text-gray-500 mb-6">
                            Jika tidak bisa scan QR Code, masukkan secret key ini secara manual di aplikasi authenticator
                        </p>

                        {{-- Verification Form --}}
                        <form method="POST" action="{{ route('2fa.enable') }}" x-data="{ submitting: false }">
                            @csrf
                            <div class="mb-6">
                                <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Verifikasi (6 Digit)
                                </label>
                                <input type="text" 
                                       id="otp" 
                                       name="otp" 
                                       inputmode="numeric"
                                       pattern="[0-9]{6}"
                                       maxlength="6"
                                       required
                                       autofocus
                                       class="block w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('otp') border-red-300 @enderror"
                                       placeholder="000000">
                                @error('otp')
                                    <p class="mt-2 text-sm text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button type="submit"
                                    @click="submitting = true"
                                    :disabled="submitting"
                                    class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg disabled:bg-blue-300 disabled:cursor-not-allowed">
                                <span x-show="!submitting" class="inline-flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Verifikasi & Aktifkan 2FA</span>
                                </span>
                                <span x-show="submitting" class="inline-flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Memverifikasi...</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                <div class="text-sm text-amber-800">
                    <p class="font-medium mb-1">Penting untuk Diketahui</p>
                    <p class="text-xs">
                        Setelah 2FA diaktifkan, Anda akan diminta memasukkan kode 6 digit setiap kali login. 
                        Pastikan aplikasi authenticator Anda selalu accessible. Anda juga akan mendapatkan kode recovery untuk backup.
                    </p>
                </div>
            </div>
        </div>

        {{-- Back Link --}}
        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-focus on OTP input
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.focus();
            
            // Only allow numbers
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
@endpush
@endsection
