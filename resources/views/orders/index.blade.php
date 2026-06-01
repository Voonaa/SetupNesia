<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
            {{ __('My Orders') }}
        </h2>
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

        @if($orders && $orders->count() > 0)
            <div class="space-y-6">
                <!-- Orders Table / List -->
                <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                            <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">Order Number</th>
                                    <th class="px-6 py-4">Date Placed</th>
                                    <th class="px-6 py-4">Products</th>
                                    <th class="px-6 py-4">Total Amount</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-slate-800/30 transition duration-150">
                                        <!-- Order Number -->
                                        <td class="px-6 py-4 font-bold text-slate-200">
                                            {{ $order->order_number }}
                                        </td>
                                        
                                        <!-- Date Placed -->
                                        <td class="px-6 py-4 text-slate-400 font-semibold">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </td>

                                        <!-- Products Quick Summary -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                @foreach($order->items->take(3) as $item)
                                                    <div class="w-8 h-8 rounded bg-slate-800 overflow-hidden shrink-0 border border-slate-700" title="{{ $item->product ? $item->product->name : 'N/A' }}">
                                                        @if($item->product && $item->product->primaryImage)
                                                            <img src="{{ $item->product->primaryImage->image_path }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs font-bold">N/A</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if($order->items->count() > 3)
                                                    <span class="text-xs text-slate-500 font-bold">+{{ $order->items->count() - 3 }} more</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Total Amount -->
                                        <td class="px-6 py-4 font-extrabold text-slate-100">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="px-6 py-4">
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
                                            <span class="inline-flex items-center px-2.5 py-1 text-2xs font-extrabold tracking-wider uppercase border rounded-lg {{ $badgeStyle }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>

                                        <!-- Action -->
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 border border-slate-800 rounded-xl font-bold text-xs text-slate-300 bg-slate-900 hover:text-white hover:border-slate-700 transition cursor-pointer">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Orders State -->
            <div class="bg-[#1E293B]/50 border border-slate-800 rounded-3xl p-16 text-center max-w-xl mx-auto shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
                <div class="p-4 bg-purple-500/10 border border-purple-500/25 text-purple-400 rounded-full w-fit mx-auto shadow-lg shadow-purple-500/10 mb-6">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-white mb-2">No Orders Found</h3>
                <p class="text-slate-400 font-semibold mb-8 max-w-xs mx-auto">You haven't placed any orders yet. Check out our high-quality workspace accessories!</p>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 transition shadow-lg shadow-purple-500/20 cursor-pointer">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
