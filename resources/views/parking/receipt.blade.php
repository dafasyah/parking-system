@extends('layouts.app')

@section('title', 'Struk Parkir')
@section('page-title', 'Struk Parkir')

@section('content')
<div class="max-w-xl mx-auto" x-data="{ showPrintModal: true }">
    {{-- Print Confirmation Modal --}}
    <div x-show="showPrintModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showPrintModal = false"></div>
        <div class="relative z-50 w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 mx-4 p-6" x-transition>
            <div class="text-center mb-6">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 mb-4">
                    <i class="fas fa-receipt text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Cetak Struk Parkir?</h3>
                <p class="mt-2 text-sm text-gray-500">Struk berisi detail lengkap transaksi parkir Anda.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="showPrintModal = false" class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tidak
                </button>
                <button type="button" @click="window.print(); showPrintModal = false" class="flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" id="print-area">
    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Struk Parkir</h1>
                <p class="text-xs text-gray-500">{{ config('app.name', 'Parking System') }}</p>
            </div>
            <div class="text-right text-xs text-gray-500">
                <p>{{ now()->format('d M Y H:i:s') }}</p>
                <p>Kode: <span class="font-mono text-gray-800">{{ $transaction->unique_code }}</span></p>
            </div>
        </div>

        <div class="border-t border-gray-200 my-3"></div>

        <table class="w-full text-xs">
            <tbody class="text-gray-700">
                <tr>
                    <td class="py-1 w-1/3 text-gray-500">Nomor Polisi</td>
                    <td class="py-1 font-semibold">{{ $transaction->license_plate }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Jenis Kendaraan</td>
                    <td class="py-1 font-semibold capitalize">{{ $transaction->vehicle_type }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Waktu Masuk</td>
                    <td class="py-1 font-semibold">{{ $transaction->entry_time->format('d M Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Waktu Keluar</td>
                    <td class="py-1 font-semibold">{{ $transaction->exit_time->format('d M Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Durasi</td>
                    <td class="py-1 font-semibold">{{ $transaction->duration_hours }} jam</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Tarif per Jam</td>
                    <td class="py-1 font-semibold">Rp {{ number_format(($transaction->parking_fee / max($transaction->duration_hours,1)), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="border-t border-gray-200 my-3"></div>

        <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">TOTAL</span>
            <span class="text-3xl font-bold tracking-tight text-gray-900">Rp {{ number_format($transaction->parking_fee, 0, ',', '.') }}</span>
        </div>

        <p class="mt-4 text-[11px] text-gray-500 text-center">Terima kasih telah menggunakan layanan parkir kami. Simpan struk ini sebagai bukti pembayaran yang sah.</p>
    </div>
</div>

<div class="mt-4 flex justify-end no-print">
    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <i class="fas fa-print"></i>
        <span>Print Ulang</span>
    </button>
</div>
</div>

@push('styles')
<style>
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
        box-shadow: none !important;
        border: none !important;
        font-family: 'Courier New', monospace;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endpush
@endsection
