@props([
    'variant' => 'default',
])

@php
$variants = [
    'default' => 'bg-background text-foreground',
    'destructive' => 'border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive',
];
$classes = 'relative w-full rounded-lg border px-4 py-3 text-sm '.($variants[$variant] ?? $variants['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>