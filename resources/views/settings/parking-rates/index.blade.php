@extends('layouts.app')

@section('title', 'Pengaturan Tarif Parkir')
@section('page-title', 'Pengaturan Tarif Parkir')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pengaturan Tarif Parkir</h1>
        <p class="text-sm text-gray-600">Kelola tarif parkir per jam untuk setiap jenis kendaraan</p>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Rates Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Tarif</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-info-circle mr-1.5"></i>
                    {{ $rates->count() }} Jenis Kendaraan
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenis Kendaraan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarif Per Jam
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Terakhir Diperbarui
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rates as $rate)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg 
                                                {{ $rate->vehicle_type === 'motor' ? 'bg-blue-100' : 'bg-green-100' }}">
                                        <i class="fas {{ $rate->vehicle_type === 'motor' ? 'fa-motorcycle' : 'fa-car' }} 
                                                  {{ $rate->vehicle_type === 'motor' ? 'text-blue-600' : 'text-green-600' }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 capitalize">{{ $rate->vehicle_type }}</div>
                                        <div class="text-xs text-gray-500">{{ $rate->vehicle_type === 'motor' ? 'Sepeda Motor' : 'Mobil' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-gray-900">
                                    Rp {{ number_format($rate->hourly_rate, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">per jam</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $rate->updated_at->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $rate->updated_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('settings.parking-rates.edit', $rate->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-exclamation-circle text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-sm text-gray-600">Belum ada tarif parkir yang diatur</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Informasi Penting</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>Tarif parkir dihitung per jam dengan pembulatan ke atas</li>
                    <li>Perubahan tarif akan berlaku untuk transaksi baru</li>
                    <li>Transaksi yang sudah berjalan tidak terpengaruh perubahan tarif</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
