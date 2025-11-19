@props(['id', 'title'])
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 p-4']) }}>
    <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $title }}</h3>
    <canvas id="{{ $id }}" class="w-full h-52"></canvas>
</div>
