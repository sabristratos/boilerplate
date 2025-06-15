<x-layouts.frontend :title="'Home'">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="space-y-8 max-w-2xl mx-auto p-4 sm:p-6">

            {{-- Page Header --}}
            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-5">
                <x-heading :level="1" variant="title-xl" color="accent">
                    Account Settings
                </x-heading>
                <x-text color="muted" class="mt-2">
                    Manage your profile, login credentials, and security settings.
                </x-text>
            </div>

            {{-- Form sections --}}
            <form action="#" method="POST" class="space-y-10">

                {{-- Profile Information Section --}}
                <div class="space-y-4">
                    <x-heading :level="2" variant="title-lg">Profile Information</x-heading>
                    <x-input name="username" label="Username" placeholder="your_username" icon="user" />

                    <x-input name="email" label="Email Address" type="email" placeholder="you@example.com" icon="envelope" helper-text="Used for login and notifications." />
                    <x-select name="country" label="Country" icon="globe-alt">
                        <option>United States</option>
                        <option>Canada</option>
                        <option>Mexico</option>
                    </x-select>

                    <x-switch name="notifications" label="Enable Notifications" checked />

                    <x-textarea name="bio" label="Your Bio" placeholder="Tell us a little about yourself." helper-text="This will appear on your public profile." />
                </div>


                {{-- Security Section --}}
                <div class="space-y-4">
                    <x-heading :level="2" variant="title-lg">Update Password</x-heading>
                    <x-input name="password" label="New Password" type="password" />
                    <x-input name="password_confirmation" label="Confirm New Password" type="password" />
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-x-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <x-button variant="ghost">Cancel</x-button>
                    <x-button type="submit">Save Changes</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.frontend>
