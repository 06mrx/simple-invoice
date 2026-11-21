@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'rounded-2xl border bg-card text-card-foreground shadow-sm transition-shadow hover:shadow-md '.$class]) }}>
    {{ $slot }}
</div>
