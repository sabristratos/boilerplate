<flux:card class="space-y-4">
    <flux:heading size="lg">Two Factor Authentication</flux:heading>
    <flux:text class="text-sm text-gray-600">
        Add additional security to your account using two factor authentication.
    </flux:text>

    @if ($message)
        <flux:callout variant="success" class="mt-4">
            {{ $message }}
        </flux:callout>
    @endif

    @if ($error)
        <flux:callout variant="danger" class="mt-4">
            {{ $error }}
        </flux:callout>
    @endif

    <div class="mt-5">
        @if (! $user->hasTwoFactorEnabled())
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="md">You have not enabled two factor authentication.</flux:heading>
                    <flux:text class="mt-1 text-sm text-gray-600">
                        When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                    </flux:text>
                </div>
                <flux:button wire:click="enableTwoFactorAuthentication" variant="primary">
                    Enable
                </flux:button>
            </div>
        @else
            <div>
                <flux:heading size="md">You have enabled two factor authentication.</flux:heading>
                <flux:text class="mt-1 text-sm text-gray-600">
                    Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
                </flux:text>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div>
                    <flux:button wire:click="showRecoveryCodes" variant="outline" class="mr-2">
                        Show Recovery Codes
                    </flux:button>
                    <flux:button wire:click="regenerateRecoveryCodes" variant="outline">
                        Regenerate Recovery Codes
                    </flux:button>
                </div>
                <flux:button wire:click="disableTwoFactorAuthentication" variant="danger">
                    Disable
                </flux:button>
            </div>
        @endif

        @if ($showingQrCode)
            <flux:card class="mt-4 bg-gray-50">
                <flux:text class="font-medium">
                    Two factor authentication is not enabled yet. Scan the following QR code using your phone's authenticator application and enter the verification code to enable two factor authentication.
                </flux:text>
                <div class="mt-4">
                    {!! $user->twoFactorQrCodeSvg() !!}
                </div>
                <div class="mt-4">
                    <flux:input
                        wire:model="code"
                        label="Verification Code"
                        placeholder="Enter the code from your authenticator app"
                    />
                </div>
                <div class="mt-4">
                    <flux:button wire:click="confirmTwoFactorAuthentication" variant="primary">
                        Confirm
                    </flux:button>
                </div>
            </flux:card>
        @endif

        @if ($showingRecoveryCodes)
            <flux:card class="mt-4 bg-gray-50">
                <flux:text class="font-medium">
                    Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                </flux:text>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($user->two_factor_recovery_codes as $code)
                        <div class="p-2 bg-white rounded-md font-mono text-sm">{{ $code }}</div>
                    @endforeach
                </div>
            </flux:card>
        @endif
    </div>
</flux:card>
