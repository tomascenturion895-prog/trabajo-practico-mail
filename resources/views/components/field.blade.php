@props(['name', 'label', 'type' => 'text', 'required' => false, 'hint' => null, 'model' => null])
<div>
    <label for="{{ $name }}" class="block mb-1.5 text-sm font-semibold text-slate-700">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
           @if(! $model) value="{{ old($name) }}" @endif
           {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500']) }}>
    @if($hint)<p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
