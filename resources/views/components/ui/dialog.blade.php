@props([
    'show' => false,
])

<div x-data="{ open: {{ $show ? 'true' : 'false' }} }" class="inline-block">
    @if (isset($trigger))
        <div @click="open = true">{{ $trigger }}</div>
    @endif

    <template x-teleport="body">
        <div x-show="open" x-transition.opacity class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-black/80" @click="open = false"></div>
            <div class="fixed left-1/2 top-1/2 z-50 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-lg border bg-background p-6 shadow-lg" @click.stop>
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
