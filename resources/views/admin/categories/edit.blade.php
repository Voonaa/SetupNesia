<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-slate-400">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-slate-100 transition">Categories</a>
            <span>&middot;</span>
            <span class="text-slate-200">Edit Category</span>
        </div>
        <h2 class="font-extrabold text-2xl text-slate-100 tracking-tight mt-1">
            {{ __('Edit Category') }}
        </h2>
    </x-slot>

    <div class="max-w-xl">
        <div class="bg-[#1E293B]/50 backdrop-blur-md border border-slate-800 rounded-2xl shadow-xl overflow-hidden relative">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-purple-500 via-fuchsia-500 to-blue-500"></div>
            
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Category Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full" :value="old('name', $category->name)" required autofocus placeholder="e.g. Mechanical Keyboard" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Description -->
                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" rows="4" class="mt-1.5 block w-full border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition duration-150" placeholder="Describe the workspace accessory category...">{{ old('description', $category->description) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg font-bold text-xs text-slate-300 uppercase tracking-widest transition duration-150">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Update Category') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
