<nav x-data="{ open: false }" class="bg-[#1E293B]/70 backdrop-blur-xl border-b border-slate-800 sticky top-0 z-40 relative">
    <!-- Glowing Top Accent Line -->
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
    
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <x-application-logo />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-full">
                    <x-nav-link :href="url('/')" :active="request()->is('/')" class="text-slate-300 hover:text-white font-bold transition">
                        {{ __('Home') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index') || request()->is('shop*')" class="text-slate-300 hover:text-white font-bold transition">
                        {{ __('Shop Catalog') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-300 hover:text-white font-bold transition">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index') || request()->is('orders*')" class="text-slate-300 hover:text-white font-bold transition">
                            {{ __('My Orders') }}
                        </x-nav-link>

                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" class="text-purple-400 hover:text-purple-300 font-bold transition uppercase tracking-wider text-xs">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings & Cart Dropdowns -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                <!-- Cart Button with Counter -->
                @php
                    $cartCount = 0;
                    if(Auth::check()) {
                        $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                        if($cart) {
                            $cartCount = $cart->items()->sum('quantity');
                        }
                    }
                @endphp
                
                @auth
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-400 hover:text-slate-100 transition rounded-xl bg-slate-900 border border-slate-800 shadow-md">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center px-2 py-1 text-2xs font-extrabold leading-none text-white bg-purple-600 rounded-full border border-slate-900 shadow-lg shadow-purple-500/35">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-slate-800 text-sm leading-4 font-bold rounded-xl text-slate-300 bg-slate-900 hover:text-slate-100 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1.5">
                                    <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')" class="text-slate-300 hover:bg-slate-800 hover:text-white font-semibold">
                                {{ __('Profile Settings') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('orders.index')" class="text-slate-300 hover:bg-slate-800 hover:text-white font-semibold">
                                {{ __('My Orders') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();" class="text-red-400 hover:bg-red-950/20 hover:text-red-300 font-semibold">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-slate-300 hover:text-white transition">Log In</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 transition shadow-md shadow-purple-500/10 cursor-pointer">Register</a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-100 hover:bg-slate-800 border border-transparent focus:outline-none transition duration-150 ease-in-out cursor-pointer">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#1E293B]/90 backdrop-blur-md border-t border-slate-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="url('/')" :active="request()->is('/')" class="font-bold text-slate-300 hover:text-white">
                {{ __('Home') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')" class="font-bold text-slate-300 hover:text-white">
                {{ __('Shop Catalog') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold text-slate-300 hover:text-white">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index') || request()->is('orders*')" class="font-bold text-slate-300 hover:text-white">
                    {{ __('My Orders') }}
                </x-responsive-nav-link>

                @if(Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" class="font-bold text-purple-400">
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')" class="font-bold text-slate-300 hover:text-white">
                    {{ __('My Cart') }} ({{ $cartCount }})
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-800">
            @auth
                <div class="px-4">
                    <div class="font-bold text-base text-slate-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" class="text-slate-300">
                        {{ __('Profile Settings') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('orders.index')" class="text-slate-300">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();" class="text-red-400">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-2 space-y-2">
                    <a href="{{ route('login') }}" class="block text-center w-full px-4 py-2 border border-slate-800 rounded-lg text-slate-300 font-bold hover:text-white">Log In</a>
                    <a href="{{ route('register') }}" class="block text-center w-full px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg text-white font-bold">Register</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
