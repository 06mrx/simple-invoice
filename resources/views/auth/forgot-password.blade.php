<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Forgot password</h1>
            <p class="text-sm text-muted-foreground">Enter your email to receive a reset link</p>
        </div>

        <div class="text-sm text-muted-foreground">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-sm text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="flex items-center justify-end">
                <x-ui.button type="submit">
                    {{ __('Email Password Reset Link') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-guest-layout>