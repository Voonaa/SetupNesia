<x-app-layout>
    @php
        $metaDescription = \Illuminate\Support\Str::limit(strip_tags($product->description), 155, '...');
        $ogImage = $product->primaryImage ? url($product->primaryImage->image_path) : asset('images/logo.png');
    @endphp
    @section('title', 'Buy ' . $product->name . ' - SetupNesia')
    @section('meta_description', $metaDescription)
    @section('og_type', 'product')
    @section('og_image', $ogImage)

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-400 mb-8 space-x-2">
            <a href="/" class="hover:text-slate-100 transition">Home</a>
            <span>&middot;</span>
            <a href="{{ route('shop.index') }}" class="hover:text-slate-100 transition">Shop</a>
            <span>&middot;</span>
            <span class="text-slate-200 truncate">{{ $product->name }}</span>
        </nav>

        <!-- Product Presentation -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <!-- Left: Product Image Display -->
            <div>
                <div class="aspect-[4/3] rounded-2xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                    @if($product->primaryImage)
                        <img src="{{ $product->primaryImage->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold">N/A</div>
                    @endif
                </div>

                <!-- Gallery Thumbnails (if any) -->
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-4 mt-4">
                        @foreach($product->images as $img)
                            <div class="aspect-[4/3] rounded-xl bg-slate-900 border {{ $img->is_primary ? 'border-purple-500' : 'border-slate-800' }} overflow-hidden cursor-pointer shadow-md hover:border-slate-700/80 transition duration-150">
                                <img src="{{ $img->image_path }}" alt="Gallery Thumbnail" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Product Operations -->
            <div class="space-y-6">
                <div>
                    <!-- Category Tag -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-950/50 text-purple-400 border border-purple-500/20 uppercase tracking-widest mb-3">
                        {{ $product->category->name }}
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight font-sans">
                        {{ $product->name }}
                    </h1>
                </div>

                <div class="flex items-center justify-between pb-6 border-b border-slate-800/80">
                    <span class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-blue-400">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>

                    <span class="text-sm font-semibold text-slate-400">
                        Weight: <strong class="text-slate-200">{{ $product->weight }} grams</strong>
                    </span>
                </div>

                <!-- Stock Levels -->
                <div>
                    @if($product->stock <= 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-950/40 text-red-400 border border-red-500/20">
                            Out of Stock
                        </span>
                    @elseif($product->stock <= 5)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-950/40 text-amber-400 border border-amber-500/20">
                            Hurry! Only {{ $product->stock }} left in stock
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-950/40 text-emerald-400 border border-emerald-500/20">
                            In Stock: {{ $product->stock }} units
                        </span>
                    @endif
                </div>

                <!-- Short Summary -->
                <p class="text-slate-400 text-sm leading-relaxed font-medium">
                    Transform your desktop setup with this premium workspace accessory. Engineered with top-grade materials to ensure outstanding durability and visual aesthetics.
                </p>

                <!-- Purchase CTA Form -->
                @auth
                    @if($product->stock > 0)
                        <form action="{{ route('cart.store', $product) }}" method="POST" class="space-y-4 pt-4 border-t border-slate-800/60">
                            @csrf
                            
                            <div class="flex items-center space-x-4">
                                <div class="w-32">
                                    <x-input-label for="quantity" :value="__('Quantity')" />
                                    <select id="quantity" name="quantity" class="mt-2 block w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 py-2.5 px-3">
                                        @for($i = 1; $i <= min(10, $product->stock); $i++)
                                            <option value="{{ $i }}">{{ $i }} unit{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/25 cursor-pointer">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Add to Shopping Cart
                            </button>
                        </form>
                    @endif
                @else
                    <div class="p-4 bg-slate-900 border border-slate-800 rounded-2xl text-center text-sm text-slate-400 mt-6">
                        <a href="{{ route('login') }}" class="font-bold text-purple-400 hover:text-purple-300 transition">Log In</a> or <a href="{{ route('register') }}" class="font-bold text-purple-400 hover:text-purple-300 transition">Register</a> to add this item to your cart and make purchases.
                    </div>
                @endauth
            </div>
        </div>

        <!-- Description Details Tabs -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl p-8 mb-16 relative">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
            <h3 class="font-bold text-xl text-slate-100 border-b border-slate-800 pb-3 mb-6">Product Specifications</h3>
            
            <div class="text-slate-300 text-base leading-relaxed space-y-4 whitespace-pre-wrap font-medium">
                {{ $product->description }}
            </div>
        </div>

        <!-- Related Products Section -->
        @if($relatedProducts->count() > 0)
            <div>
                <h3 class="font-bold text-xl text-slate-100 mb-6">Related Setup Gear</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="bg-[#1E293B]/40 hover:bg-[#1E293B]/60 border border-slate-800/80 hover:border-slate-700/60 rounded-2xl p-4 transition duration-300 flex flex-col justify-between shadow-lg relative group">
                            <a href="{{ route('shop.show', $related->slug) }}" class="block">
                                <div class="aspect-[4/3] rounded-xl bg-slate-800 overflow-hidden border border-slate-700/50 mb-4">
                                    @if($related->primaryImage)
                                        <img src="{{ $related->primaryImage->image_path }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold">N/A</div>
                                    @endif
                                </div>
                                <h4 class="font-bold text-slate-100 group-hover:text-purple-400 transition text-sm leading-snug line-clamp-1">{{ $related->name }}</h4>
                                <span class="font-extrabold text-slate-300 text-sm block mt-2">Rp {{ number_format($related->price, 0, ',', '.') }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
