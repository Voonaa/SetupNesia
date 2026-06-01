<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        @if($cart && $cart->items->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items List Left -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                                <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    <tr>
                                        <th class="px-6 py-4">Product</th>
                                        <th class="px-6 py-4">Price</th>
                                        <th class="px-6 py-4">Quantity</th>
                                        <th class="px-6 py-4">Subtotal</th>
                                        <th class="px-6 py-4 text-right">Remove</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                                    @foreach($cart->items as $item)
                                        <tr class="hover:bg-slate-800/30 transition duration-150">
                                            <!-- Product Info -->
                                            <td class="px-6 py-4 flex items-center space-x-3">
                                                <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden shrink-0 border border-slate-700">
                                                    @if($item->product && $item->product->primaryImage)
                                                        <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold">N/A</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('shop.show', $item->product->slug) }}" class="font-bold text-slate-200 hover:text-purple-400 transition block">{{ $item->product->name }}</a>
                                                    <span class="text-xs text-slate-400 block mt-0.5 font-semibold">Stock: {{ $item->product->stock }} units left</span>
                                                </div>
                                            </td>
                                            
                                            <!-- Price -->
                                            <td class="px-6 py-4 text-slate-300 font-semibold">
                                                Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                            </td>
                                            
                                            <!-- Quantity Form -->
                                            <td class="px-6 py-4">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center space-x-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="quantity" class="border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-lg text-xs py-1.5 px-2.5 shadow-sm" onchange="this.form.submit()">
                                                        @for($i = 1; $i <= min(10, $item->product->stock); $i++)
                                                            <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </form>
                                            </td>

                                            <!-- Subtotal -->
                                            <td class="px-6 py-4 font-extrabold text-slate-100">
                                                Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                            </td>

                                            <!-- Remove Action -->
                                            <td class="px-6 py-4 text-right">
                                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove item from cart?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-red-950/20 hover:bg-red-950/40 text-red-400 rounded-lg border border-red-500/20 transition cursor-pointer" title="Remove">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Checkout Billing Box Right -->
                <div class="space-y-6">
                    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                        <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-5">Order Summary</h3>
                        
                        <div class="space-y-4 text-sm text-slate-300">
                            <div class="flex justify-between">
                                <span>Subtotal Items</span>
                                <span class="font-bold text-slate-100">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping costs</span>
                                <span class="text-slate-500 italic">Calculated at checkout</span>
                            </div>
                            <div class="flex justify-between pt-4 border-t border-slate-800 text-base font-bold text-slate-100">
                                <span>Total Price</span>
                                <span class="text-purple-400 text-lg">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-800 mt-6">
                            <a href="{{ route('checkout.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/25 cursor-pointer">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart State -->
            <div class="bg-[#1E293B]/50 border border-slate-800 rounded-3xl p-16 text-center max-w-xl mx-auto shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                <div class="p-4 bg-purple-500/10 border border-purple-500/25 text-purple-400 rounded-full w-fit mx-auto shadow-lg shadow-purple-500/10 mb-6">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-white mb-2">Your Cart is Empty</h3>
                <p class="text-slate-400 font-semibold mb-8 max-w-xs mx-auto">Looks like you haven't added any premium desk accessories to your cart yet.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 transition shadow-lg shadow-purple-500/20 cursor-pointer">
                    Browse Shop Catalog
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
