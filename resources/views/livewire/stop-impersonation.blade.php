<div class="flex items-center justify-center space-x-4">
    <flux:icon name="exclamation-circle" class="h-6 w-6" />
    <span>
        {{ __('You are currently impersonating :name.', ['name' => auth()->user()->name]) }}
        <span class="font-bold">({{ __('Originally logged in as :name', ['name' => session('impersonator_name')]) }})</span>.
    </span>
    <flux:button wire:click="stopImpersonating" variant="danger" size="sm">
        {{ __('Stop Impersonating') }}
    </flux:button>
</div> 