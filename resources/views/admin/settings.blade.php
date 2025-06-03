    <div>
    <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab name="general">{{ __('General') }}</flux:tab>
            <flux:tab name="appearance">{{ __('Appearance') }}</flux:tab>
            <flux:tab name="notifications">{{ __('Notifications') }}</flux:tab>
            <flux:tab name="security">{{ __('Security') }}</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="general">
            <div class="space-y-6 py-4">
                <flux:heading size="lg">{{ __('General Settings') }}</flux:heading>
                <flux:text class="text-sm text-gray-600">
                    {{ __('Configure general system settings and preferences.') }}
                </flux:text>

                <div class="space-y-4">
                    <flux:input label="{{ __('Site Name') }}" placeholder="{{ __('My Awesome Site') }}" />
                    <flux:input label="{{ __('Site Description') }}" placeholder="{{ __('A brief description of your site') }}" />
                    <flux:select label="{{ __('Default Language') }}">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                    </flux:select>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="appearance">
            <div class="space-y-6 py-4">
                <flux:heading size="lg">{{ __('Appearance Settings') }}</flux:heading>
                <flux:text class="text-sm text-gray-600">
                    {{ __('Customize the look and feel of your application.') }}
                </flux:text>

                <div class="space-y-4">
                    <flux:select label="{{ __('Theme') }}">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                        <option value="system">System Default</option>
                    </flux:select>
                    <flux:select label="{{ __('Primary Color') }}">
                        <option value="blue">Blue</option>
                        <option value="green">Green</option>
                        <option value="purple">Purple</option>
                        <option value="red">Red</option>
                    </flux:select>
                    <flux:checkbox label="{{ __('Show Logo in Header') }}" />
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="notifications">
            <div class="space-y-6 py-4">
                <flux:heading size="lg">{{ __('Notification Settings') }}</flux:heading>
                <flux:text class="text-sm text-gray-600">
                    {{ __('Configure how and when you receive notifications.') }}
                </flux:text>

                <div class="space-y-4">
                    <flux:checkbox label="{{ __('Email Notifications') }}" />
                    <flux:checkbox label="{{ __('Browser Notifications') }}" />
                    <flux:checkbox label="{{ __('Mobile Push Notifications') }}" />
                    <flux:select label="{{ __('Notification Frequency') }}">
                        <option value="immediately">Immediately</option>
                        <option value="hourly">Hourly Digest</option>
                        <option value="daily">Daily Digest</option>
                        <option value="weekly">Weekly Digest</option>
                    </flux:select>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="security">
            <div class="space-y-6 py-4">
                <flux:heading size="lg">{{ __('Security Settings') }}</flux:heading>
                <flux:text class="text-sm text-gray-600">
                    {{ __('Configure security settings for your application.') }}
                </flux:text>

                <div class="space-y-4">
                    <flux:checkbox label="{{ __('Require Two-Factor Authentication') }}" />
                    <flux:select label="{{ __('Session Timeout') }}">
                        <option value="15">15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                    </flux:select>
                    <flux:checkbox label="{{ __('Log Failed Login Attempts') }}" />
                </div>
            </div>
        </flux:tab.panel>
    </flux:tab.group>

    <div class="flex justify-end mt-6">
        <flux:button variant="primary">{{ __('Save Settings') }}</flux:button>
    </div>
