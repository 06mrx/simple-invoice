@extends('layouts.admin')
@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Profile</h2>
            <p class="text-muted-foreground">Manage your account settings.</p>
        </div>

        <div class="grid gap-6">
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>Profile Information</x-ui.card-title>
                    <x-ui.card-description>Update your account's profile information and email address.</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>Update Password</x-ui.card-title>
                    <x-ui.card-description>Ensure your account is using a long, random password.</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </x-ui.card-content>
            </x-ui.card>

            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>Delete Account</x-ui.card-title>
                    <x-ui.card-description>Permanently delete your account.</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </div>
@endsection