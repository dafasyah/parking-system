@extends('layouts.auth')

@section('title', 'Verifikasi 2FA')

@section('content')
    {{-- Two-factor authentication verification --}}
    <h2 class="text-xl font-semibold text-gray-900 mb-1">Verifikasi Two-Factor</h2>
    <p class="text-sm text-gray-500 mb-5">Pindai QR Code dengan aplikasi autentikator lalu masukkan kode 6 digit.</p>

    @if(session('error'))
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @isset($qrUrl)
            <div class="flex flex-col items-center justify-center gap-2">
                <img src="{{ $qrUrl }}" alt="QR Code 2FA" class="rounded-lg border border-gray-200 bg-white p-2 shadow-sm">
                <p class="text-xs text-gray-500 text-center">Jika QR tidak terbaca, Anda dapat memasukkan kode secret secara manual: <span class="font-mono text-gray-700">{{ $secret ?? '' }}</span></p>
            </div>
        @endisset

        <form method="POST" action="{{ route('twofactor.enable') }}" x-data="{ codes: ['', '', '', '', '', ''], loading: false }" @submit="loading = true; $refs.otp.value = codes.join('')">
            @csrf

            <input type="hidden" name="otp" x-ref="otp">

            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Kode OTP</label>
                <div class="flex justify-between gap-2" x-data="{}">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric"
                               x-model="codes[{{ $i }}]"
                               x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, ''); if ($event.target.value && $event.target.nextElementSibling) { $event.target.nextElementSibling.focus(); }"
                               x-on:keydown.backspace="if(!$event.target.value && $event.target.previousElementSibling){ $event.target.previousElementSibling.focus(); }"
                               class="w-10 h-12 rounded-lg border border-gray-300 text-center text-lg font-semibold tracking-widest focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500" autofocus="{{ $i === 0 ? 'autofocus' : '' }}">
                    @endfor
                </div>
                @error('otp')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if(!empty($backupCodes ?? []))
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700">
                    <p class="font-semibold mb-1">Backup Codes</p>
                    <p class="mb-1 text-gray-500">Simpan kode berikut di tempat aman. Anda dapat menggunakannya jika tidak dapat mengakses aplikasi autentikator.</p>
                    <div class="grid grid-cols-2 gap-1 font-mono">
                        @foreach($backupCodes as $code)
                            <span class="px-2 py-1 rounded bg-white border border-dashed border-gray-300">{{ $code }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <button type="submit"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-blue-400 transition-all duration-200"
                    :disabled="loading">
                <template x-if="!loading">
                    <span>Verifikasi & Aktifkan</span>
                </template>
                <template x-if="loading">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Memproses...</span>
                    </span>
                </template>
            </button>
        </form>
    </div>
@endsection
