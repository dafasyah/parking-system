@extends('layouts.app')

@section('title', 'Laporan Parkir')
@section('page-title', 'Laporan Parkir')

@section('content')
<div class="space-y-6">
    {{-- Filter form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ open: true }">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-800">Filter Laporan</h2>
            <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="open = !open">
                <span x-show="open">Sembunyikan</span>
                <span x-show="!open">Tampilkan</span>
            </button>
        </div>
        <form method="GET" action="{{ route('reports.index') }}" x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block font-medium text-gray-600 mb-1">Jenis Kendaraan</label>
                <div class="space-y-1">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="vehicle_type[]" value="motor" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(collect(request('vehicle_type'))->contains('motor'))>
                        <span>Motor</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="vehicle_type[]" value="mobil" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(collect(request('vehicle_type'))->contains('mobil'))>
                        <span>Mobil</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="parked" @selected(request('status')=='parked')>Parkir</option>
                    <option value="completed" @selected(request('status')=='completed')>Selesai</option>
                </select>
                <div class="flex justify-end gap-2 mt-3">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Terapkan</button>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @include('components.stats-card', ['title' => 'Total Transaksi', 'value' => $summary['count'] ?? 0, 'icon' => 'list', 'color' => 'blue'])
        @include('components.stats-card', ['title' => 'Total Pendapatan', 'value' => $summary['total'] ?? 0, 'icon' => 'cash', 'color' => 'green'])
        @include('components.stats-card', ['title' => 'Rata-rata', 'value' => $summary['average'] ?? 0, 'icon' => 'chart', 'color' => 'indigo'])
    </div>

    {{-- Export buttons --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
        <p class="text-gray-500">Export laporan berdasarkan filter aktif.</p>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('reports.export') }}">
                @csrf
                <input type="hidden" name="format" value="excel">
                @if(request('from'))
                    <input type="hidden" name="from" value="{{ request('from') }}">
                @endif
                @if(request('to'))
                    <input type="hidden" name="to" value="{{ request('to') }}">
                @endif
                @if(request('vehicle_type'))
                    @foreach((array)request('vehicle_type') as $vtype)
                        <input type="hidden" name="vehicle_type[]" value="{{ $vtype }}">
                    @endforeach
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </button>
            </form>
            <form method="POST" action="{{ route('reports.export') }}">
                @csrf
                <input type="hidden" name="format" value="pdf">
                @if(request('from'))
                    <input type="hidden" name="from" value="{{ request('from') }}">
                @endif
                @if(request('to'))
                    <input type="hidden" name="to" value="{{ request('to') }}">
                @endif
                @if(request('vehicle_type'))
                    @foreach((array)request('vehicle_type') as $vtype)
                        <input type="hidden" name="vehicle_type[]" value="{{ $vtype }}">
                    @endforeach
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @include('components.table', [
            'headers' => ['Kode', 'Plat', 'Jenis', 'Masuk', 'Keluar', 'Durasi', 'Biaya', 'Status'],
            'sortableColumns' => ['unique_code', 'license_plate', 'vehicle_type', 'entry_time', 'exit_time', 'duration_hours', 'parking_fee', 'status'],
            'rowsView' => 'reports.partials.rows',
            'collection' => $transactions,
        ])
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
