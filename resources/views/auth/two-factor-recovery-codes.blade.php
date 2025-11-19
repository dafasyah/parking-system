@extends('layouts.guest')

@section('title', 'Recovery Codes')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-emerald-100 px-4 py-12">
    <div class="max-w-2xl w-full">
        {{-- Success Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-600 mb-4 animate-bounce">
                <i class="fas fa-check-circle text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">2FA Berhasil Diaktifkan!</h1>
            <p class="text-gray-600">Akun Anda sekarang lebih aman dengan autentikasi dua faktor</p>
        </div>

        {{-- Recovery Codes Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-key text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Kode Recovery Anda</h2>
                    <p class="text-sm text-gray-600">Simpan kode-kode ini dengan aman!</p>
                </div>
            </div>

            {{-- Warning Banner --}}
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                    <div class="text-sm text-red-800">
                        <p class="font-semibold mb-1">⚠️ Sangat Penting!</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Simpan kode-kode ini di tempat yang aman (password manager, brankas, dsb)</li>
                            <li>Kode recovery ini adalah satu-satunya cara untuk akses akun jika smartphone hilang</li>
                            <li>Setiap kode hanya bisa digunakan sekali</li>
                            <li>Jangan bagikan kode ini kepada siapapun</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Recovery Codes Grid --}}
            <div class="grid grid-cols-2 gap-3 mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                @foreach($recoveryCodes as $code)
                    <div class="flex items-center gap-2 p-3 bg-white border border-gray-300 rounded-lg hover:border-blue-500 transition-colors">
                        <i class="fas fa-shield-alt text-gray-400 text-xs"></i>
                        <code class="flex-1 text-sm font-mono font-semibold text-gray-900 select-all">{{ $code }}</code>
                        <button type="button" 
                                onclick="copyCode('{{ $code }}')"
                                class="text-gray-400 hover:text-blue-600 transition-colors"
                                title="Copy">
                            <i class="fas fa-copy text-xs"></i>
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button"
                        onclick="printCodes()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-print"></i>
                    <span>Print Kode</span>
                </button>
                <button type="button"
                        onclick="downloadCodes()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-download"></i>
                    <span>Download TXT</span>
                </button>
                <button type="button"
                        onclick="copyAllCodes()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                    <i class="fas fa-clipboard"></i>
                    <span>Copy Semua</span>
                </button>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Cara Menggunakan Kode Recovery</p>
                    <p class="text-xs">
                        Jika Anda kehilangan akses ke aplikasi authenticator, gunakan salah satu kode recovery ini 
                        di halaman verifikasi 2FA dengan memilih opsi "Gunakan Kode Recovery".
                    </p>
                </div>
            </div>
        </div>

        {{-- Continue Button --}}
        <div class="text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                <i class="fas fa-check"></i>
                <span>Lanjutkan ke Dashboard</span>
            </a>
        </div>
    </div>
</div>

{{-- Hidden Print Area --}}
<div id="printArea" class="hidden print:block">
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-2">{{ config('app.name') }} - Recovery Codes</h1>
        <p class="text-sm text-gray-600 mb-6">Akun: {{ Auth::user()->email }}</p>
        <p class="text-sm mb-4">Simpan kode-kode ini dengan aman. Setiap kode hanya bisa digunakan sekali.</p>
        <div class="grid grid-cols-2 gap-3">
            @foreach($recoveryCodes as $code)
                <div class="p-2 border border-gray-300 rounded">
                    <code class="text-lg font-mono font-semibold">{{ $code }}</code>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-6">Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>
</div>

@push('scripts')
<script>
    const recoveryCodes = @json($recoveryCodes);
    
    function copyCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Kode berhasil disalin!',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
    
    function copyAllCodes() {
        const allCodes = recoveryCodes.join('\n');
        navigator.clipboard.writeText(allCodes).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Semua Kode Disalin',
                text: 'Semua recovery codes telah disalin ke clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }
    
    function printCodes() {
        window.print();
    }
    
    function downloadCodes() {
        const content = `{{ config('app.name') }} - Recovery Codes\n` +
                       `Akun: {{ Auth::user()->email }}\n` +
                       `Tanggal: {{ now()->format('d M Y, H:i') }} WIB\n\n` +
                       `SIMPAN KODE-KODE INI DENGAN AMAN!\n` +
                       `Setiap kode hanya bisa digunakan sekali.\n\n` +
                       recoveryCodes.join('\n');
        
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '2fa-recovery-codes-{{ Auth::user()->id }}.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        Swal.fire({
            icon: 'success',
            title: 'File Berhasil Didownload',
            text: 'Recovery codes telah disimpan sebagai file TXT',
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printArea, #printArea * {
            visibility: visible;
        }
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
@endpush
@endsection
