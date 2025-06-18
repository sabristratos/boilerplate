<div x-data="{}">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <flux:heading size="xl">{{ $config->getEntityPluralName() }}</flux:heading>
                <flux:text class="mt-2">{{ __('Manage all :name', ['name' => strtolower($config->getEntityPluralName())]) }}</flux:text>
            </div>
            @can($config->getPermissionPrefix() . '.create')
                <flux:button :href="route('admin.crud.create', ['alias' => $alias])" variant="primary">
                    {{ __('New :entity_name', ['entity_name' => $config->getEntityName()]) }}
                </flux:button>
            @endcan
            @foreach($globalActions as $action)
                @can($action['permission'] ?? null)
                    <flux:button
                        wire:click="handleAction('{{ $action['method'] }}')"
                        :variant="$action['variant'] ?? 'secondary'"
                    >
                        {{ __($action['label']) }}
                    </flux:button>
                @endcan
            @endforeach
        </div>

        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-grow md:flex-grow-0 md:w-80">
                <flux:input wire:model.live.debounce.500ms="search" :placeholder="__('Search...')" />
            </div>
            @foreach($config->getFilters() as $field => $filter)
                <div class="flex-grow md:flex-grow-0">
                    @switch($filter['type'] ?? 'select')
                        @case('select')
                            <flux:select wire:model.live.debounce.150ms="filters.{{ $field }}">
                                <option value="">{{ __($filter['label']) }} (All)</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            @break
                        @case('boolean')
                            <flux:select wire:model.live.debounce.150ms="filters.{{ $field }}">
                                <option value="">{{ __($filter['label']) }} (All)</option>
                                <option value="1">{{ __('Yes') }}</option>
                                <option value="0">{{ __('No') }}</option>
                            </flux:select>
                            @break
                        @case('date')
                            <flux:field :label="__($filter['label'])">
                                <flux:input type="date" wire:model.live.debounce.150ms="filters.{{ $field }}" />
                            </flux:field>
                            @break
                    @endswitch
                </div>
            @endforeach
            <div class="flex-grow md:flex-grow-0">
                <flux:select wire:model.live.debounce.150ms="perPage">
                    <option value="10">10 {{ __('per page') }}</option>
                    <option value="25">25 {{ __('per page') }}</option>
                    <option value="50">50 {{ __('per page') }}</option>
                </flux:select>
            </div>
            @php
                $isFiltered = $search || collect($filters)->filter()->isNotEmpty();
            @endphp
            <div>
                <flux:button wire:click="resetFilters" variant="ghost" :disabled="!$isFiltered">
                    {{ __('Reset') }}
                </flux:button>
            </div>
        </div>
        <flux:table :paginate="$items">
            <flux:table.columns>
                @foreach($config->getTableColumns() as $column)
                    @if($column['sortable'] ?? false)
                        <flux:table.column
                            sortable
                            :direction="$sortBy === $column['key'] ? $sortDirection : null"
                            wire:click="sort('{{ $column['key'] }}')"
                        >
                            {{ __($column['label']) }}
                        </flux:table.column>
                    @else
                        <flux:table.column>
                            {{ __($column['label']) }}
                        </flux:table.column>
                    @endif
                @endforeach
                <flux:table.column />
            </flux:table.columns>
            <flux:table.rows>
                @forelse($items as $item)
                    <flux:table.row :key="$item->id">
                        @foreach($config->getTableColumns() as $column)
                            <flux:table.cell>
                                @if(isset($column['render']))
                                    {!! Blade::render($column['render'], ['item' => $item, 'column' => $column]) !!}
                                @else
                                    @switch($column['type'] ?? 'default')
                                        @case('image')
                                            <img src="{{ data_get($item, $column['key']) }}" alt="{{ $item->name }}" class="h-10 w-10 rounded-full object-cover">
                                            @break
                                        @case('badge')
                                            @php
                                                $value = data_get($item, $column['key']);
                                                $colorKey = $value;
                                                $labelText = $value;

                                                if (is_null($value) && ($column['key'] === 'email_verified_at')) {
                                                    $colorKey = false;
                                                    $labelText = __('No');
                                                } elseif ($value instanceof \Illuminate\Support\Carbon) {
                                                    $colorKey = !is_null($value);
                                                    $labelText = $colorKey ? __('Yes') : __('No');
                                                } elseif ($value instanceof \UnitEnum) {
                                                    $colorKey = $value->value;
                                                    $labelText = $value->getLabel();
                                                } elseif (is_bool($value)) {
                                                    $labelText = $value ? __('Yes') : __('No');
                                                }

                                                $color = $column['colors'][$colorKey] ?? 'zinc';
                                            @endphp
                                            <flux:badge :color="$color">
                                                {{ $labelText }}
                                            </flux:badge>
                                            @break
                                        @default
                                            {{ data_get($item, $column['key']) }}
                                    @endswitch
                                @endif
                            </flux:table.cell>
                        @endforeach
                        <flux:table.cell>
                            <div class="flex justify-end">
                                <flux:dropdown>
                                    <flux:button variant="ghost" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        @can($config->getPermissionPrefix() . '.update')
                                            <flux:menu.item
                                                wire:navigate
                                                :href="route('admin.crud.edit', ['alias' => $alias, 'id' => $item->id])"
                                                icon="pencil"
                                            >
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                        @endcan
                                        @foreach($rowActions as $action)
                                            @can($action['permission'] ?? null, $item)
                                                <flux:menu.item
                                                    wire:click="handleAction('{{ $action['method'] }}', {{ $item->id }})"
                                                    :icon="$action['icon'] ?? null"
                                                    :variant="$action['variant'] ?? 'default'"
                                                >
                                                    {{ __($action['label']) }}
                                                </flux:menu.item>
                                            @endcan
                                        @endforeach
                                        @can($config->getPermissionPrefix() . '.delete')
                                            <flux:menu.item
                                                wire:click="$dispatch('confirm-delete', { id: {{ $item->id }} })"
                                                icon="trash"
                                                variant="danger"
                                            >
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ count($config->getTableColumns()) + 1 }}">
                            <x-empty-state
                                :title="__('No :name found', ['name' => $config->getEntityPluralName()])"
                                :description="__('There are no :name to display.', ['name' => strtolower($config->getEntityPluralName())])"
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal name="confirm-delete-modal" :dismissible="false">
        <flux:card class="p-8">
            <div class="space-y-2">
                <flux:heading size="lg">{{ __('Are you sure?') }}</flux:heading>
                <flux:text>{{ __('This action cannot be undone.') }}</flux:text>
            </div>
            <div class="pt-6 flex justify-end gap-x-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger">{{ __('Delete') }}</flux:button>
            </div>
        </flux:card>
    </flux:modal>
</div> 