
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ __('Forgot your password?') }}
            </h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}
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
                            <flux:button class="w-full" variant="subtle">
                                {{ __('Back to login') }}
                            </flux:button>
                        </a>
                    </div>
                </div>
            @else
                <form wire:submit="sendResetLink" class="space-y-6 p-6">
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

                    <div class="flex items-center justify-between">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                            {{ __('Back to login') }}
                        </a>
                        <flux:button type="submit" variant="primary">
                            {{ __('Email Password Reset Link') }}
                        </flux:button>
                    </div>
                </form>
            @endif
        </div>
    </div>

