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

    @push('scripts')
        <script src="{{ asset('js/Sortable.js') }}"></script>
        <script>
            function initSortable() {
                document.querySelectorAll('.status-container').forEach(function (container) {
                    if (container._sortableInstance) {
                        container._sortableInstance.destroy();
                    }
                    container._sortableInstance = Sortable.create(container, {
                        group: {
                            name: 'status-' + container.dataset.status,
                            pull: true,
                            put: true
                        },
                        handle: '.handle',
                        filter: '.create-record, .load-more-btn',
                        animation: 100,
                        onEnd: function (evt) {
                            Livewire.emit('recordUpdated',
                                +evt.clone.dataset.id,
                                +evt.newIndex,
                                +evt.to.dataset.status,
                            );
                        },
                    });
                });
            }

            Livewire.hook('message.processed', function () {
                if (document.querySelector('.status-container')) {
                    initSortable();
                }
            });
        </script>
    @endpush

</x-filament::page>
