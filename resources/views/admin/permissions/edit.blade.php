@extends('layouts.admin')
@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission: ' . $permission->name)

@section('content')
    <div class="mx-auto max-w-xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Edit Permission</x-ui.card-title>
                <x-ui.card-description>Update permission name.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        <x-ui.label for="name">Permission Name</x-ui.label>
                        <x-ui.input id="name" name="name" value="{{ old('name', $permission->name) }}" placeholder="e.g. edit-post, delete-user" required />
                    </div>
                    <div class="flex justify-end gap-3">
                        <x-ui.button variant="outline" href="{{ route('admin.permissions.index') }}" type="button">Cancel</x-ui.button>
                        <x-ui.button type="submit">Update Permission</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection