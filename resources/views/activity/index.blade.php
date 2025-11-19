@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
<div class="space-y-6">
    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <form method="GET" action="{{ route('activity.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Aktivitas</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..." 
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                </div>
                
                {{-- User Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Log Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Aktivitas</label>
                    <select name="log_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        @foreach($logNames as $logName)
                            <option value="{{ $logName }}" @selected(request('log_name') == $logName)>{{ ucfirst($logName) }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rentang Tanggal</label>
                    <div class="flex gap-2">
                        <input type="date" name="from" value="{{ request('from') }}" class="w-1/2 rounded-lg border border-gray-300 px-2 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                        <input type="date" name="to" value="{{ request('to') }}" class="w-1/2 rounded-lg border border-gray-300 px-2 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2 justify-end">
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="{{ route('activity.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Activity Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Timeline Aktivitas</h3>
        
        <div class="space-y-4">
            @forelse($activities as $activity)
                <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center
                            @switch($activity->log_name)
                                @case('parking') bg-blue-100 text-blue-600 @break
                                @case('default') bg-gray-100 text-gray-600 @break
                                @default bg-green-100 text-green-600
                            @endswitch">
                            @switch($activity->log_name)
                                @case('parking')
                                    <i class="fas fa-car"></i>
                                    @break
                                @case('default')
                                    <i class="fas fa-info-circle"></i>
                                    @break
                                @default
                                    <i class="fas fa-check-circle"></i>
                            @endswitch
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $activity->causer ? $activity->causer->name : 'System' }}
                                    </span>
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                    @if($activity->properties->has('ip'))
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-network-wired mr-1"></i>
                                            {{ $activity->properties['ip'] }}
                                        </span>
                                    @endif
                                    @if($activity->log_name)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($activity->log_name) }}
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Additional properties --}}
                                @if($activity->properties->count() > 1)
                                    <div class="mt-2 text-xs text-gray-600" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-blue-600 hover:text-blue-800">
                                            <span x-show="!open">Lihat Detail</span>
                                            <span x-show="open">Sembunyikan</span>
                                        </button>
                                        <div x-show="open" x-transition class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                                            <pre class="text-xs overflow-x-auto">{{ json_encode($activity->properties->except(['ip']), JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-shrink-0 text-right">
                                <p class="text-xs text-gray-500">{{ $activity->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $activity->created_at->format('H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Tidak ada activity log</p>
                </div>
            @endforelse
        </div>
        
        @if($activities->hasPages())
            <div class="mt-6 pt-4 border-t border-gray-100">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
