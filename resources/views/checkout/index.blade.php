<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
            {{ __('Checkout Order') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <!-- Left: Shipping Address Information Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-6">Delivery Details</h3>
                    
                    <div class="space-y-6">
                        <!-- Shipping Address -->
                        <div>
                            <x-input-label for="shipping_address" :value="__('Full Destination Address')" />
                            <textarea id="shipping_address" name="shipping_address" rows="4" class="mt-2 block w-full border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition duration-150 py-2.5 px-3" placeholder="Provide full address (street, city, district, postal code)..." required minlength="10">{{ old('shipping_address') }}</textarea>
                            <span class="text-3xs text-slate-500 mt-1 block font-semibold">Min 10 characters for dispatching.</span>
                            <x-input-error class="mt-2" :messages="$errors->get('shipping_address')" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Additional Delivery Instructions (Optional)')" />
                            <textarea id="notes" name="notes" rows="2" class="mt-2 block w-full border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition duration-150 py-2.5 px-3" placeholder="e.g., Leave with security guard, custom coiled cable color accent request...">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary & Place Order -->
            <div class="space-y-6">
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-4">Checkout Items</h3>
                    
                    <!-- Items Grid -->
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2 divide-y divide-slate-800">
                        @foreach($cart->items as $item)
                            <div class="flex items-center justify-between py-2 text-sm first:pt-0">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-10 h-10 rounded-lg bg-slate-800 overflow-hidden shrink-0 border border-slate-700">
                                        @if($item->product && $item->product->primaryImage)
                                            <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-500">N/A</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-200 block truncate">{{ $item->product->name }}</span>
                                        <span class="text-xs text-slate-400 block mt-0.5">x{{ $item->quantity }} unit{{ $item->quantity > 1 ? 's' : '' }}</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-slate-200 shrink-0">
                                    Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Financial Summary -->
                    <div class="space-y-3 text-sm text-slate-300 pt-4 border-t border-slate-800 mt-4">
                        <div class="flex justify-between">
                            <span>Subtotal Items</span>
                            <span class="font-semibold text-slate-100">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Weight</span>
                            <span class="font-semibold text-slate-100">{{ number_format($shipping['weight']) }} grams ({{ ceil($shipping['weight'] / 1000) }} kg)</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping Costs</span>
                            <span class="font-semibold text-slate-100">Rp {{ number_format($shipping['cost'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-4 border-t border-slate-800 text-base font-bold text-slate-100">
                            <span>Total Bill</span>
                            <span class="text-purple-400 text-lg">Rp {{ number_format($subtotal + $shipping['cost'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <div class="pt-6 border-t border-slate-800 mt-6">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/25 cursor-pointer">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
