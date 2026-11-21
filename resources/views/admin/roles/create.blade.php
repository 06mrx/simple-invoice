@extends('layouts.admin')
@section('title', 'Create Role')
@section('page-title', 'Create New Role')

@section('content')
    <div class="mx-auto max-w-2xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Create Role</x-ui.card-title>
                <x-ui.card-description>Define a new role and assign permissions.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <x-ui.label for="name">Role Name</x-ui.label>
                        <x-ui.input id="name" name="name" placeholder="e.g. Editor, Admin" required />
                    </div>
                    <div class="space-y-2">
                        <x-ui.label>Assign Permissions</x-ui.label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($permissions as $permission)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="h-4 w-4 rounded border-input text-primary focus:ring-ring">
                                    {{ $permission->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <x-ui.button variant="outline" href="{{ route('admin.roles.index') }}" type="button">Cancel</x-ui.button>
                        <x-ui.button type="submit">Save Role</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection