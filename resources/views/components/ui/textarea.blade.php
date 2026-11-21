@props([
    'name' => null,
    'id' => null,
    'rows' => 3,
])

<textarea
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    rows="{{ $rows }}"
    {{ $attributes->merge(['class' => 'flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50']) }}
>{{ old($name, $slot) }}</textarea>