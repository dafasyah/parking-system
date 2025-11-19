@extends('layouts.app')

@section('title', 'Scan QR Keluar')
@section('page-title', 'Scan QR Keluar Parkir')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="scanPage()">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-lg font-semibold text-gray-800">Scanner QR</h2>
            <button type="button" class="text-xs rounded-full border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-50" @click="toggleMode()">
                <span x-show="mode === 'scan'">Gunakan Input Manual</span>
                <span x-show="mode === 'manual'">Gunakan Kamera</span>
            </button>
        </div>
        <div x-show="mode === 'scan'" x-transition>
            <div id="qr-reader" class="w-full rounded-lg border border-gray-200 overflow-hidden"></div>
            <p class="mt-2 text-xs text-gray-500">Arahkan kamera ke QR Code tiket parkir Anda.</p>
        </div>
        <div x-show="mode === 'manual'" x-transition class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Kode Unik</label>
            <input type="text" x-model="code" placeholder="PARK-YYYYMMDD-XXXX" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            <button type="button" @click="checkCode()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" :disabled="loading">
                <template x-if="!loading"><span>Cek &amp; Proses</span></template>
                <template x-if="loading">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Memproses...</span>
                    </span>
                </template>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Detail Tiket</h2>
            <template x-if="!transaction">
                <p class="text-xs text-gray-500">Detail tiket akan muncul di sini setelah QR berhasil dipindai atau kode unik diinput.</p>
            </template>
            <template x-if="transaction">
                <div class="space-y-2 text-xs mt-2">
                    <div class="flex justify-between"><span class="text-gray-500">Kode</span><span class="font-semibold text-gray-900" x-text="transaction.unique_code"></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Nomor Polisi</span><span class="font-semibold text-gray-900" x-text="transaction.license_plate"></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Jenis</span><span class="font-semibold text-gray-900 capitalize" x-text="transaction.vehicle_type"></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Waktu Masuk</span><span class="font-semibold text-gray-900" x-text="transaction.entry_time"></span></div>
                </div>
            </template>
        </div>
        <div class="mt-4 flex justify-end">
            <form method="POST" :action="routeExit" x-show="transaction">
                @csrf
                <input type="hidden" name="unique_code" :value="transaction?.unique_code">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    Proses Keluar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function scanPage() {
    return {
        mode: 'scan',
        code: '',
        loading: false,
        transaction: null,
        reader: null,
        routeCheck: "{{ route('parking.check') }}",
        routeExit: "{{ route('parking.exit.store') }}",
        init() {
            this.initScanner();
        },
        initScanner() {
            if (!document.getElementById('qr-reader')) return;
            if (typeof Html5Qrcode === 'undefined') {
                console.error('html5-qrcode library not loaded');
                window.toast && window.toast({
                    icon: 'error',
                    title: 'QR Scanner tidak tersedia. Gunakan input manual.'
                });
                this.mode = 'manual';
                return;
            }
            
            this.reader = new Html5Qrcode('qr-reader');
            
            // Request camera permission and start scanning
            this.reader.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText) => {
                    // Stop scanner after successful scan
                    this.reader.stop().then(() => {
                        this.code = decodedText;
                        this.checkCode();
                    }).catch(err => console.error('Stop scan error:', err));
                },
                (errorMessage) => {
                    // Scan error, just ignore
                }
            ).catch(err => {
                console.error('Scanner start error:', err);
                window.toast && window.toast({
                    icon: 'warning',
                    title: 'Kamera tidak dapat diakses. Gunakan input manual.',
                    timer: 3000
                });
                this.mode = 'manual';
            });
        },
        async checkCode() {
            if (!this.code) {
                window.toast && window.toast({
                    icon: 'warning',
                    title: 'Masukkan kode unik terlebih dahulu'
                });
                return;
            }
            
            this.loading = true;
            try {
                const res = await fetch(this.routeCheck + '?code=' + encodeURIComponent(this.code));
                const data = await res.json();
                
                if (data && data.unique_code) {
                    this.transaction = data;
                    if (window.toast) {
                        window.toast({
                            icon: 'success',
                            title: 'Tiket ditemukan! Silakan proses keluar.'
                        });
                    }
                } else {
                    this.transaction = null;
                    if (window.toast) {
                        window.toast({
                            icon: 'error',
                            title: 'Kode tidak valid atau kendaraan sudah keluar'
                        });
                    }
                }
            } catch (e) {
                console.error('Check code error:', e);
                if (window.toast) {
                    window.toast({
                        icon: 'error',
                        title: 'Gagal memeriksa tiket. Silakan coba lagi.'
                    });
                }
            } finally {
                this.loading = false;
            }
        },
        toggleMode() {
            if (this.mode === 'scan' && this.reader) {
                this.reader.stop().catch(() => {});
            }
            
            this.mode = this.mode === 'scan' ? 'manual' : 'scan';
            
            if (this.mode === 'scan') {
                this.$nextTick(() => {
                    this.initScanner();
                });
            }
        }
    }
}
</script>
@endpush
