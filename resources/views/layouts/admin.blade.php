<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <div
        x-data="{
            sidebarOpen: false,
            collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
            dark: localStorage.getItem('theme') === 'dark'
        }"
        x-init="
            if (dark) document.documentElement.classList.add('dark');
            $watch('collapsed', v => localStorage.setItem('sidebar-collapsed', v));
            $watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); });
        "
        class="flex min-h-screen w-full">

        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside
            :class="[
                collapsed ? 'md:w-16' : 'md:w-64',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
            ]"
            class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground transition-all duration-200 ease-in-out md:relative">
            <!-- Header -->
            <div class="flex h-14 items-center border-b border-sidebar-border px-3" :class="collapsed ? 'justify-center px-2' : 'px-3'">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden font-semibold">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground overflow-hidden">
                        <img src="/images/favicon/android-chrome-192x192.png" alt="{{ config('app.name') }}" class="h-6 w-6 object-contain" />
                    </div>
                    <span x-show="!collapsed" x-transition.opacity class="truncate">{{ config('app.name') }}</span>
                </a>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-2 py-4">
                <div class="space-y-1">
                    <p x-show="!collapsed" class="px-2 text-xs font-semibold uppercase text-muted-foreground">Menu</p>
                    <a href="{{ route('dashboard') }}" title="Dashboard"
                        :class="collapsed ? 'justify-center px-2' : 'px-3'"
                        class="flex items-center gap-3 rounded-md py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span x-show="!collapsed" x-transition.opacity>Dashboard</span>
                    </a>
                    @can('view-bayi')
                    <a href="{{ route('bayi.index') }}" title="Bayi"
                        :class="collapsed ? 'justify-center px-2' : 'px-3'"
                        class="flex items-center gap-3 rounded-md py-2 text-sm font-medium transition-colors {{ request()->routeIs('bayi.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span x-show="!collapsed" x-transition.opacity>Bayi</span>
                    </a>
                    @endcan
                </div>

                @role('admin')
                <div class="mt-6 space-y-1">
                    <p x-show="!collapsed" class="px-2 text-xs font-semibold uppercase text-muted-foreground">Administration</p>
                    @foreach ([
                        ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['route' => 'admin.roles.index', 'pattern' => 'admin.roles.*', 'label' => 'Roles', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ['route' => 'admin.permissions.index', 'pattern' => 'admin.permissions.*', 'label' => 'Permissions', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                        ['route' => 'admin.audit.index', 'pattern' => 'admin.audit.*', 'label' => 'Audit', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                            :class="collapsed ? 'justify-center px-2' : 'px-3'"
                            class="flex items-center gap-3 rounded-md py-2 text-sm font-medium transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-sidebar-accent text-sidebar-accent-foreground' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" /></svg>
                            <span x-show="!collapsed" x-transition.opacity>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
                @endrole
            </nav>

            <!-- Footer -->
            <div class="border-t border-sidebar-border p-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" title="Logout"
                        :class="collapsed ? 'justify-center px-2' : 'px-3'"
                        class="flex w-full items-center gap-3 rounded-md py-2 text-sm font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span x-show="!collapsed" x-transition.opacity>Logout</span>
                    </x-ui.button>
                </form>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex flex-1 flex-col overflow-hidden bg-sidebar p-2">
            <div class="flex flex-1 flex-col overflow-hidden rounded-2xl border bg-background shadow-md">
            <header class="flex h-14 items-center gap-3 border-b bg-background px-4 md:px-6">
                <x-ui.button variant="outline" size="icon"
                    @click="window.innerWidth < 768 ? sidebarOpen = !sidebarOpen : collapsed = !collapsed"
                    aria-label="Toggle sidebar">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2" /><path d="M9 3v18" /></svg>
                </x-ui.button>

                <div class="flex-1">
                    <h1 class="text-lg font-semibold">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div x-data="{ commandOpen: false, query: '' }" @keydown.window="if (($event.ctrlKey || $event.metaKey) && $event.key.toLowerCase() === 'k') { $event.preventDefault(); commandOpen = true; }" @keydown.escape.window="commandOpen = false">
                    <x-ui.button variant="outline" @click="commandOpen = true; $nextTick(() => $refs.commandInput?.focus())" class="hidden md:inline-flex h-9 w-72 items-center gap-2 px-3 text-muted-foreground">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <span class="flex-1 text-left">Search...</span>
                        <kbd class="pointer-events-none inline-flex h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium text-muted-foreground">
                            <span class="text-xs">⌘</span>K
                        </kbd>
                    </x-ui.button>

                    <template x-teleport="body">
                        <div x-show="commandOpen" x-transition.opacity class="fixed inset-0 z-50">
                            <div class="fixed inset-0 bg-black/80" @click="commandOpen = false"></div>
                            <div class="fixed left-1/2 top-24 z-50 w-full max-w-lg -translate-x-1/2 rounded-lg border bg-popover text-popover-foreground shadow-lg" @click.stop>
                                <div class="flex items-center border-b px-3">
                                    <svg class="mr-2 h-4 w-4 shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    <input x-ref="commandInput" x-model="query" type="text" placeholder="Type a command or search..." class="flex h-11 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground" />
                                </div>
                                <div class="max-h-72 overflow-y-auto p-2">
                                    <div class="px-2 py-1.5 text-xs font-medium text-muted-foreground">Navigation</div>
                                    <a href="{{ route('dashboard') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Dashboard</a>
                                    @can('view-bayi')
                                    <a href="{{ route('bayi.index') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Bayi</a>
                                    @endcan
                                    @role('admin')
                                    <a href="{{ route('admin.users.index') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Users</a>
                                    <a href="{{ route('admin.roles.index') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Roles</a>
                                    <a href="{{ route('admin.permissions.index') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Permissions</a>
                                    <a href="{{ route('admin.audit.index') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Audit</a>
                                    @endrole
                                    <a href="{{ route('profile.edit') }}" x-show="query === '' || $el.textContent.toLowerCase().includes(query.toLowerCase())" class="flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Profile</a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <x-ui.button variant="outline" size="icon" @click="dark = !dark" aria-label="Toggle theme">
                    <svg x-show="!dark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="dark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </x-ui.button>

                <div class="relative" x-data="{ userMenu: false }">
                    <x-ui.button variant="outline" size="sm" @click="userMenu = !userMenu" class="gap-2 px-3 py-2">
                        <span class="hidden sm:inline-block">{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </x-ui.button>
                    <div x-show="userMenu" @click.outside="userMenu = false" x-transition class="absolute right-0 mt-2 w-48 rounded-md border bg-popover p-1 text-popover-foreground shadow-md">
                        <a href="{{ route('profile.edit') }}" class="block rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui.button type="submit" variant="ghost" size="sm" class="block w-full justify-start rounded-sm px-2 py-1.5 text-left text-sm hover:bg-accent hover:text-accent-foreground">Logout</x-ui.button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if (session('status') || session('success'))
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
                        {{ session('status') ?? session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-destructive/50 bg-destructive/10 px-4 py-3 text-sm text-destructive">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>