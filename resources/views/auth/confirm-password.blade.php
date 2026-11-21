<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Confirm password</h1>
            <p class="text-sm text-muted-foreground">This is a secure area. Please confirm your password.</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div class="space-y-2">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="flex items-center justify-end">
                <x-ui.button type="submit">
                    {{ __('Confirm') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-guest-layout>