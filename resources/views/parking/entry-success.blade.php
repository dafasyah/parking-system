@extends('layouts.app')

@section('title', 'Tiket Parkir')
@section('page-title', 'Tiket Parkir')

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="max-w-4xl mx-auto" x-data="{ showPrintModal: true }">
    {{-- Success Icon with Animation --}}
    <div class="text-center mb-8 animate-fade-in">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kendaraan Berhasil Masuk Parkir!</h1>
        <p class="text-gray-600">Simpan dan tunjukkan tiket ini saat keluar parkir</p>
    </div>

    {{-- Print Confirmation Modal --}}
    <div x-show="showPrintModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showPrintModal = false"></div>
        <div class="relative z-50 w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 mx-4 p-6" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="text-center mb-6">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 mb-4">
                    <i class="fas fa-print text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Cetak Tiket Parkir?</h3>
                <p class="text-sm text-gray-500">Apakah Anda ingin mencetak QR Code dan tiket parkir sekarang?</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="showPrintModal = false" 
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Nanti Saja
                </button>
                <a href="{{ route('parking.print-ticket', $transaction->id) }}" 
                   target="_blank"
                   @click="showPrintModal = false"
                   class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors text-center">
                    <i class="fas fa-file-pdf mr-2"></i>Print PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Main Ticket Card --}}
    <div id="print-area" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-6">
        <div class="flex flex-col md:flex-row gap-8">
            {{-- QR Code Section --}}
            <div class="flex-1 flex flex-col items-center justify-center">
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50">
                    <img src="{{ url('storage/' . $transaction->qr_code_path) }}" 
                         alt="QR Code Parkir" 
                         class="w-[300px] h-[300px] object-contain" />
                </div>
                <p class="mt-4 text-sm text-gray-500 text-center max-w-xs">
                    Scan QR Code ini saat keluar untuk proses yang lebih cepat
                </p>
            </div>

            {{-- Details Section --}}
            <div class="flex-1 flex flex-col justify-center space-y-6">
                {{-- Unique Code --}}
                <div class="text-center md:text-left">
                    <p class="text-sm text-gray-500 mb-2">Kode Unik</p>
                    <h2 class="text-4xl font-bold text-gray-900 tracking-wide font-mono">
                        {{ $transaction->unique_code }}
                    </h2>
                </div>

                {{-- Vehicle Details Grid --}}
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nomor Polisi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $transaction->license_plate }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Jenis Kendaraan</p>
                        <p class="text-lg font-semibold text-gray-900 capitalize">{{ $transaction->vehicle_type }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Waktu Masuk</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $transaction->entry_time->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="flex items-center gap-2 pt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Sedang Parkir
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 justify-center sm:justify-end no-print">
        <a href="{{ route('parking.print-ticket', $transaction->id) }}" 
           target="_blank"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
            <i class="fas fa-file-pdf"></i>
            <span>Print/Download PDF</span>
        </a>
        <a href="{{ route('parking.download-qr', $transaction->id) }}" 
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-colors">
            <i class="fas fa-download"></i>
            <span>Download QR</span>
        </a>
        <a href="{{ route('parking.entry.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Selesai</span>
        </a>
    </div>
</div>

@push('styles')
<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px;
        box-shadow: none !important;
        border: none !important;
    }
    #print-area img {
        display: block !important;
        visibility: visible !important;
        max-width: 300px;
        height: auto;
    }
    /* Hide all non-printable elements */
    .no-print, button, a, nav, aside, header, footer, 
    [x-data], .fixed {
        display: none !important;
    }
    /* Optimize for printing */
    @page {
        margin: 1cm;
    }
}
</style>
@endpush
@endsection
