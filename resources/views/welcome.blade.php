<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.favicon')
</head>
<body class="min-h-screen bg-background text-foreground flex items-center justify-center">
    <div class="text-center space-y-6">
        <h1 class="text-3xl font-bold tracking-tight">{{ config('app.name') }}</h1>
        <p class="text-muted-foreground">Admin template with Tailwind + shadcn style.</p>
        <div class="flex justify-center gap-3">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">Login</a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">Register</a>
        </div>
    </div>
</body>
</html>