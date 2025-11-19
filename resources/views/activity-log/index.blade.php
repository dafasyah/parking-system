@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
<div class="space-y-6">
    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ open: true }">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-800">Filter Aktivitas</h2>
            <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="open = !open">
                <span x-show="open">Sembunyikan</span>
                <span x-show="!open">Tampilkan</span>
            </button>
        </div>
        <form method="GET" action="{{ route('activity.index') }}" x-show="open" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-medium text-gray-600 mb-1">Log Name</label>
                <select name="log_name" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="user" @selected(request('log_name')=='user')>User</option>
                    <option value="parking" @selected(request('log_name')=='parking')>Parking</option>
                </select>
            </div>
            <div>
                <label class="block font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-lg border border-gray-300 px-2 py-1.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="flex items-end justify-end gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Terapkan</button>
                <a href="{{ route('activity.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-px bg-gray-200"></div>
            <ul class="space-y-4">
                @forelse($activities as $activity)
                    <li class="relative pl-10">
                        <div class="absolute left-1 top-1.5 h-6 w-6 rounded-full flex items-center justify-center text-xs font-semibold
                            @switch($activity->log_name)
                                @case('user') bg-blue-100 text-blue-700 @break
                                @case('parking') bg-emerald-100 text-emerald-700 @break
                                @default bg-gray-100 text-gray-700
                            @endswitch">
                            @switch($activity->log_name)
                                @case('user') U @break
                                @case('parking') P @break
                                @default L
                            @endswitch
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-1">
                            <div>
                                <p class="text-xs font-semibold text-gray-800">{{ $activity->description }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    @if($activity->causer)
                                        oleh <span class="font-medium text-gray-700">{{ $activity->causer->name }}</span>
                                    @else
                                        sistem
                                    @endif
                                    @if($activity->subject)
                                        pada <span class="font-mono text-gray-700">{{ class_basename($activity->subject_type) }}#{{ $activity->subject_id }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-[11px] text-right text-gray-400">
                                <p>{{ $activity->created_at->format('d M Y H:i:s') }}</p>
                                @if(isset($activity->properties['ip']))
                                    <p>IP: {{ $activity->properties['ip'] }}</p>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-xs text-gray-400 text-center">Belum ada aktivitas tercatat.</li>
                @endforelse
            </ul>
        </div>

        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection
