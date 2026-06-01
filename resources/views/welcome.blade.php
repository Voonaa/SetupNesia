<x-app-layout>
    @section('title', 'SetupNesia - Premium Mechanical Keyboards & Workspace Accessories')
    @section('meta_description', 'Discover premium custom mechanical keyboards, high-fidelity keycaps, workspace deskmats, mouse, and cable management accessories to level up your setup at SetupNesia.')
    
    <!-- Hero Section -->
    <div class="relative py-20 lg:py-32 overflow-hidden flex items-center bg-[#0F172A]">
        <!-- Glowing Ambient Circle Backgrounds -->
        <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-80 h-80 rounded-full bg-purple-600/10 blur-[100px] pointer-events-none"></div>
        <div class="absolute top-1/3 right-1/4 -translate-y-1/2 w-96 h-96 rounded-full bg-blue-600/10 blur-[130px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Typography -->
                <div class="space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-950/50 text-purple-400 border border-purple-500/20 uppercase tracking-widest">
                        Ultimate Desk Upgrades
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight font-sans">
                        Elevate Your <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-fuchsia-400 to-blue-400">
                            Workspace Setup
                        </span>
                    </h1>
                    <p class="text-slate-400 text-base sm:text-lg max-w-lg mx-auto lg:mx-0 leading-relaxed font-medium">
                        SetupNesia curated selection of premium mechanical keyboards, keycaps, mouse, monitor stands, and desk accessories designed to wow your aesthetics and boost your productivity.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4 pt-4">
                        <a href="{{ route('shop.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/30 hover:shadow-purple-500/40 cursor-pointer">
                            Explore Shop
                            <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#categories" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-xl font-bold text-sm text-slate-300 uppercase tracking-widest transition duration-150">
                            Browse Categories
                        </a>
                    </div>
                </div>

                <!-- Hero Graphic (Premium keyboard preview) -->
                <div class="relative flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-md lg:max-w-lg aspect-square rounded-3xl bg-gradient-to-br from-purple-600/10 to-blue-600/10 border border-slate-800 p-4 shadow-2xl overflow-hidden group">
                        <div class="absolute inset-0 bg-[#0F172A]/50 z-10 rounded-2xl"></div>
                        <img src="/images/products/keyboard.jpg" alt="Premium Keyboard" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition duration-500 relative z-0">
                        <div class="absolute bottom-6 left-6 right-6 z-20 p-6 bg-[#1E293B]/80 backdrop-blur-md rounded-2xl border border-slate-800 shadow-xl">
                            <span class="text-xs font-bold text-purple-400 uppercase tracking-wider block">Featured Hot Item</span>
                            <span class="text-slate-100 font-extrabold text-lg block mt-1">Keychron Q1 Pro Wireless</span>
                            <span class="text-slate-400 text-sm block mt-0.5">Custom mechanical keyboard with KSA keycaps.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Grid Section -->
    <div id="categories" class="py-20 border-t border-slate-900 bg-[#0B0F19]/40 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-bold text-purple-400 uppercase tracking-widest">Browse by Theme</span>
                <h2 class="text-3xl font-extrabold text-white leading-tight">Curated Setup Essentials</h2>
                <div class="w-16 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500 mx-auto"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categories as $cat)
                    @php
                        $iconClass = match($cat->slug) {
                            'mechanical-keyboard' => 'from-purple-500/10 to-purple-600/5 text-purple-400 border-purple-500/20',
                            'keycaps' => 'from-pink-500/10 to-pink-600/5 text-pink-400 border-pink-500/20',
                            'deskmat' => 'from-blue-500/10 to-blue-600/5 text-blue-400 border-blue-500/20',
                            'mouse' => 'from-emerald-500/10 to-emerald-600/5 text-emerald-400 border-emerald-500/20',
                            'monitor-stand' => 'from-amber-500/10 to-amber-600/5 text-amber-400 border-amber-500/20',
                            'cable-management' => 'from-rose-500/10 to-rose-600/5 text-rose-400 border-rose-500/20',
                            default => 'from-indigo-500/10 to-indigo-600/5 text-indigo-400 border-indigo-500/20',
                        };
                    @endphp
                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" class="group bg-[#1E293B]/40 hover:bg-[#1E293B]/60 border border-slate-800/80 hover:border-slate-700/60 rounded-2xl p-6 transition duration-300 flex flex-col justify-between shadow-lg relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-16 h-16 rounded-full bg-purple-500/5 group-hover:scale-150 transition duration-300 blur-xl"></div>
                        <div class="p-3 bg-gradient-to-br {{ $iconClass }} border rounded-xl w-fit shadow-md">
                            <span class="font-bold text-sm tracking-wider uppercase">{{ substr($cat->name, 0, 3) }}</span>
                        </div>
                        <div class="mt-6">
                            <h3 class="font-bold text-base text-slate-100 group-hover:text-purple-400 transition">{{ $cat->name }}</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-[180px] leading-relaxed line-clamp-2">{{ $cat->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Products Grid Section -->
    <div class="py-20 bg-[#0F172A] relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-16 space-y-4 sm:space-y-0">
                <div class="text-center sm:text-left space-y-2">
                    <span class="text-xs font-bold text-purple-400 uppercase tracking-widest">Setup Favorites</span>
                    <h2 class="text-3xl font-extrabold text-white leading-tight">Featured Hot Gear</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-xl text-sm font-bold text-slate-300 transition duration-150 shadow-md">
                    View Catalog
                    <svg class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-[#1E293B]/40 hover:bg-[#1E293B]/60 border border-slate-800/80 hover:border-slate-700/60 rounded-2xl p-4 transition duration-300 flex flex-col justify-between shadow-lg relative group">
                        <a href="{{ route('shop.show', $product->slug) }}" class="block">
                            <!-- Image container -->
                            <div class="aspect-[4/3] rounded-xl bg-slate-800 overflow-hidden border border-slate-700/50 mb-4 relative">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold">N/A</div>
                                @endif
                                <div class="absolute top-2 left-2 px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-[#0F172A]/80 backdrop-blur-md text-purple-400 border border-purple-500/20 uppercase tracking-wider">
                                    {{ $product->category->name }}
                                </div>
                            </div>
                            
                            <!-- Product Details -->
                            <div class="space-y-1.5">
                                <h3 class="font-bold text-slate-100 group-hover:text-purple-400 transition text-sm leading-snug line-clamp-1">{{ $product->name }}</h3>
                                <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed h-8">{{ $product->description }}</p>
                            </div>
                        </a>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-800/60">
                            <span class="font-extrabold text-base text-slate-200">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            
                            @auth
                                <form action="{{ route('cart.store', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2.5 bg-purple-600/10 hover:bg-purple-600 border border-purple-500/20 text-purple-400 hover:text-white rounded-xl transition duration-150 cursor-pointer shadow-md hover:shadow-purple-500/30" title="Add to Cart">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="p-2.5 bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition" title="Log in to purchase">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
