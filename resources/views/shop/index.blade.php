<x-app-layout>
    @section('title', 'Shop Premium Keyboards & Workspace Accessories - SetupNesia')
    @section('meta_description', 'Browse SetupNesia catalog of custom mechanical keyboards, high-fidelity keycaps, deskmats, mouse stands, custom cables, and productivity workspace aesthetics.')
    
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
            {{ __('Shop Catalog') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Filters -->
            <div class="space-y-6">
                <!-- Search & Filters Card -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-5">Filter Accessories</h3>
                    
                    <form action="{{ route('shop.index') }}" method="GET" class="space-y-6">
                        <!-- Keep category parameter if set -->
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        <!-- Search -->
                        <div>
                            <x-input-label for="search" :value="__('Search Gear')" />
                            <div class="mt-2 relative">
                                <x-text-input id="search" name="search" type="text" class="block w-full pr-10" :value="request('search')" placeholder="e.g. keycaps..." />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <x-input-label :value="__('Price Range (IDR)')" />
                            <div class="grid grid-cols-2 gap-3 mt-2">
                                <div>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" class="w-full border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:ring-purple-500 rounded-lg text-xs py-2 px-2.5" placeholder="Min Price">
                                </div>
                                <div>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" class="w-full border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-600 focus:border-purple-500 focus:ring-purple-500 rounded-lg text-xs py-2 px-2.5" placeholder="Max Price">
                                </div>
                            </div>
                        </div>

                        <!-- Submit and Reset Buttons -->
                        <div class="space-y-2 pt-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/25 cursor-pointer">
                                Apply Filters
                            </button>
                            @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                                <a href="{{ route('shop.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-lg font-bold text-xs text-slate-400 hover:text-slate-200 uppercase tracking-widest transition duration-150">
                                    Reset Filters
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Categories filter block -->
                <div class="bg-[#1E293B]/50 border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-4">Categories</h3>
                    <div class="space-y-1">
                        <a href="{{ route('shop.index', request()->except('category')) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ !request('category') ? 'bg-purple-600/10 text-purple-400 border border-purple-500/20' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                            <span>All Categories</span>
                            <span class="text-xs font-bold text-slate-500 bg-slate-900 border border-slate-800 px-2 py-0.5 rounded-full">
                                {{ \App\Models\Product::count() }}
                            </span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $cat->slug])) }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition duration-150 {{ request('category') === $cat->slug ? 'bg-purple-600/10 text-purple-400 border border-purple-500/20' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs font-bold text-slate-500 bg-slate-900 border border-slate-800 px-2 py-0.5 rounded-full">
                                    {{ $cat->products()->count() }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Products Catalog Right -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
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
                    @empty
                        <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-16 text-slate-400">
                            <svg class="h-12 w-12 mx-auto text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-semibold text-lg">No products found matching your search parameters.</p>
                            <p class="text-sm text-slate-500 mt-1">Try clearing your filters or search keywords.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Links -->
                <div class="pt-6 border-t border-slate-900">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
