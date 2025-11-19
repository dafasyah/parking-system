@extends('layouts.app')

@section('title', 'Masuk Parkir')
@section('page-title', 'Input Masuk Parkir')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ loading: false, vehicleType: 'motor' }">
    <h2 class="text-lg font-semibold text-gray-800 mb-1">Data Kendaraan Masuk</h2>
    <p class="text-xs text-gray-500 mb-5">Masukkan nomor polisi dan jenis kendaraan. Sistem akan menghasilkan kode unik dan QR Code tiket parkir.</p>

    <form method="POST" action="{{ route('parking.entry.store') }}" @submit="loading = true">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="license_plate" class="block text-sm font-medium text-gray-700">Nomor Polisi</label>
                <input id="license_plate" name="license_plate" type="text" value="{{ old('license_plate') }}" required placeholder="B 1234 CD"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
                <p class="mt-1 text-[11px] text-gray-500">Format: A / AB 1234 CD (otomatis diubah ke huruf besar).</p>
                @error('license_plate')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</span>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <label @click="vehicleType = 'motor'" 
                           :class="vehicleType === 'motor' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white'" 
                           class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 hover:border-blue-500 hover:bg-blue-50 transition-colors">
                        <input type="radio" name="vehicle_type" value="motor" class="sr-only" :checked="vehicleType === 'motor'">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-white">
                            <i class="fas fa-motorcycle text-sm"></i>
                        </span>
                        <span>Motor</span>
                    </label>
                    <label @click="vehicleType = 'mobil'" 
                           :class="vehicleType === 'mobil' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white'" 
                           class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 hover:border-blue-500 hover:bg-blue-50 transition-colors">
                        <input type="radio" name="vehicle_type" value="mobil" class="sr-only" :checked="vehicleType === 'mobil'">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-white">
                            <i class="fas fa-car text-sm"></i>
                        </span>
                        <span>Mobil</span>
                    </label>
                </div>
                @error('vehicle_type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-blue-400 transition-all duration-200"
                        :disabled="loading">
                    <template x-if="!loading">
                        <span>Proses Masuk</span>
                    </template>
                    <template x-if="loading">
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Menyimpan...</span>
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
