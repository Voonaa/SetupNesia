<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SetupNesia') }} - Admin Panel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100 bg-[#0F172A] min-h-screen relative overflow-x-hidden">
        <!-- Ambient Background Glows -->
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-600/5 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/5 blur-[120px] pointer-events-none"></div>

        <div class="flex min-h-screen relative z-10">
            <!-- Sidebar -->
            <aside class="w-64 bg-[#1E293B]/60 backdrop-blur-xl border-r border-slate-800 shrink-0 hidden md:block flex flex-col justify-between">
                <div>
                    <!-- Logo Header -->
                    <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-[#1E293B]/40">
                        <x-application-logo />
                    </div>

                    <!-- Nav Links -->
                    <nav class="mt-6 px-4 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-purple-600/20 to-blue-600/10 text-purple-400 border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                            <svg class="mr-3 h-5 w-5 transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.categories.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-purple-600/20 to-blue-600/10 text-purple-400 border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                            <svg class="mr-3 h-5 w-5 transition duration-150 {{ request()->routeIs('admin.categories.*') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Categories
                        </a>

                        <a href="{{ route('admin.products.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-purple-600/20 to-blue-600/10 text-purple-400 border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                            <svg class="mr-3 h-5 w-5 transition duration-150 {{ request()->routeIs('admin.products.*') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Products
                        </a>

                        <a href="{{ route('admin.orders.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-purple-600/20 to-blue-600/10 text-purple-400 border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                            <svg class="mr-3 h-5 w-5 transition duration-150 {{ request()->routeIs('admin.orders.*') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Orders
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-purple-600/20 to-blue-600/10 text-purple-400 border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                            <svg class="mr-3 h-5 w-5 transition duration-150 {{ request()->routeIs('admin.users.*') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </a>

                        <a href="{{ route('admin.reports.index') }}" class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition duration-150 {{ request()->routeIs('admin.reports.*') ? 'bg-gradient-to-r from-purple-600/20 to-blue-600/10 text-purple-400 border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                            <svg class="mr-3 h-5 w-5 transition duration-150 {{ request()->routeIs('admin.reports.*') ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            Reports
                        </a>
                    </nav>
                </div>

                <!-- Footer / Logout -->
                <div class="p-4 border-t border-slate-800">
                    <a href="/" class="group flex items-center px-4 py-2.5 text-xs font-semibold rounded-lg text-slate-400 hover:bg-slate-800/50 hover:text-slate-100 mb-2 transition duration-150">
                        <svg class="mr-2 h-4 w-4 text-slate-500 group-hover:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Main Shop
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full group flex items-center px-4 py-2.5 text-xs font-semibold rounded-lg text-red-400 hover:bg-red-950/20 hover:text-red-300 transition duration-150 cursor-pointer">
                            <svg class="mr-2 h-4 w-4 text-red-500 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Panel -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                <!-- Top Nav -->
                <header class="h-16 border-b border-slate-800 bg-[#1E293B]/40 backdrop-blur-xl flex items-center justify-between px-6 sticky top-0 z-30">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile toggle menu -->
                        <button class="text-slate-400 hover:text-slate-100 md:hidden">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                            Admin Portal
                        </h1>
                    </div>

                    <!-- User menu -->
                    <div class="flex items-center space-x-4">
                        <div class="text-right hidden sm:block">
                            <span class="block text-sm font-bold text-slate-200">{{ Auth::user()->name }}</span>
                            <span class="block text-xs text-purple-400 font-semibold uppercase tracking-wider">{{ Auth::user()->role }}</span>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center font-bold text-white shadow-md shadow-purple-500/20 border border-slate-700">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6 md:p-8 overflow-y-auto">
                    <!-- Notification Banners -->
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-emerald-950/30 border border-emerald-500/30 text-emerald-400 rounded-xl flex items-center shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 bottom-0 left-0 w-[4px] bg-emerald-500"></div>
                            <svg class="h-5 w-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-950/30 border border-red-500/30 text-red-400 rounded-xl flex items-center shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 bottom-0 left-0 w-[4px] bg-red-500"></div>
                            <svg class="h-5 w-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                    @endif

                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
