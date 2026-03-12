<?php

namespace App\Helpers;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

trait KanbanScrumHelper
{

    public bool $sortable = true;
    public bool $boardLoaded = false;
    public int $recordsPerStatus = 20;

    public Project|null $project = null;

    public $users = [];
    public $types = [];
    public $priorities = [];
    public $includeNotAffectedTickets = false;

    public bool $ticket = false;

    public array $visibleLimits = [];

    protected ?Collection $cachedRecords = null;
    protected ?Collection $cachedStatuses = null;

    protected function formSchema(): array
    {
        return [
            Grid::make([
                'default' => 2,
                'md' => 6
            ])
                ->schema([
                    Select::make('users')
                        ->label(__('Owners / Responsibles'))
                        ->multiple()
                        ->options(User::all()->pluck('name', 'id')),

                    Select::make('types')
                        ->label(__('Ticket types'))
                        ->multiple()
                        ->options(TicketType::all()->pluck('name', 'id')),

                    Select::make('priorities')
                        ->label(__('Ticket priorities'))
                        ->multiple()
                        ->options(TicketPriority::all()->pluck('name', 'id')),

                    Toggle::make('includeNotAffectedTickets')
                        ->label(__('Show only not affected tickets'))
                        ->columnSpan(2),

                    Placeholder::make('search')
                        ->label(new HtmlString('&nbsp;'))
                        ->content(new HtmlString('
                            <button type="button"
                                    wire:click="filter" wire:loading.attr="disabled"
                                    class="bg-primary-500 px-3 py-2 text-white rounded hover:bg-primary-600
                                    disabled:bg-primary-300">
                                ' . __('Filter') . '
                            </button>
                            <button type="button"
                                    wire:click="resetFilters" wire:loading.attr="disabled"
                                    class="ml-2 bg-gray-800 px-3 py-2 text-white rounded hover:bg-gray-900
                                    disabled:bg-gray-300">
                                ' . __('Reset filters') . '
                            </button>
                        ')),
                ]),
        ];
    }

    public function getStatuses(): Collection
    {
        if ($this->cachedStatuses) {
            return $this->cachedStatuses;
        }

        $query = TicketStatus::query();
        if ($this->project && $this->project->status_type === 'custom') {
            $query->where('project_id', $this->project->id);
        } else {
            $query->whereNull('project_id');
        }
        $query->withCount(['tickets' => function ($query) {
            if ($this->project) {
                $query->where('project_id', $this->project->id);
            }
        }]);

        $this->cachedStatuses = $query->orderBy('order')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'color' => $item->color,
                    'size' => $item->tickets_count,
                    'add_ticket' => $item->is_default && auth()->user()->can('Create ticket')
                ];
            });

        return $this->cachedStatuses;
    }

    public function getRecords(): Collection
    {
        if ($this->cachedRecords) {
            return $this->cachedRecords;
        }

        $query = Ticket::query();
        if ($this->project->type === 'scrum') {
            $query->where('sprint_id', $this->project->currentSprint->id);
        }
        $query->with(['project', 'owner', 'responsible', 'status', 'type', 'priority', 'epic', 'relations.relation:id,code', 'hours']);
        $query->where('project_id', $this->project->id);
        if (sizeof($this->users)) {
            $query->where(function ($query) {
                return $query->whereIn('owner_id', $this->users)
                    ->orWhereIn('responsible_id', $this->users);
            });
        }
        if (sizeof($this->types)) {
            $query->whereIn('type_id', $this->types);
        }
        if (sizeof($this->priorities)) {
            $query->whereIn('priority_id', $this->priorities);
        }
        if ($this->includeNotAffectedTickets) {
            $query->whereNull('responsible_id');
        }
        $this->cachedRecords = $query->get()
            ->map(fn(Ticket $item) => [
                'id' => $item->id,
                'code' => $item->code,
                'title' => $item->name,
                'owner' => $item->owner,
                'type' => $item->type,
                'responsible' => $item->responsible,
                'project' => $item->project,
                'status' => $item->status->id,
                'priority' => $item->priority,
                'epic' => $item->epic,
                'relations' => $item->relations,
                'totalLoggedHours' => $item->totalLoggedSeconds ? $item->totalLoggedHours : null
            ]);

        return $this->cachedRecords;
    }

    public function recordUpdated(int $record, int $newIndex, int $newStatus): void
    {
        $ticket = Ticket::find($record);
        if ($ticket) {
            $ticket->order = $newIndex;
            $ticket->status_id = $newStatus;
            $ticket->save();
            Filament::notify('success', __('Ticket updated'));
            $this->cachedRecords = null;
            $this->cachedStatuses = null;
        }
    }

    public function loadBoard(): void
    {
        $this->boardLoaded = true;
    }

    public function getVisibleLimit(int $statusId): int
    {
        return $this->visibleLimits[$statusId] ?? $this->recordsPerStatus;
    }

    public function loadMore(int $statusId): void
    {
        $this->visibleLimits[$statusId] = $this->getVisibleLimit($statusId) + $this->recordsPerStatus;
    }

    public function isMultiProject(): bool
    {
        return $this->project === null;
    }

    public function filter(): void
    {
        $this->cachedRecords = null;
        $this->cachedStatuses = null;
        $this->visibleLimits = [];
        $this->getRecords();
    }

    public function resetFilters(): void
    {
        $this->form->fill();
        $this->filter();
    }

    public function createTicket(): void
    {
        $this->ticket = true;
    }

    public function closeTicketDialog(bool $refresh): void
    {
        $this->ticket = false;
        if ($refresh) {
            $this->filter();
        }
    }

    protected function kanbanHeading(): string|Htmlable
    {
        $heading = '<div class="w-full flex flex-col gap-1">';
        $heading .= '<a href="' . route('filament.pages.board') . '"
                            class="text-primary-500 text-xs font-medium hover:underline">';
        $heading .= __('Back to board');
        $heading .= '</a>';
        $heading .= '<div class="flex flex-col gap-1">';
        $heading .= '<span>' . __('Kanban');
        if ($this->project) {
            $heading .= ' - ' . $this->project->name . '</span>';
        } else {
            $heading .= '</span><span class="text-xs text-gray-400">'
                . __('Only default statuses are listed when no projects selected')
                . '</span>';
        }
        $heading .= '</div>';
        $heading .= '</div>';
        return new HtmlString($heading);
    }

    protected function scrumHeading(): string|Htmlable
    {
        $heading = '<div class="w-full flex flex-col gap-1">';
        $heading .= '<a href="' . route('filament.pages.board') . '"
                            class="text-primary-500 text-xs font-medium hover:underline">';
        $heading .= __('Back to board');
        $heading .= '</a>';
        $heading .= '<div class="flex flex-col gap-1">';
        $heading .= '<span>' . __('Scrum');
        if ($this->project) {
            $heading .= ' - ' . $this->project->name . '</span>';
        } else {
            $heading .= '</span><span class="text-xs text-gray-400">'
                . __('Only default statuses are listed when no projects selected')
                . '</span>';
        }
        $heading .= '</div>';
        $heading .= '</div>';
        return new HtmlString($heading);
    }

    protected function scrumSubHeading(): string|Htmlable|null
    {
        if ($this->project?->currentSprint) {
            return new HtmlString(
                '<div class="w-full flex flex-col gap-1">'
                . '<div class="w-full flex items-center gap-2">'
                . '<span class="bg-danger-500 px-2 py-1 rounded text-white text-sm">'
                . $this->project->currentSprint->name
                . '</span>'
                . '<span class="text-xs text-gray-400">'
                . __('Started at:') . ' ' . $this->project->currentSprint->started_at->format(__('Y-m-d')) . ' - '
                . __('Ends at:') . ' ' . $this->project->currentSprint->ends_at->format(__('Y-m-d')) . ' - '
                . ($this->project->currentSprint->remaining ?
                    (
                        __('Remaining:') . ' ' . $this->project->currentSprint->remaining . ' ' . __('days'))
                    : ''
                )
                . '</span>'
                . '</div>'
                . ($this->project->nextSprint ? '<span class="text-xs text-primary-500 font-medium">'
                    . __('Next sprint:') . ' ' . $this->project->nextSprint->name . ' - '
                    . __('Starts at:') . ' ' . $this->project->nextSprint->starts_at->format(__('Y-m-d'))
                    . ' (' . __('in') . ' ' . $this->project->nextSprint->starts_at->diffForHumans() . ')'
                    . '</span>'
                    . '</span>' : '')
                . '</div>'
            );
        } else {
            return null;
        }
    }

}
