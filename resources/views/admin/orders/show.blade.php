<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-slate-400">
            <a href="{{ route('admin.orders.index') }}" class="hover:text-slate-100 transition">Orders</a>
            <span>&middot;</span>
            <span class="text-slate-200">Order {{ $order->order_number }}</span>
        </div>
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight mt-1">
            Order Detail: <span class="font-mono text-purple-400">{{ $order->order_number }}</span>
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details Left (Items Table, Addresses) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Ordered Items Card -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
                <div class="p-6 border-b border-slate-800">
                    <h3 class="font-bold text-lg text-slate-100">Ordered Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                        <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Product</th>
                                <th class="px-6 py-4">Price</th>
                                <th class="px-6 py-4">Qty</th>
                                <th class="px-6 py-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-slate-800/30 transition duration-150">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden shrink-0 border border-slate-700">
                                            @if($item->product && $item->product->primaryImage)
                                                <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-slate-500">N/A</div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-200 block">{{ $item->product ? $item->product->name : 'Deleted Product' }}</span>
                                            <span class="text-xs text-slate-400 block mt-0.5">Weight: {{ $item->product ? $item->product->weight : 0 }}g</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-300 font-semibold">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-300">
                                        x{{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-extrabold text-slate-100">
                                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Panel -->
                <div class="p-6 bg-[#1E293B]/40 border-t border-slate-800 flex justify-end">
                    <div class="w-72 space-y-3 text-sm text-slate-300">
                        <div class="flex justify-between">
                            <span>Subtotal Items</span>
                            <span class="font-semibold text-slate-100">Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping Cost</span>
                            <span class="font-semibold text-slate-100">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-slate-800 text-base font-bold text-slate-100">
                            <span>Grand Total</span>
                            <span class="text-purple-400 text-lg">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Details Card -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl p-6 space-y-4">
                <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3">Shipping & Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Destination Address</span>
                        <p class="mt-1 text-slate-300 leading-relaxed font-semibold">
                            {{ $order->shipping_address }}
                        </p>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Customer Notes</span>
                        <p class="mt-1 text-slate-400 italic">
                            {{ $order->notes ?? 'No special instructions.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details Right (Status Update, Payment) -->
        <div class="space-y-8">
            <!-- Order Status Controls Card -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3 mb-4">Order Operations</h3>
                
                <span class="text-xs text-slate-400 block uppercase font-bold tracking-wider mb-1">Current Order Status</span>
                @php
                    $badgeClass = match($order->status) {
                        'pending' => 'bg-amber-950/40 text-amber-400 border-amber-500/20',
                        'paid' => 'bg-emerald-950/40 text-emerald-400 border-emerald-500/20',
                        'processing' => 'bg-blue-950/40 text-blue-400 border-blue-500/20',
                        'shipped' => 'bg-purple-950/40 text-purple-400 border-purple-500/20',
                        'completed' => 'bg-emerald-950/40 text-emerald-400 border-emerald-500/20',
                        'cancelled' => 'bg-red-950/40 text-red-400 border-red-500/20',
                        default => 'bg-slate-800 text-slate-300 border-slate-700/50',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wide border {{ $badgeClass }} mb-6">
                    {{ $order->status }}
                </span>

                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4 pt-4 border-t border-slate-800">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <x-input-label for="status" :value="__('Change Order Status')" />
                        <select id="status" name="status" class="mt-2 block w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition py-2.5 px-3">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending (Unpaid)</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/25 cursor-pointer">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Payment Logs Card -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="font-bold text-lg text-slate-100 border-b border-slate-800 pb-3">Payment Information</h3>
                
                @if($order->payment)
                    <div class="text-sm space-y-3">
                        <div>
                            <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Midtrans Transaction ID</span>
                            <span class="font-mono text-purple-400 font-semibold block mt-0.5">{{ $order->payment->transaction_id ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Payment Channel</span>
                            <span class="font-semibold text-slate-200 block mt-0.5 uppercase">{{ $order->payment->payment_type ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Gross Amount Paid</span>
                            <span class="font-bold text-slate-100 block mt-0.5">Rp {{ number_format($order->payment->gross_amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-500 font-bold uppercase tracking-wider">Gateway Status</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase mt-1 {{ $order->payment->transaction_status === 'settlement' ? 'bg-emerald-950/40 text-emerald-400 border border-emerald-500/20' : 'bg-amber-950/40 text-amber-400 border border-amber-500/20' }}">
                                {{ $order->payment->transaction_status }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 text-slate-500 text-sm">
                        <svg class="h-8 w-8 mx-auto text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        No active payment record found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
