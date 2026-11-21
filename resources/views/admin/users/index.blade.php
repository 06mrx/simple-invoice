@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'Manage Users')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Users</h2>
                <p class="text-muted-foreground">Manage application users and roles.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full sm:w-64">
                    <x-ui.input name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="pr-16" />
                    <div class="absolute right-2 top-1/2 flex -translate-y-1/2 items-center gap-1">
                        @if (request('search'))
                            <x-ui.button variant="ghost" size="icon" href="{{ route('admin.users.index') }}" class="h-8 w-8 text-muted-foreground hover:text-foreground">&times;</x-ui.button>
                        @endif
                        <x-ui.button type="submit" variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </x-ui.button>
                    </div>
                </form>

                <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-2">
                    <label for="per_page" class="text-xs text-muted-foreground">Show</label>
                    <x-ui.select name="per_page" id="per_page" onchange="this.form.submit()" class="w-20">
                        @foreach ([10, 20, 50, 100] as $val)
                            <option value="{{ $val }}" {{ request('per_page', 10) == $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </x-ui.select>
                </form>

                <x-ui.button href="{{ route('admin.users.create') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add
                </x-ui.button>
            </div>
        </div>

        <x-ui.card>
            <x-ui.table>
                <x-ui.table-header>
                    <x-ui.table-row>
                        <x-ui.table-head>No</x-ui.table-head>
                        <x-ui.table-head>Name</x-ui.table-head>
                        <x-ui.table-head>Email</x-ui.table-head>
                        <x-ui.table-head>Roles</x-ui.table-head>
                        <x-ui.table-head class="text-right">Actions</x-ui.table-head>
                    </x-ui.table-row>
                </x-ui.table-header>
                <x-ui.table-body>
                    @forelse ($users as $user)
                        <x-ui.table-row>
                            <x-ui.table-cell class="text-muted-foreground">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</x-ui.table-cell>
                            <x-ui.table-cell class="font-medium">{{ $user->name }}</x-ui.table-cell>
                            <x-ui.table-cell class="text-muted-foreground">{{ $user->email }}</x-ui.table-cell>
                            <x-ui.table-cell>
                                @forelse ($user->roles as $role)
                                    <x-ui.badge variant="secondary" class="mr-1">{{ $role->name }}</x-ui.badge>
                                @empty
                                    <span class="text-muted-foreground">-</span>
                                @endforelse
                            </x-ui.table-cell>
                            <x-ui.table-cell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <x-ui.button variant="outline" size="sm" href="{{ route('admin.users.edit', $user) }}">Edit</x-ui.button>
                                    <x-ui.dialog>
                                        <x-slot:trigger>
                                            <x-ui.button variant="destructive" size="sm" type="button">Delete</x-ui.button>
                                        </x-slot:trigger>
                                        <div class="space-y-4">
                                            <div>
                                                <h2 class="text-lg font-semibold">Delete user?</h2>
                                                <p class="text-sm text-muted-foreground">This action cannot be undone. Delete {{ $user->name }}?</p>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <x-ui.button variant="outline" type="button" @click="open = false">Cancel</x-ui.button>
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button variant="destructive" type="submit">Delete</x-ui.button>
                                                </form>
                                            </div>
                                        </div>
                                    </x-ui.dialog>
                                </div>
                            </x-ui.table-cell>
                        </x-ui.table-row>
                    @empty
                        <x-ui.table-row>
                            <x-ui.table-cell colspan="5" class="text-center text-muted-foreground">Tidak ada data ditemukan.</x-ui.table-cell>
                        </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
            <div class="p-4">
                {{ $users->links() }}
            </div>
        </x-ui.card>
    </div>
@endsection