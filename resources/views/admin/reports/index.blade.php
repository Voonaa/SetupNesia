<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
                    Sales Reports
                </h2>
                <p class="text-xs text-slate-400 font-semibold mt-1">Daily, monthly, and yearly business revenue performance tracking</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.reports.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 rounded-xl font-bold text-xs text-white uppercase tracking-widest transition shadow-md shadow-emerald-500/10 cursor-pointer">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export to Excel
                </a>
                <a href="{{ route('admin.reports.print', request()->all()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded-xl font-bold text-xs text-white uppercase tracking-widest transition shadow-md shadow-purple-500/10 cursor-pointer">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print PDF Report
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Report Filtering Panel -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 to-blue-500"></div>
            
            <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Report Interval</label>
                    <select name="type" id="type-select" class="w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-xl text-sm py-2.5 shadow-sm">
                        <option value="daily" {{ $type === 'daily' ? 'selected' : '' }}>Daily Range</option>
                        <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <!-- Daily Range Selectors -->
                <div id="daily-filters" class="md:col-span-2 grid grid-cols-2 gap-4" style="display: {{ $type === 'daily' ? 'grid' : 'none' }}">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-xl text-sm py-2 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-xl text-sm py-2 shadow-sm" />
                    </div>
                </div>

                <!-- Monthly Selectors -->
                <div id="monthly-filters" class="md:col-span-2 grid grid-cols-2 gap-4" style="display: {{ $type === 'monthly' ? 'grid' : 'none' }}">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Year</label>
                        <select name="year" class="w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-xl text-sm py-2.5 shadow-sm">
                            @for($y = (int)date('Y'); $y >= (int)date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Month</label>
                        <select name="month" class="w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-xl text-sm py-2.5 shadow-sm">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Yearly Selectors -->
                <div id="yearly-filters" class="md:col-span-2" style="display: {{ $type === 'yearly' ? 'block' : 'none' }}">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Year</label>
                    <select name="year_only" class="w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-xl text-sm py-2.5 shadow-sm">
                        @for($y = (int)date('Y'); $y >= (int)date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition shadow-lg shadow-purple-500/25 cursor-pointer">
                        Filter Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Metrics Overview Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-purple-500"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider block">Total Sales Orders</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">{{ $report['total_orders'] }}</span>
                    </div>
                    <div class="p-3 bg-purple-500/10 border border-purple-500/25 rounded-xl text-purple-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-emerald-500"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider block">Total Revenue</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">Rp {{ number_format($report['total_revenue'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-emerald-500/10 border border-emerald-500/25 rounded-xl text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Items Sold -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-blue-500"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider block">Total Items Sold</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">{{ $report['total_items_sold'] }} units</span>
                    </div>
                    <div class="p-3 bg-blue-500/10 border border-blue-500/25 rounded-xl text-blue-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Order Value -->
            <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-pink-500"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider block">Average Order Value</span>
                        <span class="text-2xl font-extrabold text-white mt-1 block">Rp {{ number_format($report['average_order_value'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-pink-500/10 border border-pink-500/25 rounded-xl text-pink-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Listing Table -->
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
            
            <div class="p-6 border-b border-slate-800 bg-[#1E293B]/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="font-extrabold text-lg text-slate-100">{{ $report['title'] }}</h3>
                <span class="text-xs font-bold text-slate-400 bg-slate-900/80 px-3 py-1.5 border border-slate-800 rounded-lg">
                    {{ $report['orders']->count() }} Transactions Found
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                    <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Order Number</th>
                            <th class="px-6 py-4">Date Placed</th>
                            <th class="px-6 py-4">Customer Info</th>
                            <th class="px-6 py-4">Ordered Items</th>
                            <th class="px-6 py-4">Shipping Cost</th>
                            <th class="px-6 py-4">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                        @forelse($report['orders'] as $order)
                            <tr class="hover:bg-slate-800/30 transition duration-150">
                                <td class="px-6 py-4 font-bold text-purple-400">
                                    <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-slate-400 font-semibold">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-200 block">{{ $order->user->name }}</span>
                                    <span class="text-xs text-slate-400 block font-medium mt-0.5">{{ $order->user->email }}</span>
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="text-xs text-slate-300 font-semibold">
                                            {{ $item->product ? $item->product->name : 'N/A' }} 
                                            <span class="text-slate-500 font-extrabold">&times; {{ $item->quantity }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-slate-300 font-semibold">
                                    Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-extrabold text-slate-100">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic font-semibold">
                                    No transaction reports match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script to toggle form inputs -->
    <script>
        document.getElementById('type-select').onchange = function() {
            const type = this.value;
            document.getElementById('daily-filters').style.display = type === 'daily' ? 'grid' : 'none';
            document.getElementById('monthly-filters').style.display = type === 'monthly' ? 'grid' : 'none';
            document.getElementById('yearly-filters').style.display = type === 'yearly' ? 'block' : 'none';
            
            // Adjust form parameter values if yearly chosen
            if (type === 'yearly') {
                const yearVal = document.querySelector('select[name="year_only"]').value;
                document.querySelector('select[name="year"]').value = yearVal;
            }
        };
    </script>
</x-admin-layout>
