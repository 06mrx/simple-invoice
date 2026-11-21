@extends('layouts.admin')
@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Create User</x-ui.card-title>
                <x-ui.card-description>Add a new user account.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <x-ui.label for="name">Name</x-ui.label>
                        <x-ui.input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required />
                        @error('name')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="email">Email</x-ui.label>
                        <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="e.g. john@example.com" required />
                        @error('email')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="password">Password</x-ui.label>
                        <x-ui.input id="password" name="password" type="password" placeholder="Minimum 6 characters" required />
                        @error('password')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label>Assign Roles</x-ui.label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($roles as $role)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="h-4 w-4 rounded border-input text-primary focus:ring-ring">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('roles')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <x-ui.button variant="outline" href="{{ route('admin.users.index') }}" type="button">Cancel</x-ui.button>
                        <x-ui.button type="submit">Save User</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection