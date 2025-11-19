@props(['show' => false])
<div x-data="{ open: {{ $show ? 'true' : 'false' }} }" x-show="open" x-cloak class="fixed inset-0 z-40 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative z-50 w-full max-w-lg rounded-2xl bg-white shadow-xl border border-gray-200 mx-4" x-transition>
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">{{ $title ?? '' }}</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" @click="open = false">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="px-4 py-3 text-sm text-gray-700">
            {{ $slot }}
        </div>
        <div class="px-4 py-3 border-t border-gray-100 flex justify-end gap-2 text-xs">
            {{ $footer ?? '' }}
        </div>
    </div>
</div>
