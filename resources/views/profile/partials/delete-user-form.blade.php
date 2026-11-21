<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-foreground">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-muted-foreground">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-ui.dialog :show="$errors->userDeletion->isNotEmpty()">
        <x-slot:trigger>
            <x-ui.button variant="destructive" type="button">
                {{ __('Delete Account') }}
            </x-ui.button>
        </x-slot:trigger>

        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div class="space-y-2">
                <h2 class="text-lg font-semibold">{{ __('Are you sure you want to delete your account?') }}</h2>
                <p class="text-sm text-muted-foreground">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            <div class="space-y-2">
                <x-ui.label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-ui.input
                    id="password"
                    name="password"
                    type="password"
                    class="w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-ui.input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="flex justify-end gap-2">
                <x-ui.button variant="outline" type="button" @click="open = false">
                    {{ __('Cancel') }}
                </x-ui.button>

                <x-ui.button variant="destructive" type="submit">
                    {{ __('Delete Account') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.dialog>
</section>
