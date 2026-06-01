<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
            {{ __('Manage Orders') }}
        </h2>
    </x-slot>

    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Order Number</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Total Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Placed At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-800/30 transition duration-150">
                            <td class="px-6 py-4 font-bold text-purple-400 font-mono text-base">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="block text-slate-200 font-bold">{{ $order->user->name }}</span>
                                <span class="block text-slate-400 text-xs font-medium">{{ $order->user->email }}</span>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-slate-100">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide border {{ $badgeClass }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-medium">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold uppercase rounded-lg transition duration-150 border border-slate-700/50 cursor-pointer">
                                    Detail
                                    <svg class="h-3.5 w-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                <svg class="h-10 w-10 mx-auto text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
