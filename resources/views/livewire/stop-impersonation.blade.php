<div>
    @auth
        @if (session()->has('impersonator_id'))
            <div class="fixed bottom-0 left-0 right-0 z-50 bg-danger-600 px-4 py-2 text-white shadow-lg">
                <div class="container mx-auto flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <flux:icon name="identification" class="h-6 w-6" />
                        <p>
                            <span class="font-semibold">{{ __('Impersonating:') }}</span>
                            <span class="font-normal">{{ auth()->user()->name }}</span>
                            <span class="mx-2 text-sm opacity-75">|</span>
                            <span class="text-sm">{{ __('Return to your account:') }}</span>
                        </p>
                    </div>
                    <flux:button wire:click="stopImpersonating" color="light" size="sm">
                        {{ __('Stop Impersonating') }}
                    </flux:button>
                </div>
            </div>
        @endif
    @endauth
</div> 