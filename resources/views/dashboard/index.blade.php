@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="dashboardFilter()">
    {{-- Stats cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                    <i class="fas fa-car text-blue-600"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Kendaraan Parkir</p>
                <p class="text-3xl font-bold text-gray-900" x-text="stats.parked" x-show="!loading">{{ $stats['parked'] ?? 0 }}</p>
                <div x-show="loading" class="h-9 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900" x-text="formatCurrency(stats.revenue_today)" x-show="!loading">Rp {{ number_format($stats['revenue_today'] ?? 0, 0, ',', '.') }}</p>
                <div x-show="loading" class="h-9 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                    <i class="fas fa-chart-line text-indigo-600"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Pendapatan Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-900" x-text="formatCurrency(stats.revenue_month)" x-show="!loading">Rp {{ number_format($stats['revenue_month'] ?? 0, 0, ',', '.') }}</p>
                <div x-show="loading" class="h-9 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Transaksi Selesai</p>
                <p class="text-3xl font-bold text-gray-900" x-text="completedCount" x-show="!loading">{{ $completedCount ?? 0 }}</p>
                <div x-show="loading" class="h-9 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- Filter toggle --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3">
        <button type="button" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-700" @click="filterOpen = !filterOpen">
            <i class="fas fa-filter text-xs"></i>
            <span>Filter Dashboard</span>
            <i class="fas fa-chevron-down text-xs" x-show="!filterOpen"></i>
            <i class="fas fa-chevron-up text-xs" x-show="filterOpen"></i>
        </button>
        <div x-show="filterOpen" x-transition class="mt-4 border-t border-gray-100 pt-4 space-y-3 text-sm">
            <form @submit.prevent="applyFilters()" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rentang Tanggal</label>
                    <div class="flex gap-2">
                        <input type="date" x-model="filters.from" class="w-1/2 rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
                        <input type="date" x-model="filters.to" class="w-1/2 rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Kendaraan</label>
                    <select x-model="filters.vehicle_type" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="motor">Motor</option>
                        <option value="mobil">Mobil</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select x-model="filters.status" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="parked">Parkir</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                <div class="flex items-end justify-end gap-2">
                    <button type="submit" :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-blue-300 disabled:cursor-not-allowed">
                        <i class="fas fa-filter" x-show="!loading"></i>
                        <i class="fas fa-spinner fa-spin" x-show="loading"></i>
                        <span>Terapkan</span>
                    </button>
                    <button type="button" @click="resetFilters()" :disabled="loading"
                            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-show="!loading">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Pendapatan 7 Hari Terakhir</h3>
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-show="loading">
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-48 bg-gray-200 rounded"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-show="!loading">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Komposisi Kendaraan</h3>
            <canvas id="vehiclePieChart"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-show="loading">
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-48 bg-gray-200 rounded"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-show="!loading">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Transaksi per Jam</h3>
            <canvas id="hourlyBarChart"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" x-show="loading">
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-48 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    {{-- Recent transactions table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-4">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Transaksi Terbaru</h3>
        </div>
        @include('components.table', [
            'headers' => ['Kode', 'Plat', 'Jenis', 'Masuk', 'Keluar', 'Durasi (jam)', 'Biaya', 'Status'],
            'sortableColumns' => ['unique_code', 'license_plate', 'vehicle_type', 'entry_time', 'exit_time', 'duration_hours', 'parking_fee', 'status'],
            'rowsView' => 'dashboard.partials.transaction-rows',
            'collection' => $transactions ?? collect(),
        ])
        @if(isset($transactions))
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardFilter() {
    return {
        filterOpen: false,
        loading: false,
        filters: {
            from: '{{ request('from') }}',
            to: '{{ request('to') }}',
            vehicle_type: '{{ request('vehicle_type') }}',
            status: '{{ request('status') }}'
        },
        stats: {
            parked: {{ $stats['parked'] ?? 0 }},
            revenue_today: {{ $stats['revenue_today'] ?? 0 }},
            revenue_month: {{ $stats['revenue_month'] ?? 0 }}
        },
        completedCount: {{ $completedCount ?? 0 }},
        charts: {},

        init() {
            this.loadCharts(this.filters);
        },

        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        },

        async applyFilters() {
            this.loading = true;
            
            try {
                // Load charts with filters
                await this.loadCharts(this.filters);
                
                // Load stats and update UI
                const params = new URLSearchParams();
                for (const [key, value] of Object.entries(this.filters)) {
                    if (value) params.append(key, value);
                }
                
                const response = await fetch('{{ route('dashboard') }}?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.stats = data.stats;
                    this.completedCount = data.completedCount;
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Filter Diterapkan',
                            text: 'Dashboard berhasil diperbarui',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    
                    // Reload page to update table
                    setTimeout(() => {
                        window.location.href = '{{ route('dashboard') }}?' + params.toString();
                    }, 1000);
                }
            } catch (error) {
                console.error('Failed to apply filters:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memfilter data'
                    });
                }
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters = {
                from: '',
                to: '',
                vehicle_type: '',
                status: ''
            };
            window.location.href = '{{ route('dashboard') }}';
        },

        async loadCharts(params = {}) {
            const queryString = new URLSearchParams(params).toString();
            const url = "{{ route('dashboard.charts') }}" + (queryString ? '?' + queryString : '');
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                this.initCharts(data);
            } catch (err) {
                console.error('Failed to load charts:', err);
            }
        },

        initCharts(data) {
            if (typeof window.Chart === 'undefined') {
                console.error('Chart.js not loaded');
                return;
            }

            // Destroy existing charts
            Object.values(this.charts).forEach(chart => chart?.destroy());
            this.charts = {};

            // Revenue Line Chart
            const ctxRevenue = document.getElementById('revenueChart');
            if (ctxRevenue) {
                this.charts.revenue = new window.Chart(ctxRevenue, {
                    type: 'line',
                    data: {
                        labels: data.last7.map(i => i.date),
                        datasets: [{
                            label: 'Pendapatan',
                            data: data.last7.map(i => i.revenue),
                            borderColor: 'rgb(37 99 235)',
                            backgroundColor: 'rgba(37,99,235,0.1)',
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { 
                            legend: { display: false } 
                        },
                        scales: { 
                            y: { 
                                ticks: { 
                                    callback: v => 'Rp ' + v.toLocaleString('id-ID') 
                                } 
                            } 
                        }
                    }
                });
            }

            // Vehicle Pie Chart
            const ctxPie = document.getElementById('vehiclePieChart');
            if (ctxPie) {
                this.charts.pie = new window.Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: ['Motor', 'Mobil'],
                        datasets: [{
                            data: [data.vehicleSplit.motor || 0, data.vehicleSplit.mobil || 0],
                            backgroundColor: ['rgb(59 130 246)', 'rgb(16 185 129)'],
                        }]
                    },
                    options: { 
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            }

            // Hourly Bar Chart
            const ctxBar = document.getElementById('hourlyBarChart');
            if (ctxBar) {
                this.charts.bar = new window.Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: data.perHour.map(i => i.hour + ':00'),
                        datasets: [{
                            label: 'Transaksi',
                            data: data.perHour.map(i => i.total),
                            backgroundColor: 'rgb(129 140 248)'
                        }]
                    },
                    options: { 
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } } 
                    }
                });
            }
        }
    }
}
</script>
@endpush
