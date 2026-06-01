<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Users -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-700/80 transition duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-purple-600/5 group-hover:bg-purple-600/10 blur-xl transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Customers</span>
                    <span class="text-3xl font-extrabold text-slate-100 block mt-2">{{ number_format($metrics['total_customers']) }}</span>
                </div>
                <div class="p-3 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-xl shadow-lg shadow-purple-500/10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <span class="text-xs text-purple-400 font-semibold block mt-4">&uarr; Customer registrations active</span>
        </div>

        <!-- Card 2: Total Products -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-700/80 transition duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-blue-600/5 group-hover:bg-blue-600/10 blur-xl transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Products</span>
                    <span class="text-3xl font-extrabold text-slate-100 block mt-2">{{ number_format($metrics['total_products']) }}</span>
                </div>
                <div class="p-3 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-xl shadow-lg shadow-blue-500/10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <span class="text-xs text-blue-400 font-semibold block mt-4">&uarr; Workspace products seeded</span>
        </div>

        <!-- Card 3: Total Orders -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-700/80 transition duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-pink-600/5 group-hover:bg-pink-600/10 blur-xl transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Orders</span>
                    <span class="text-3xl font-extrabold text-slate-100 block mt-2">{{ number_format($metrics['total_orders']) }}</span>
                </div>
                <div class="p-3 bg-pink-500/10 border border-pink-500/20 text-pink-400 rounded-xl shadow-lg shadow-pink-500/10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <span class="text-xs text-pink-400 font-semibold block mt-4">Transacting orders active</span>
        </div>

        <!-- Card 4: Total Revenue -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-slate-700/80 transition duration-300">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-emerald-600/5 group-hover:bg-emerald-600/10 blur-xl transition duration-300"></div>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Revenue</span>
                    <span class="text-2xl lg:text-3xl font-extrabold text-slate-100 block mt-2">Rp {{ number_format($metrics['total_revenue'], 0, ',', '.') }}</span>
                </div>
                <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl shadow-lg shadow-emerald-500/10">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <span class="text-xs text-emerald-400 font-semibold block mt-4">&uarr; Successful payments calculated</span>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-lg text-slate-100">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-purple-300 transition duration-150">
                View All Orders
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Order Number</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Total Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Placed At</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-800/30 transition duration-150">
                            <td class="px-6 py-4 font-bold text-purple-400 font-mono">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="block font-bold text-slate-200">{{ $order->user->name }}</span>
                                <span class="block text-slate-400 text-xs font-medium">{{ $order->user->email }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-100">
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
                                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold uppercase rounded-lg border border-slate-700/50 transition cursor-pointer">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                No recent orders.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
