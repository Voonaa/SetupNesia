<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-white transition mb-2">
                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to My Orders
                </a>
                <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
                    Order {{ $order->order_number }}
                </h2>
            </div>
            
            <div class="flex items-center space-x-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                        'paid' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                        'processing' => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
                        'shipped' => 'bg-purple-500/10 border-purple-500/20 text-purple-400',
                        'completed' => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400',
                        'cancelled' => 'bg-red-500/10 border-red-500/20 text-red-400',
                    ];
                    $badgeStyle = $statusColors[$order->status] ?? 'bg-slate-500/10 border-slate-500/20 text-slate-400';
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-extrabold tracking-wider uppercase border rounded-xl {{ $badgeStyle }}">
                    {{ $order->status }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
        <!-- Status Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl flex items-center space-x-3 shadow-lg shadow-emerald-500/5">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl flex items-center space-x-3 shadow-lg shadow-red-500/5">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Order Items & Delivery details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items Card -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
                    
                    <div class="p-6 border-b border-slate-800 bg-[#1E293B]/30">
                        <h3 class="font-bold text-lg text-slate-100">Ordered Items</h3>
                    </div>

                    <div class="divide-y divide-slate-800 bg-[#1E293B]/10">
                        @foreach($order->items as $item)
                            <div class="p-6 flex items-center justify-between hover:bg-slate-800/20 transition duration-150">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-xl bg-slate-800 overflow-hidden shrink-0 border border-slate-700">
                                        @if($item->product && $item->product->primaryImage)
                                            <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-500 font-bold text-xs">N/A</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-200 text-sm hover:text-purple-400 transition">
                                            @if($item->product)
                                                <a href="{{ route('shop.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                            @else
                                                N/A (Product Deleted)
                                            @endif
                                        </h4>
                                        <p class="text-xs text-slate-400 mt-1 font-semibold">
                                            Rp {{ number_format($item->price, 0, ',', '.') }} &times; {{ $item->quantity }}
                                        </p>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-100">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Delivery Details Card -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-blue-500 to-purple-500"></div>
                    
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-5">Delivery Information</h3>
                    
                    <div class="space-y-6 text-sm">
                        <div>
                            <span class="text-xs text-slate-500 font-extrabold uppercase tracking-wider block">Customer Name</span>
                            <span class="text-slate-200 font-bold mt-1 block">{{ $order->user->name }}</span>
                            <span class="text-slate-400 font-medium text-xs block">{{ $order->user->email }}</span>
                        </div>
                        
                        <div>
                            <span class="text-xs text-slate-500 font-extrabold uppercase tracking-wider block">Shipping Address</span>
                            <p class="text-slate-300 font-semibold mt-1 bg-slate-900/60 p-4 border border-slate-800 rounded-xl leading-relaxed">
                                {{ $order->shipping_address }}
                            </p>
                        </div>

                        @if($order->notes)
                            <div>
                                <span class="text-xs text-slate-500 font-extrabold uppercase tracking-wider block">Order Notes</span>
                                <p class="text-slate-400 italic mt-1 bg-slate-900/40 p-4 border border-slate-800/50 rounded-xl">
                                    "{{ $order->notes }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Billing Summary & Payment/Cancellation Actions -->
            <div class="space-y-6">
                <!-- Order Pricing Breakdown -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                    
                    <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-5">Billing Summary</h3>
                    
                    @php
                        $subtotal = 0;
                        foreach($order->items as $item) {
                            $subtotal += $item->price * $item->quantity;
                        }
                    @endphp

                    <div class="space-y-4 text-sm text-slate-300">
                        <div class="flex justify-between">
                            <span>Items Subtotal</span>
                            <span class="font-bold text-slate-100">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping Cost</span>
                            <span class="font-bold text-slate-100">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-4 border-t border-slate-800 text-base font-bold text-slate-100">
                            <span>Grand Total</span>
                            <span class="text-purple-400 text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Midtrans QRIS Live Payment Box (Only when status is pending) -->
                @if($order->status === 'pending')
                    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-purple-500/25 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500"></div>
                        
                        <h3 class="font-extrabold text-base text-slate-100 flex items-center space-x-2 border-b border-slate-800 pb-3 mb-4">
                            <span class="p-1.5 bg-purple-500/10 border border-purple-500/25 rounded-lg text-purple-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </span>
                            <span>Payment Method: QRIS</span>
                        </h3>

                        <div class="space-y-4">
                            <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-xl space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-semibold">Payment Status</span>
                                    <span class="text-amber-400 font-extrabold uppercase tracking-wide">Awaiting Payment</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-semibold">Gateway</span>
                                    <span class="text-purple-400 font-bold">Midtrans Snap</span>
                                </div>
                            </div>

                            <button id="pay-button" class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/20 cursor-pointer">
                                Pay Now with QRIS
                            </button>

                            <div class="text-slate-400 text-3xs font-semibold uppercase tracking-wider text-center mt-2">
                                🔒 Secured & Encrypted via Midtrans Sandbox
                            </div>
                        </div>

                        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
                        <script>
                            document.getElementById('pay-button').onclick = function (e) {
                                e.preventDefault();
                                
                                const button = this;
                                button.disabled = true;
                                const originalText = button.innerText;
                                button.innerText = 'GENERATING PAYMENT CODE...';

                                fetch('{{ route('payment.snap-token', $order->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(err => { throw new Error(err.message || 'Failed to generate snap token.'); });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    button.disabled = false;
                                    button.innerText = originalText;
                                    
                                    snap.pay(data.token, {
                                        onSuccess: function(result) {
                                            window.location.href = "{{ route('payment.finish') }}?order_id={{ $order->order_number }}";
                                        },
                                        onPending: function(result) {
                                            window.location.href = "{{ route('payment.unfinish') }}?order_id={{ $order->order_number }}";
                                        },
                                        onError: function(result) {
                                            window.location.href = "{{ route('payment.error') }}?order_id={{ $order->order_number }}";
                                        },
                                        onClose: function() {
                                            // Customer closed popup without paying
                                        }
                                    });
                                })
                                .catch(error => {
                                    button.disabled = false;
                                    button.innerText = originalText;
                                    alert('Payment initialization failed: ' + error.message);
                                });
                            };
                        </script>
                    </div>
                @endif

                <!-- Cancellation Form Box -->
                @if(in_array($order->status, ['pending', 'paid']))
                    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-red-500/15 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-red-500/35"></div>
                        
                        <h3 class="font-bold text-base text-slate-100 border-b border-slate-800 pb-3 mb-4 flex items-center space-x-2">
                            <span class="p-1 bg-red-500/10 border border-red-500/25 rounded text-red-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <span>Need to Cancel?</span>
                        </h3>
                        
                        <p class="text-xs text-slate-400 font-semibold mb-5 leading-relaxed">
                            You can cancel this order since it has not been processed. If you've already paid, your funds will be queueing for refund verification.
                        </p>

                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This will restock the inventory immediately.');">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-950/20 hover:bg-red-950/45 text-red-400 border border-red-500/20 rounded-xl font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                                Cancel This Order
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
