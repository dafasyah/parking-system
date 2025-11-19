@props(['type' => 'primary', 'size' => 'md', 'loading' => false])
@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70 transition-all duration-200';
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-blue-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-sm',
    ];
    $classes = $base . ' ' . ($variants[$type] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp
<button {{ $attributes->merge(['class' => $classes]) }} @if($loading) disabled @endif>
    @if($loading)
        <i class="fas fa-spinner fa-spin"></i>
    @endif
    <span>{{ $slot }}</span>
</button>
