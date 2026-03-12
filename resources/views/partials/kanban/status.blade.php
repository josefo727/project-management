<div class="kanban-statuses">
    <div class="status-header"
         style="border-color: {{ $status['color'] }}66;">
        <span>{{ $status['title'] }}</span>
        @if($status['size'])
            {{ $status['size'] }} {{ __($status['size'] > 1 ? 'tickets' : 'ticket') }}
        @endif
    </div>
    @php
        $statusRecords = $this->getRecords()->where('status', $status['id'])->values();
        $totalRecords = $statusRecords->count();
        $visibleLimit = $this->getVisibleLimit($status['id']);
        $visibleRecords = $statusRecords->take($visibleLimit);
        $remaining = $totalRecords - $visibleLimit;
    @endphp
    <div class="status-container"
         data-status="{{ $status['id'] }}"
         id="status-records-{{ $status['id'] }}"
         style="border-color: {{ $status['color'] }}66;">
        @foreach($visibleRecords as $record)
            @include('partials.kanban.record')
        @endforeach

        @if($remaining > 0)
            <button type="button"
                    wire:click="loadMore({{ $status['id'] }})"
                    class="load-more-btn w-full py-2 text-xs text-primary-500 hover:text-primary-700 font-medium text-center">
                {{ __('Load more') }} ({{ $remaining }})
            </button>
        @endif

        @if($status['add_ticket'])
            <a class="create-record hover:cursor-pointer"
               wire:click="createTicket"
               target="_blank">
                <x-heroicon-o-plus class="w-4 h-4" /> {{ __('Create ticket') }}
            </a>

            @if($ticket)
                <div class="dialog-container">
                    <div class="dialog dialog-xl">
                        <div class="dialog-header">
                            {{ __('Create ticket') }}
                        </div>
                        <div class="dialog-content">
                            @livewire('road-map.issue-form', ['project' => null])
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
