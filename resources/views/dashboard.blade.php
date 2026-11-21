@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
            <p class="text-muted-foreground">Selamat datang, {{ auth()->user()->name }}.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <x-ui.card class="overflow-hidden">
                <x-ui.card-content class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Total Users</p>
                            <div class="mt-2 text-3xl font-bold tracking-tight">{{ \App\Models\User::count() }}</div>
                            <p class="mt-1 text-xs text-muted-foreground">Registered accounts</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card class="overflow-hidden">
                <x-ui.card-content class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Roles</p>
                            <div class="mt-2 text-3xl font-bold tracking-tight">{{ \Spatie\Permission\Models\Role::count() }}</div>
                            <p class="mt-1 text-xs text-muted-foreground">Defined roles</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-500/10 text-violet-600 dark:text-violet-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card class="overflow-hidden">
                <x-ui.card-content class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Permissions</p>
                            <div class="mt-2 text-3xl font-bold tracking-tight">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                            <p class="mt-1 text-xs text-muted-foreground">Registered permissions</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                        </div>
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card class="overflow-hidden">
                <x-ui.card-content class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Bayi</p>
                            <div class="mt-2 text-3xl font-bold tracking-tight">{{ \App\Models\Bayi::count() }}</div>
                            <p class="mt-1 text-xs text-muted-foreground">Example records</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        </div>

        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Template siap digunakan</x-ui.card-title>
                <x-ui.card-description>Gunakan modul Bayi sebagai referensi CRUD.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <p class="text-sm text-muted-foreground">Admin panel ini sudah mencakup manajemen user, role, permission, dan audit log.</p>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
