<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-slate-400">
            <a href="{{ route('admin.products.index') }}" class="hover:text-slate-100 transition">Products</a>
            <span>&middot;</span>
            <span class="text-slate-200">Add New Product</span>
        </div>
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight mt-1">
            {{ __('Add New Product') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
            
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <x-input-label for="name" :value="__('Product Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full" :value="old('name')" required autofocus placeholder="e.g. Keychron Q1 Pro Wireless Keyboard" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Category -->
                    <div>
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id" class="mt-1.5 block w-full border-slate-700 bg-slate-900 text-slate-100 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition duration-150 py-2.5 px-3" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                    </div>

                    <!-- Price -->
                    <div>
                        <x-input-label for="price" :value="__('Price (IDR)')" />
                        <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1.5 block w-full" :value="old('price')" required placeholder="e.g. 2899000" />
                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>

                    <!-- Stock -->
                    <div>
                        <x-input-label for="stock" :value="__('Stock')" />
                        <x-text-input id="stock" name="stock" type="number" class="mt-1.5 block w-full" :value="old('stock', 0)" required placeholder="e.g. 15" />
                        <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                    </div>

                    <!-- Weight -->
                    <div>
                        <x-input-label for="weight" :value="__('Weight (Grams)')" />
                        <x-text-input id="weight" name="weight" type="number" class="mt-1.5 block w-full" :value="old('weight', 500)" required placeholder="e.g. 1800" />
                        <x-input-error class="mt-2" :messages="$errors->get('weight')" />
                    </div>

                    <!-- Image -->
                    <div class="md:col-span-2">
                        <x-input-label for="image" :value="__('Product Primary Image')" />
                        <div class="mt-1.5 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-700 border-dashed rounded-xl bg-slate-900/50 hover:bg-slate-900 transition duration-150">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-400">
                                    <label for="image" class="relative cursor-pointer rounded-md font-semibold text-purple-400 hover:text-purple-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500 focus-within:ring-offset-slate-900 transition">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" class="sr-only" required accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, JPEG up to 2MB</p>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('image')" />
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="5" class="mt-1.5 block w-full border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition duration-150" placeholder="Write full premium product specifications, sound tests, customizable components..." required>{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Is Active -->
                    <div class="md:col-span-2 flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded bg-slate-900 border-slate-700 text-purple-600 shadow-sm focus:ring-purple-500 focus:ring-offset-slate-900" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <label for="is_active" class="ms-2 font-bold text-sm text-slate-300">Set this product as Active (Visible in shop)</label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg font-bold text-xs text-slate-300 uppercase tracking-widest transition duration-150">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Save Product') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
