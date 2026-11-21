@extends('layouts.admin')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Edit User</x-ui.card-title>
                <x-ui.card-description>Update user account details.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <x-ui.label for="name">Name</x-ui.label>
                        <x-ui.input id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="e.g. John Doe" required />
                        @error('name')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="email">Email</x-ui.label>
                        <x-ui.input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="e.g. john@example.com" required />
                        @error('email')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="password">Password (Leave blank to keep current)</x-ui.label>
                        <x-ui.input id="password" name="password" type="password" placeholder="Minimum 6 characters" />
                        @error('password')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label>Assign Roles</x-ui.label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($roles as $role)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                        class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                                        {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray() ?? [])) ? 'checked' : '' }}>
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('roles')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <x-ui.button variant="outline" href="{{ route('admin.users.index') }}" type="button">Cancel</x-ui.button>
                        <x-ui.button type="submit">Update User</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection