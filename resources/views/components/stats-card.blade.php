@props(['title' => '', 'value' => 0, 'icon' => 'info', 'color' => 'blue'])
@php
    $colors = [
        'blue' => 'bg-blue-100 text-blue-700',
        'green' => 'bg-green-100 text-green-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'red' => 'bg-red-100 text-red-700',
    ];
    $colorClass = $colors[$color] ?? $colors['blue'];
    $formatted = is_numeric($value) ? number_format($value, 0, ',', '.') : $value;
@endphp
<div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
    <div>
        <p class="text-xs font-medium text-gray-500">{{ $title }}</p>
        <p class="mt-1 text-xl font-semibold text-gray-900">{{ $formatted }}</p>
    </div>
    <div class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $colorClass }}">
        @switch($icon)
            @case('car')
                <i class="fas fa-car"></i>
                @break
            @case('cash')
                <i class="fas fa-money-bill-wave"></i>
                @break
            @case('chart')
                <i class="fas fa-chart-bar"></i>
                @break
            @case('check')
                <i class="fas fa-check-circle"></i>
                @break
            @case('list')
                <i class="fas fa-list"></i>
                @break
            @default
                <i class="fas fa-info-circle"></i>
        @endswitch
    </div>
</div>
