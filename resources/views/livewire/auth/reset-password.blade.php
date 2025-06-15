    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ __('Reset Password') }}
            </h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Enter your new password below') }}
            </p>
        </div>

        <div>
            @if ($status)
                <div class="p-6">
                    <flux:callout variant="success" title="{{ __('Success') }}" class="mb-4">
                        {{ $status }}
                    </flux:callout>

                    <div class="mt-4">
                        <a href="{{ route('login') }}">
                            <flux:button class="w-full" variant="primary">
                                {{ __('Back to login') }}
                            </flux:button>
                        </a>
                    </div>
                </div>
            @else
                <form wire:submit="resetPassword" class="space-y-6 p-6">
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
                            autocomplete="new-password"
                            required
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
                            {{ __('Reset Password') }}
                        </flux:button>
                    </div>
                </form>
            @endif
        </div>
    </div>
