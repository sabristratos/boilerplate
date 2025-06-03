<div>
    <flux:heading size="xl">User Profile</flux:heading>

    @if ($successMessage)
        <flux:callout variant="success" class="my-4">
            {{ $successMessage }}
        </flux:callout>
    @endif

    <flux:separator variant="subtle" class="my-8" />

    <!-- Profile Information Section -->
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Profile Information</flux:heading>
            <flux:subheading>Update your account's profile information.</flux:subheading>
        </div>

        <div class="flex-1 space-y-6">
            <form wire:submit="updateProfileInformation">
                <flux:input
                    wire:model="name"
                    label="Name"
                    description="Your full name as it will be displayed on the site."
                    placeholder="John Doe"
                />

                <flux:input
                    wire:model="email"
                    label="Email"
                    description="Your email address for account notifications and communications."
                    placeholder="john@example.com"
                    class="mt-4"
                />

                <div class="flex justify-end mt-6">
                    <flux:button type="submit" variant="primary">Save Profile Information</flux:button>
                </div>
            </form>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <!-- Update Password Section -->
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">Update Password</flux:heading>
            <flux:subheading>Ensure your account is using a secure password.</flux:subheading>
        </div>

        <div class="flex-1 space-y-6">
            <form wire:submit="updatePassword">
                <flux:input
                    wire:model="current_password"
                    label="Current Password"
                    type="password"
                    description="Your current password is required to confirm your changes."
                />

                <flux:input
                    wire:model="password"
                    label="New Password"
                    type="password"
                    description="Your new password must be at least 8 characters long."
                    class="mt-4"
                />

                <flux:input
                    wire:model="password_confirmation"
                    label="Confirm Password"
                    type="password"
                    description="Please confirm your new password."
                    class="mt-4"
                />

                <div class="flex justify-end mt-6">
                    <flux:button type="submit" variant="primary">Update Password</flux:button>
                </div>
            </form>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <!-- Two Factor Authentication Section -->
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 pb-10">
        <div class="w-80">
            <flux:heading size="lg">Two Factor Authentication</flux:heading>
            <flux:subheading>Add additional security to your account using two factor authentication.</flux:subheading>
        </div>

        <div class="flex-1 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text class="text-sm text-gray-600">
                        When two factor authentication is enabled, you will be prompted for a secure, random token during authentication.
                    </flux:text>
                </div>
                <flux:button href="{{ route('two-factor.setup') }}" variant="outline">Manage 2FA</flux:button>
            </div>
        </div>
    </div>
</div>
