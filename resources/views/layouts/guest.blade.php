<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('theme') === 'dark' }" x-init="if (dark) document.documentElement.classList.add('dark');">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.favicon')
    @stack('styles')
</head>

<body class="min-h-screen bg-background text-foreground antialiased">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="mb-6 flex justify-center">
                <a href="/" class="flex items-center gap-2 font-semibold text-lg">
                    <div class="flex h-9 w-9 items-center justify-center rounded-md bg-primary text-primary-foreground">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" /></svg>
                    </div>
                    {{ config('app.name') }}
                </a>
            </div>
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                <div class="p-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>

</html>