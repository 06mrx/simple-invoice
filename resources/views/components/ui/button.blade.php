@props([
    'variant' => 'default',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
])

@php
$base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 active:scale-[0.98]';
$variants = [
    'default' => 'bg-primary text-primary-foreground shadow-sm hover:bg-primary/90',
    'destructive' => 'bg-destructive text-destructive-foreground shadow-sm hover:bg-destructive/90',
    'outline' => 'border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
    'secondary' => 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
    'ghost' => 'hover:bg-accent hover:text-accent-foreground',
    'link' => 'text-primary underline-offset-4 hover:underline',
    'success' => 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-600/90',
    'warning' => 'bg-amber-500 text-white shadow-sm hover:bg-amber-500/90',
    'info' => 'bg-blue-600 text-white shadow-sm hover:bg-blue-600/90',
];
$sizes = [
    'default' => 'h-10 px-4 py-2',
    'sm' => 'h-9 rounded-md px-3',
    'lg' => 'h-11 rounded-md px-8',
    'icon' => 'h-10 w-10',
];
$classes = $base.' '.($variants[$variant] ?? $variants['default']).' '.($sizes[$size] ?? $sizes['default']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
