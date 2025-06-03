    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ __('Verify your email address') }}
            </h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>

        <div>
            <div class="p-6">
                @if ($verificationLinkSent)
                    <flux:callout variant="success" title="{{ __('Success') }}" class="mb-4">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </flux:callout>
                @endif

                <div class="flex items-center justify-between">
                    <form wire:submit="sendVerificationEmail">
                        <flux:button type="submit" variant="primary">
                            {{ __('Resend Verification Email') }}
                        </flux:button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button type="submit" variant="subtle">
                            {{ __('Log Out') }}
                        </flux:button>
                    </form>
                </div>
            </div>
        </div>
    </div>
