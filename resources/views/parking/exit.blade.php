@extends('layouts.app')

@section('title', 'Keluar Parkir')
@section('page-title', 'Proses Keluar Parkir')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ loading: false }">
    <h2 class="text-lg font-semibold text-gray-800 mb-1">Input Kode Tiket</h2>
    <p class="text-xs text-gray-500 mb-5">Masukkan kode unik tiket parkir atau gunakan halaman scan QR.</p>

    <form method="POST" action="{{ route('parking.exit.store') }}" @submit="loading = true">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="unique_code" class="block text-sm font-medium text-gray-700">Kode Unik</label>
                <input id="unique_code" name="unique_code" type="text" value="{{ old('unique_code') }}" required placeholder="PARK-YYYYMMDD-XXXX"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
                @error('unique_code')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @isset($transaction)
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-xs space-y-2">
                    <div class="flex justify-between"><span class="text-gray-500">Nomor Polisi</span><span class="font-semibold text-gray-800">{{ $transaction->license_plate }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Jenis</span><span class="font-semibold text-gray-800 capitalize">{{ $transaction->vehicle_type }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Waktu Masuk</span><span class="font-semibold text-gray-800">{{ $transaction->entry_time->format('d M Y H:i:s') }}</span></div>
                </div>
            @endisset

            <div class="flex justify-end gap-2 mt-4">
                <a href="{{ route('parking.exit.create') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-blue-400 transition-all duration-200"
                        :disabled="loading">
                    <template x-if="!loading">
                        <span>Cek &amp; Proses</span>
                    </template>
                    <template x-if="loading">
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Memproses...</span>
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
