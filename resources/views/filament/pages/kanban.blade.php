<x-filament::page>

    <div class="mx-auto w-full" wire:ignore>
        <details class="w-full bg-white open:bg-gray-200 duration-300">
            <summary
                class="relative w-full bg-inherit px-5 py-3 text-base cursor-pointer text-gray-500">
                {{ __('Filters') }}
            </summary>
            <div class="bg-white px-5 py-3">
                <form>
                    {{ $this->form }}
                </form>
            </div>
        </details>
    </div>

    <div class="kanban-container" wire:init="loadBoard">
        @if($boardLoaded)
            @foreach($this->getStatuses() as $status)
                @include('partials.kanban.status')
            @endforeach
        @else
            <div class="w-full flex items-center justify-center py-12">
                <svg class="animate-spin w-8 h-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="ml-2 text-gray-500">{{ __('Loading board...') }}</span>
            </div>
        @endif
    </div>

    @if($boardLoaded)
        @push('scripts')
            <script src="{{ asset('js/Sortable.js') }}"></script>
            <script>

                (() => {
                    let record;
                    @foreach($this->getStatuses() as $status)
                        record = document.querySelector('#status-records-{{ $status['id'] }}');

                        Sortable.create(record, {
                            group: {
                                name: 'status-{{ $status['id'] }}',
                                pull: true,
                                put: true
                            },
                            handle: '.handle',
                            animation: 100,
                            onEnd: function (evt) {
                                Livewire.emit('recordUpdated',
                                    +evt.clone.dataset.id, // id
                                    +evt.newIndex, // newIndex
                                    +evt.to.dataset.status, // newStatus
                                );
                            },
                        })
                    @endforeach
                })();
            </script>
        @endpush
    @endif

</x-filament::page>
