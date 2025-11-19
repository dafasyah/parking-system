{{-- Auth layout for login & 2FA pages --}}
<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-blue-600 via-indigo-600 to-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Parking System') }} - @yield('title', 'Masuk')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full antialiased text-gray-900">
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center text-white">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 border border-white/20 mb-3">
                <span class="text-2xl font-bold">P</span>
            </div>
            <h1 class="text-2xl font-semibold leading-tight">Parking System</h1>
            <p class="text-sm text-white/80 mt-1">Manajemen parkir modern, aman, dan real-time.</p>
        </div>

        <div class="bg-white/95 backdrop-blur shadow-xl rounded-2xl px-6 py-6 md:px-8 md:py-8">
            @yield('content')
        </div>

        <p class="mt-6 text-center text-xs text-white/70">&copy; {{ date('Y') }} Parking System. Semua hak dilindungi.</p>
    </div>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
