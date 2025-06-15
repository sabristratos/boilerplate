<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('Import Users') }}</flux:heading>
    </div>
    <flux:card>
        <form wire:submit.prevent="import">
            <div>
                <flux:label for="file">{{ __('Upload File') }}</flux:label>
                <input type="file" id="file" wire:model="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4">
                <flux:button type="submit" variant="primary">{{ __('Import Users') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
