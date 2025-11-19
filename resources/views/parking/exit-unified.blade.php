@extends('layouts.app')

@section('title', 'Keluar Parkir')
@section('page-title', 'Proses Keluar Parkir')

@section('content')
<div class="max-w-4xl mx-auto" x-data="exitPage()">
    {{-- Header --}}
    <div class="bg-white rounded-t-2xl shadow-sm border border-gray-200 border-b-0 p-6">
        <h1 class="text-2xl font-bold text-gray-900">Keluar Parkir</h1>
        <p class="text-sm text-gray-600 mt-1">Pilih metode untuk memproses kendaraan keluar</p>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white border-x border-gray-200">
        <div class="flex border-b border-gray-200">
            <button @click="activeTab = 'scan'" 
                    :class="activeTab === 'scan' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors">
                <i class="fas fa-qrcode mr-2"></i>
                Scan QR Code
            </button>
            <button @click="activeTab = 'manual'" 
                    :class="activeTab === 'manual' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 px-6 py-4 text-sm font-medium transition-colors">
                <i class="fas fa-keyboard mr-2"></i>
                Input Manual
            </button>
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="bg-white rounded-b-2xl shadow-sm border border-gray-200 border-t-0 p-6 min-h-[400px]">
        
        {{-- Tab 1: QR Scanner --}}
        <div x-show="activeTab === 'scan'" x-transition class="space-y-6">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                    <i class="fas fa-qrcode text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Scan QR Code Tiket</h2>
                <p class="text-sm text-gray-600 mb-6">Arahkan kamera ke QR Code pada tiket parkir</p>
            </div>

            {{-- QR Scanner Container --}}
            <div id="qr-reader" class="w-full max-w-md mx-auto rounded-lg overflow-hidden border-2 border-gray-300" 
                 x-show="!scanSuccess" style="display: none;"></div>

            {{-- Scanner Controls --}}
            <div class="flex flex-col items-center gap-3" x-show="!scanSuccess">
                <button type="button" @click="startScanner()" x-show="!scanning"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-camera"></i>
                    <span>Aktifkan Scanner</span>
                </button>
                
                <button type="button" @click="stopScanner()" x-show="scanning"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-stop"></i>
                    <span>Hentikan Scanner</span>
                </button>

                <div x-show="scanning" class="text-sm text-gray-600">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Menunggu QR Code...
                </div>
            </div>

            {{-- Scanner Error Message --}}
            <div x-show="scanError" class="max-w-md mx-auto p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-red-800">Gagal mengaktifkan kamera</p>
                        <p class="text-xs text-red-600 mt-1" x-text="scanErrorMessage"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 2: Manual Input --}}
        <div x-show="activeTab === 'manual'" x-transition class="max-w-xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-keyboard text-gray-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Input Kode Manual</h2>
                <p class="text-sm text-gray-600">Masukkan kode unik dari tiket parkir</p>
            </div>

            <form @submit.prevent="checkCode()" class="space-y-6">
                <div>
                    <label for="manual_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Unik Tiket
                    </label>
                    <input type="text" 
                           id="manual_code" 
                           x-model="manualCode"
                           placeholder="PARK-YYYYMMDD-XXXX"
                           required
                           class="w-full px-4 py-3 text-lg font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                           :disabled="checkingCode">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="manualCode = ''" 
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Reset
                    </button>
                    <button type="submit"
                            :disabled="checkingCode || !manualCode"
                            class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition-colors">
                        <span x-show="!checkingCode">Cek Kendaraan</span>
                        <span x-show="checkingCode" class="inline-flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            Memeriksa...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab 3: Confirmation Form (Hidden by default) --}}
        <div x-show="activeTab === 'form'" x-transition class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Konfirmasi Keluar Parkir</h2>
                <p class="text-sm text-gray-600">Periksa data kendaraan sebelum memproses</p>
            </div>

            <form method="POST" action="{{ route('parking.exit.store') }}" @submit="loading = true">
                @csrf
                <input type="hidden" name="unique_code" x-model="transactionData.unique_code">

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 space-y-4 mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Kode Unik
                            </label>
                            <p class="text-lg font-semibold text-gray-900 font-mono" x-text="transactionData.unique_code"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Nomor Polisi
                            </label>
                            <p class="text-lg font-semibold text-gray-900" x-text="transactionData.license_plate"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Jenis Kendaraan
                            </label>
                            <p class="text-lg font-semibold text-gray-900 capitalize" x-text="transactionData.vehicle_type"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Waktu Masuk
                            </label>
                            <p class="text-lg font-semibold text-gray-900" x-text="transactionData.entry_time"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="resetForm()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="loading"
                            class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 disabled:bg-green-300 disabled:cursor-not-allowed transition-colors shadow-lg">
                        <span x-show="!loading">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Proses Keluar & Hitung Biaya
                        </span>
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function exitPage() {
    return {
        activeTab: 'scan',
        scanning: false,
        scanSuccess: false,
        scanError: false,
        scanErrorMessage: '',
        manualCode: '',
        checkingCode: false,
        loading: false,
        transactionData: {},
        html5QrCode: null,

        init() {
            console.log('Exit page initialized');
        },

        startScanner() {
            const qrReader = document.getElementById('qr-reader');
            qrReader.style.display = 'block';
            
            this.html5QrCode = new Html5Qrcode("qr-reader");
            this.scanning = true;
            this.scanError = false;
            
            this.html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText) => {
                    this.onScanSuccess(decodedText);
                },
                (error) => {
                    // Silent error for scanning attempts
                }
            ).catch((err) => {
                this.scanError = true;
                this.scanErrorMessage = 'Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.';
                this.scanning = false;
                console.error('QR Scanner error:', err);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuka Kamera',
                    text: 'Gunakan input manual untuk melanjutkan.',
                    confirmButtonColor: '#3B82F6'
                });
            });
        },

        stopScanner() {
            if (this.html5QrCode) {
                this.html5QrCode.stop().then(() => {
                    this.scanning = false;
                    document.getElementById('qr-reader').style.display = 'none';
                }).catch(err => console.error('Stop scanner error:', err));
            }
        },

        async onScanSuccess(code) {
            this.stopScanner();
            this.scanSuccess = true;
            
            // Check transaction
            await this.fetchTransaction(code);
        },

        async checkCode() {
            if (!this.manualCode) return;
            
            this.checkingCode = true;
            await this.fetchTransaction(this.manualCode);
            this.checkingCode = false;
        },

        async fetchTransaction(code) {
            try {
                const response = await fetch(`{{ route('parking.check') }}?code=${encodeURIComponent(code)}`);
                const data = await response.json();
                
                if (response.ok) {
                    this.transactionData = {
                        unique_code: data.unique_code,
                        license_plate: data.plate_number || data.license_plate,
                        vehicle_type: data.vehicle_type,
                        entry_time: new Date(data.entry_time).toLocaleString('id-ID', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })
                    };
                    
                    // Switch to form tab
                    this.activeTab = 'form';
                    
                    // Success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Kendaraan Ditemukan',
                        text: `${this.transactionData.license_plate} - ${this.transactionData.vehicle_type}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Transaksi tidak ditemukan');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kode Tidak Valid',
                    text: error.message || 'Kode tiket tidak ditemukan atau sudah diproses',
                    confirmButtonColor: '#3B82F6'
                });
                
                this.scanSuccess = false;
            }
        },

        resetForm() {
            this.activeTab = 'scan';
            this.scanSuccess = false;
            this.manualCode = '';
            this.transactionData = {};
            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection
