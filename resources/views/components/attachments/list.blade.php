@props(['attachments', 'showActions' => true, 'emptyMessage' => 'No attachments found.'])

<div {{ $attributes }}>
    @if($attachments->isEmpty())
        <flux:empty-state
            icon="document"
            heading="{{ __('No attachments') }}"
            description="{{ __($emptyMessage) }}"
        />
    @else
        <div class="space-y-3">
            @foreach($attachments as $attachment)
                <x-attachments.item
                    :attachment="$attachment"
                    :showActions="$showActions"
                >
                    @if(isset($actions))
                        <x-slot:actions>
                            {{ $actions($attachment) }}
                        </x-slot>
                    @endif
                </x-attachments.item>
            @endforeach
        </div>

        @if($attachments instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">
                {{ $attachments->links() }}
            </div>
        @endif
    @endif
</div>
