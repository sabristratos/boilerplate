<div>
    <flux:heading size="xl">{{ __('Activity Logs') }}</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <!-- Filters -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input
                label="{{ __('Search') }}"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search in descriptions...') }}"
            />

            <flux:select
                label="{{ __('Log Type') }}"
                wire:model.live="logName"
            >
                <option value="">{{ __('All log types') }}</option>
                @foreach($this->logNames as $name)
                    <option value="{{ $name }}">{{ ucfirst($name) }}</option>
                @endforeach
            </flux:select>

            <flux:select
                label="{{ __('Event') }}"
                wire:model.live="event"
            >
                <option value="">{{ __('All events') }}</option>
                @foreach($this->events as $eventName)
                    <option value="{{ $eventName }}">{{ ucfirst($eventName) }}</option>
                @endforeach
            </flux:select>

            <flux:input
                type="date"
                label="{{ __('From Date') }}"
                wire:model.live="dateFrom"
            />

            <flux:input
                type="date"
                label="{{ __('To Date') }}"
                wire:model.live="dateTo"
            />

            <flux:select
                label="{{ __('Subject Type') }}"
                wire:model.live="subjectType"
            >
                <option value="">{{ __('All subject types') }}</option>
                @foreach($this->subjectTypes as $type)
                    <option value="{{ $type }}">{{ class_basename($type) }}</option>
                @endforeach
            </flux:select>
        </div>

        <div class="flex justify-end mt-4">
            <flux:button
                variant="filled"
                wire:click="resetFilters"
            >
                {{ __('Reset Filters') }}
            </flux:button>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div>
        @if($logs->isEmpty())
            <x-flux.empty-state
                icon="document-text"
                heading="{{ __('No activity logs found') }}"
                description="{{ __('No activity logs match your search criteria.') }}"
            />
        @else
            <flux:table :paginate="$logs">
                <flux:table.columns>
                    <flux:table.column sortable wire:click="sortBy('created_at')" :sorted="$sortField === 'created_at'" :direction="$sortDirection">
                        {{ __('Date & Time') }}
                    </flux:table.column>
                    <flux:table.column sortable wire:click="sortBy('log_name')" :sorted="$sortField === 'log_name'" :direction="$sortDirection">
                        {{ __('Log Type') }}
                    </flux:table.column>
                    <flux:table.column sortable wire:click="sortBy('event')" :sorted="$sortField === 'event'" :direction="$sortDirection">
                        {{ __('Event') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('Description') }}
                    </flux:table.column>
                    <flux:table.column>
                        {{ __('User') }}
                    </flux:table.column>
                    <flux:table.column align="end">
                        {{ __('Actions') }}
                    </flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($logs as $log)
                        <flux:table.row wire:key="log-{{ $log->id }}">
                            <flux:table.cell>
                                <div class="font-medium">
                                    {{ $log->created_at->format('Y-m-d') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->log_name)
                                    <flux:badge color="blue" size="sm">
                                        {{ ucfirst($log->log_name) }}
                                    </flux:badge>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->event)
                                    <flux:badge
                                        color="{{
                                            match($log->event) {
                                                'created' => 'green',
                                                'updated' => 'amber',
                                                'deleted' => 'red',
                                                'login' => 'indigo',
                                                'login_failed' => 'rose',
                                                default => 'zinc'
                                            }
                                        }}"
                                        size="sm"
                                    >
                                        {{ ucfirst($log->event) }}
                                    </flux:badge>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate max-w-xs">
                                    {{ $log->description }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->causer)
                                    <div class="font-medium">
                                        {{ $log->causer->name ?? class_basename($log->causer) . ' #' . $log->causer->id }}
                                    </div>
                                @else
                                    <span class="text-gray-500">{{ __('System') }}</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button
                                    size="sm"
                                    variant="filled"
                                    wire:click="viewDetails({{ $log->id }})"
                                >
                                    {{ __('View Details') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    <!-- Log Details Modal -->
    @if($selectedLog)
        <flux:modal title="{{ __('Activity Log Details') }}" open wire:click="closeDetails">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Date & Time') }}</h3>
                        <p>{{ $selectedLog->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Log Type') }}</h3>
                        <p>{{ ucfirst($selectedLog->log_name ?? 'default') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Event') }}</h3>
                        <p>{{ ucfirst($selectedLog->event ?? 'unknown') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Description') }}</h3>
                        <p>{{ $selectedLog->description }}</p>
                    </div>
                </div>

                <flux:separator />

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('User Information') }}</h3>
                    @if($selectedLog->causer)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">{{ __('User') }}</h4>
                                <p>{{ $selectedLog->causer->name ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">{{ __('Type') }}</h4>
                                <p>{{ class_basename($selectedLog->causer_type) }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">{{ __('ID') }}</h4>
                                <p>{{ $selectedLog->causer_id }}</p>
                            </div>
                        </div>
                    @else
                        <p>{{ __('No user information available') }}</p>
                    @endif
                </div>

                <flux:separator />

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Subject Information') }}</h3>
                    @if($selectedLog->subject_type)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">{{ __('Type') }}</h4>
                                <p>{{ class_basename($selectedLog->subject_type) }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">{{ __('ID') }}</h4>
                                <p>{{ $selectedLog->subject_id }}</p>
                            </div>
                        </div>
                    @else
                        <p>{{ __('No subject information available') }}</p>
                    @endif
                </div>

                <flux:separator />

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Additional Properties') }}</h3>
                    @if($selectedLog->properties && count($selectedLog->properties))
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md overflow-auto max-h-64">
                            <pre class="text-xs">{{ json_encode($selectedLog->properties, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @else
                        <p>{{ __('No additional properties') }}</p>
                    @endif
                </div>

                <flux:separator />

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">{{ __('Request Information') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">{{ __('IP Address') }}</h4>
                            <p>{{ $selectedLog->ip_address ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500">{{ __('User Agent') }}</h4>
                            <p class="truncate">{{ $selectedLog->user_agent ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <flux:button variant="filled" wire:click="closeDetails">
                    {{ __('Close') }}
                </flux:button>
            </x-slot:footer>
        </flux:modal>
    @endif
</div>
