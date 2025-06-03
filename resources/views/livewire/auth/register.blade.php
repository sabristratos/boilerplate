    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ __('Create a new account') }}
            </h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Or') }}
                <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                    {{ __('sign in to your existing account') }}
                </a>
            </p>
        </div>

        <div>
            <form wire:submit="register" class="space-y-6 p-6">
                <div>
                    <flux:input
                        label="{{ __('Name') }}"
                        wire:model="name"
                        type="text"
                        autocomplete="name"
                        required
                        :error="$errors->first('name')"
                    />
                </div>

                <div>
                    <flux:input
                        label="{{ __('Email address') }}"
                        wire:model="email"
                        type="email"
                        autocomplete="email"
                        required
                        :error="$errors->first('email')"
                    />
                </div>

                <div>
                    <flux:input
                        label="{{ __('Password') }}"
                        wire:model="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        :error="$errors->first('password')"
                    />
                </div>

                <div>
                    <flux:input
                        label="{{ __('Confirm Password') }}"
                        wire:model="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    />
                </div>

                <div>
                    <flux:button type="submit" class="w-full" variant="primary">
                        {{ __('Register') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
