{{-- Main application layout for authenticated pages --}}
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Parking System') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full antialiased text-gray-900" x-data="{ sidebarOpen: false, userMenuOpen: false }">
<div class="min-h-screen flex bg-gray-100">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out md:static md:translate-x-0" :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white font-bold">P</span>
                <span class="font-semibold text-gray-800">Parking System</span>
            </a>
            <button class="md:hidden text-gray-500 hover:text-gray-700" @click="sidebarOpen = false">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="px-3 py-4 space-y-1 text-sm">
            {{-- Common navigation --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('dashboard')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>

            {{-- User parking navigation --}}
            @role('user')
            <p class="mt-4 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Transaksi Parkir</p>
            <a href="{{ route('parking.entry.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('parking.entry.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-sign-in-alt w-5"></i>
                <span>Masuk Parkir</span>
            </a>
            <a href="{{ route('parking.exit.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('parking.exit.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Keluar Parkir</span>
            </a>
            <!-- <a href="{{ route('parking.exit.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700">
                <i class="fas fa-qrcode w-5"></i>
                <span>Scan QR Keluar</span>
            </a> -->
            @endrole

            {{-- Admin navigation --}}
            @role('admin')
            <p class="mt-4 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Admin</p>
            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('reports.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-file-alt w-5"></i>
                <span>Laporan</span>
            </a>
            <a href="{{ route('activity.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('activity.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-history w-5"></i>
                <span>Activity Log</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('admin.users.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-users w-5"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('settings.parking-rates.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('settings.parking-rates.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-cog w-5"></i>
                <span>Pengaturan Tarif</span>
            </a>
            @endrole
            
            {{-- User Settings --}}
            <p class="mt-4 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Akun</p>
            <a href="{{ route('profile.security') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 @if(request()->routeIs('profile.*')) bg-blue-50 text-blue-700 font-medium @endif">
                <i class="fas fa-shield-alt w-5"></i>
                <span>Keamanan</span>
                @if(auth()->user()->is_2fa_enabled)
                    <span class="ml-auto">
                        <i class="fas fa-check-circle text-green-500 text-xs"></i>
                    </span>
                @endif
            </a>
        </nav>
        <div class="mt-auto px-4 py-4 text-xs text-gray-400 border-t border-gray-200 hidden md:block">
            &copy; {{ date('Y') }} Parking System. All rights reserved.
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen">
        {{-- Header --}}
        <header class="sticky top-0 z-20 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-3 md:px-6">
                <div class="flex items-center gap-2">
                    <button class="md:hidden text-gray-600" @click="sidebarOpen = true">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-4">
                    {{-- Notifications placeholder for SweetAlert2 usage --}}
                    <div></div>

                    @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm hover:bg-gray-50">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                            </span>
                            <div class="hidden text-left md:block">
                                <div class="text-xs text-gray-400">{{ auth()->user()->email }}</div>
                                <div class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg text-sm py-1 z-30">
                            <div class="px-3 py-2 border-b border-gray-100 text-xs text-gray-500">
                                Masuk sebagai<br><span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                            </div>
                            <!-- <a href="{{ route('dashboard') }}" class="block px-3 py-2 hover:bg-gray-50">Dashboard</a> -->
                            <a href="{{ route('logout') }}" class="block px-3 py-2 text-red-600 hover:bg-red-50" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <main class="flex-1 px-4 py-4 md:px-6 md:py-6">
            @yield('content')
        </main>

        <footer class="md:hidden border-t border-gray-200 bg-white px-4 py-3 text-xs text-gray-400 text-center">
            &copy; {{ date('Y') }} Parking System.
        </footer>
    </div>
</div>

@stack('scripts')

@if(session('success') || session('error') || session('warning') || session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
            if (window.toast) {
                window.toast({
                    icon: 'success',
                    title: @json(session('success')),
                });
            }
            @endif

            @if(session('error'))
            if (window.toast) {
                window.toast({
                    icon: 'error',
                    title: @json(session('error')),
                });
            }
            @endif

            @if(session('warning'))
            if (window.toast) {
                window.toast({
                    icon: 'warning',
                    title: @json(session('warning')),
                });
            }
            @endif

            @if(session('info'))
            if (window.toast) {
                window.toast({
                    icon: 'info',
                    title: @json(session('info')),
                });
            }
            @endif
        });
    </script>
@endif
</body>
</html>
