<div x-data="{ saved: false }" @notifications-preferences-saved="saved = true; setTimeout(() => saved = false, 2000)">
    <flux:heading size="xl">{{ __('Notification Preferences') }}</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
        <div class="w-80">
            <flux:heading size="lg">{{ __('Notifications') }}</flux:heading>
            <flux:subheading>{{ __('Manage how you receive notifications.') }}</flux:subheading>
        </div>

        <div class="flex-1 space-y-6">
            <form wire:submit="save" class="space-y-6">
                <flux:fieldset>
                    <flux:legend>{{ __('Channels') }}</flux:legend>
                    <div class="mt-4 space-y-4">
                        <flux:switch wire:model.defer="email_notifications" id="email_notifications" label="{{ __('Email Notifications') }}" description="Receive emails about your account activity." />
                        <flux:switch wire:model.defer="browser_notifications" id="browser_notifications" label="{{ __('Browser Notifications') }}" description="Get in-app notifications." />
                        <flux:switch wire:model.defer="mobile_push_notifications" id="mobile_push_notifications" label="{{ __('Mobile Push Notifications') }}" description="Get push notifications on your mobile device."/>
                    </div>
                </flux:fieldset>

                <flux:separator />

                <flux:select
                    wire:model.defer="notification_frequency"
                    label="{{ __('Frequency') }}"
                    description="{{ __('Choose how often you want to receive digest notifications.') }}"
                >
                    <flux:select.option value="immediately">{{ __('Immediately') }}</flux:select.option>
                    <flux:select.option value="daily">{{ __('Daily Digest') }}</flux:select.option>
                    <flux:select.option value="weekly">{{ __('Weekly Digest') }}</flux:select.option>
                </flux:select>

                <div class="flex justify-end mt-6">
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
