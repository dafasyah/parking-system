@extends('layouts.app')

@section('title', 'Edit Tarif Parkir')
@section('page-title', 'Edit Tarif Parkir')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('settings.parking-rates.index') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Tarif
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Edit Tarif Parkir</h1>
        <p class="text-sm text-gray-600">Perbarui tarif per jam untuk kendaraan jenis <span class="font-semibold capitalize">{{ $rate->vehicle_type }}</span></p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('settings.parking-rates.update', $rate->id) }}" 
              x-data="{ submitting: false }" 
              @submit="submitting = true">
            @csrf
            @method('PUT')

            {{-- Vehicle Type Display --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Kendaraan</label>
                <div class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg 
                                {{ $rate->vehicle_type === 'motor' ? 'bg-blue-100' : 'bg-green-100' }}">
                        <i class="fas {{ $rate->vehicle_type === 'motor' ? 'fa-motorcycle' : 'fa-car' }} text-2xl
                                  {{ $rate->vehicle_type === 'motor' ? 'text-blue-600' : 'text-green-600' }}"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-lg font-semibold text-gray-900 capitalize">{{ $rate->vehicle_type }}</div>
                        <div class="text-sm text-gray-500">{{ $rate->vehicle_type === 'motor' ? 'Sepeda Motor' : 'Mobil' }}</div>
                    </div>
                </div>
            </div>

            {{-- Hourly Rate Input --}}
            <div class="mb-6">
                <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                    Tarif Per Jam
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 font-medium">
                        Rp
                    </span>
                    <input type="number" 
                           id="hourly_rate" 
                           name="hourly_rate" 
                           value="{{ old('hourly_rate', $rate->hourly_rate) }}"
                           min="1000"
                           max="100000"
                           step="1000"
                           required
                           class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg text-lg font-semibold 
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  @error('hourly_rate') border-red-300 @enderror"
                           placeholder="5000">
                </div>
                @error('hourly_rate')
                    <p class="mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tarif minimal Rp 1.000 dan maksimal Rp 100.000
                </p>
            </div>

            {{-- Current Rate Info --}}
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock text-amber-600 mt-0.5"></i>
                    <div class="text-sm">
                        <p class="font-medium text-amber-900 mb-1">Tarif Saat Ini</p>
                        <p class="text-amber-800">
                            <span class="text-2xl font-bold">Rp {{ number_format($rate->hourly_rate, 0, ',', '.') }}</span>
                            <span class="text-xs">per jam</span>
                        </p>
                        <p class="text-xs text-amber-700 mt-1">
                            Terakhir diperbarui: {{ $rate->updated_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('settings.parking-rates.index') }}" 
                   class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-gray-700 font-medium 
                          rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white font-semibold 
                               rounded-lg hover:bg-blue-700 transition-colors shadow-sm
                               disabled:bg-blue-300 disabled:cursor-not-allowed">
                    <i class="fas fa-save" x-show="!submitting"></i>
                    <i class="fas fa-spinner fa-spin" x-show="submitting" x-cloak></i>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Warning Card --}}
    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
            <div class="text-sm text-red-800">
                <p class="font-medium mb-1">Perhatian</p>
                <p class="text-xs">
                    Perubahan tarif hanya berlaku untuk transaksi baru setelah pembaruan ini disimpan. 
                    Kendaraan yang sudah parkir akan tetap menggunakan tarif lama hingga proses keluar selesai.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
