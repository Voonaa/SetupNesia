<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'SetupNesia - Premium Mechanical Keyboards & Workspace Accessories')</title>
        <meta name="description" content="@yield('meta_description', 'Discover premium custom mechanical keyboards, high-fidelity keycaps, workspace deskmats, mouse, and cable management accessories to level up your setup at SetupNesia.')">
        <meta name="keywords" content="mechanical keyboard, custom keyboard, keycaps, deskmat, workspace accessories, programmer setup, gaming gear, setupnesia">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', 'SetupNesia - Premium Mechanical Keyboards & Workspace Accessories')">
        <meta property="og:description" content="@yield('meta_description', 'Discover premium custom mechanical keyboards, high-fidelity keycaps, workspace deskmats, mouse, and cable management accessories to level up your setup at SetupNesia.')">
        <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="@yield('title', 'SetupNesia - Premium Mechanical Keyboards & Workspace Accessories')">
        <meta name="twitter:description" content="@yield('meta_description', 'Discover premium custom mechanical keyboards, high-fidelity keycaps, workspace deskmats, mouse, and cable management accessories to level up your setup at SetupNesia.')">
        <meta name="twitter:image" content="@yield('og_image', asset('images/logo.png'))">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100 bg-[#0F172A]">
        <div class="min-h-screen bg-[#0F172A] relative overflow-x-hidden">
            <!-- Decorative Glow background -->
            <div class="absolute top-[-10%] right-[-10%] w-[45%] h-[45%] rounded-full bg-purple-600/5 blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[45%] h-[45%] rounded-full bg-blue-600/5 blur-[120px] pointer-events-none"></div>

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
