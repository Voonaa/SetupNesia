@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-700 bg-slate-900 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm focus:ring-1 transition duration-150']) }}>
