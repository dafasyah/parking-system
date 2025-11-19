@extends('layouts.app')

@section('title', 'Kendaraan Keluar')
@section('page-title', 'Struk Parkir')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ showPrintModal: true }">
    {{-- Success Icon with Animation --}}
    <div class="text-center mb-8 animate-fade-in">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h1>
        <p class="text-gray-600">Terima kasih atas kunjungan Anda</p>
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
                    <i class="fas fa-receipt text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Cetak Struk Pembayaran?</h3>
                <p class="text-sm text-gray-500">Apakah Anda ingin mencetak struk parkir sekarang?</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="showPrintModal = false" 
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Nanti Saja
                </button>
                <a href="{{ route('parking.print-receipt', $transaction->id) }}" 
                   target="_blank"
                   @click="showPrintModal = false"
                   class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors text-center">
                    <i class="fas fa-file-pdf mr-2"></i>Print PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Main Receipt Card --}}
    <div id="print-area" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-6">
        {{-- Header --}}
        <div class="text-center border-b border-gray-200 pb-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">STRUK PEMBAYARAN PARKIR</h2>
            <p class="text-sm text-gray-500">{{ config('app.name', 'Parking System') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ now()->format('d F Y, H:i') }} WIB</p>
        </div>

        {{-- Transaction Details --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Kode Transaksi</p>
                <p class="text-lg font-bold text-gray-900 font-mono">{{ $transaction->unique_code }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nomor Polisi</p>
                <p class="text-lg font-semibold text-gray-900">{{ $transaction->license_plate }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Jenis Kendaraan</p>
                <p class="text-lg font-semibold text-gray-900 capitalize">
                    <i class="fas {{ $transaction->vehicle_type === 'motor' ? 'fa-motorcycle' : 'fa-car' }} mr-1"></i>
                    {{ $transaction->vehicle_type }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Status</p>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i>
                    Selesai
                </span>
            </div>
        </div>

        {{-- Parking Duration --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Waktu Masuk</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $transaction->entry_time->format('d M Y') }}<br>
                        <span class="text-lg">{{ $transaction->entry_time->format('H:i') }} WIB</span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Waktu Keluar</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $transaction->exit_time->format('d M Y') }}<br>
                        <span class="text-lg">{{ $transaction->exit_time->format('H:i') }} WIB</span>
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Lama Parkir</p>
                <p class="text-2xl font-bold text-blue-600">
                    {{ $transaction->duration_hours }} Jam
                </p>
            </div>
        </div>

        {{-- Fee Calculation --}}
        <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Tarif per Jam</span>
                <span class="font-semibold text-gray-900">Rp {{ number_format($transaction->hourly_rate, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Durasi ({{ $transaction->duration_hours }} jam)</span>
                <span class="font-semibold text-gray-900">× {{ $transaction->duration_hours }}</span>
            </div>
            @if($transaction->overtime_duration > 0)
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Overtime ({{ $transaction->overtime_duration }} jam)</span>
                <span class="text-gray-700">Rp {{ number_format($transaction->overtime_fee, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        {{-- Total Fee --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
                    <p class="text-4xl font-bold text-gray-900">
                        Rp {{ number_format($transaction->parking_fee, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-right">
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                    <p class="text-xs text-green-600 font-semibold mt-2">LUNAS</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center text-xs text-gray-500 pt-4 border-t border-gray-200">
            <p class="mb-1">Diproses oleh: {{ $transaction->exitUser->name ?? 'System' }}</p>
            <p>Terima kasih telah menggunakan layanan parkir kami</p>
            <p class="mt-2 font-semibold">Simpan struk ini sebagai bukti pembayaran</p>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 justify-center sm:justify-end no-print">
        <a href="{{ route('parking.print-receipt', $transaction->id) }}" 
           target="_blank"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
            <i class="fas fa-file-pdf"></i>
            <span>Print/Download PDF</span>
        </a>
        <a href="{{ route('parking.exit.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Proses Exit Lain</span>
        </a>
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition-colors">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
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
    /* Hide all non-printable elements */
    .no-print, button, a, nav, aside, header, footer, 
    [x-data], .fixed {
        display: none !important;
    }
    /* Optimize for printing */
    @page {
        margin: 1cm;
        size: A4 portrait;
    }
}
</style>
@endpush
@endsection
