<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight">
                {{ __('Manage Products') }}
            </h2>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-purple-500 hover:to-blue-500 active:from-purple-700 active:to-blue-700 transition duration-150 shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30 cursor-pointer">
                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Product
            </a>
        </div>
    </x-slot>

    <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-sm text-slate-200">
                <thead class="bg-[#1E293B]/80 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Product Info</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Weight</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-[#1E293B]/20">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-800/30 transition duration-150">
                            <td class="px-6 py-4 flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden shrink-0 border border-slate-700">
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xs font-bold text-slate-500">N/A</div>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-slate-200 block text-base">{{ $product->name }}</span>
                                    <span class="text-xs text-purple-400 block font-mono">{{ $product->slug }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-300 font-semibold">
                                {{ $product->category->name }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-100">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold {{ $product->stock <= 5 ? 'text-amber-400' : 'text-slate-300' }}">
                                    {{ $product->stock }} units
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $product->weight }}g
                            </td>
                            <td class="px-6 py-4">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/40 text-emerald-400 border border-emerald-500/20">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-950/40 text-red-400 border border-red-500/20">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 shrink-0 whitespace-nowrap">
                                <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center p-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg transition duration-150 border border-slate-700/50" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center p-2 bg-red-950/20 hover:bg-red-950/40 text-red-400 rounded-lg transition duration-150 border border-red-500/20 cursor-pointer" title="Delete">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                <svg class="h-10 w-10 mx-auto text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                No products found. Click "Add Product" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
