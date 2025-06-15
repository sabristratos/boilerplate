    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ __('Sign in to your account') }}
            </h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Or') }}
                <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-500">
                    {{ __('create a new account') }}
                </a>
            </p>
        </div>

        <div>
            <form wire:submit="login" class="space-y-6 p-6">
                <div>
                    <flux:input
                        label="{{ __('Email address') }}"
                        wire:model="email"
                        type="email"
                        autocomplete="email"
                        required
                    />
                </div>

                <div>
                    <flux:input
                        label="{{ __('Password') }}"
                        wire:model="password"
                        type="password"
                        autocomplete="current-password"
                        required
                    />
                </div>

                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="remember" label="{{ __('Remember me') }}" />

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:text-primary-500">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                </div>

                <div>
                    <flux:button type="submit" class="w-full" variant="primary">
                        {{ __('Sign in') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
