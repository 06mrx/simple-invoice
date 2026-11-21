@extends('layouts.admin')
@section('title', 'Create Permission')
@section('page-title', 'Create New Permission')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Create Permission</x-ui.card-title>
                <x-ui.card-description>Add a new permission.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('admin.permissions.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <x-ui.label for="name">Permission Name</x-ui.label>
                        <x-ui.input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. edit-post, delete-user" required />
                    </div>
                    <div class="flex justify-end gap-3">
                        <x-ui.button variant="outline" href="{{ route('admin.permissions.index') }}" type="button">Cancel</x-ui.button>
                        <x-ui.button type="submit">Save Permission</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection