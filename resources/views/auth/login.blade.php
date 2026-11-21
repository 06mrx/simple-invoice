<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Welcome back</h1>
            <p class="text-sm text-muted-foreground">Sign in to your account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-sm text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="flex items-center space-x-2">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-input text-primary focus:ring-ring">
                <label for="remember_me" class="text-sm text-muted-foreground">{{ __('Remember me') }}</label>
            </div>

            <div class="flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-muted-foreground underline-offset-4 hover:underline">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
                <x-ui.button type="submit">
                    {{ __('Log in') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-guest-layout>