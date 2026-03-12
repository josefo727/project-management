<?php

namespace Tests\Feature\Helpers;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class KanbanScrumHelperTest extends TestCase
{
    use DatabaseTransactions;

    private User $owner;
    private Project $project;
    private TicketStatus $status;
    private TicketType $type;
    private TicketPriority $priority;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $projectStatus = ProjectStatus::factory()->create();
        $this->status = TicketStatus::factory()->default()->create();
        $this->type = TicketType::factory()->create(['is_default' => true]);
        $this->priority = TicketPriority::factory()->create(['is_default' => true]);

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'status_id' => $projectStatus->id,
            'type' => 'kanban',
        ]);
    }

    public function test_get_records_returns_project_tickets(): void
    {
        $this->actingAs($this->owner);

        $ticket = Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $otherProject = Project::factory()->create(['owner_id' => $this->owner->id]);
        Ticket::factory()->create([
            'project_id' => $otherProject->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadBoard');

        $records = $component->instance()->getRecords();
        $this->assertCount(1, $records);
        $this->assertEquals($ticket->code, $records->first()['code']);
    }

    public function test_get_records_filters_by_users(): void
    {
        $this->actingAs($this->owner);

        $responsible = User::factory()->create();
        $this->project->users()->attach($responsible->id, ['role' => 'member']);

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'responsible_id' => $responsible->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $otherUser = User::factory()->create();
        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $otherUser->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->set('users', [$responsible->id])
            ->call('filter');

        $records = $component->instance()->getRecords();
        $this->assertCount(1, $records);
        $this->assertEquals($responsible->id, $records->first()['responsible']->id);
    }

    public function test_get_records_filters_by_type(): void
    {
        $this->actingAs($this->owner);

        $specificType = TicketType::factory()->create();

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $specificType->id,
            'priority_id' => $this->priority->id,
        ]);

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->set('types', [$specificType->id])
            ->call('filter');

        $records = $component->instance()->getRecords();
        $this->assertCount(1, $records);
        $this->assertEquals($specificType->id, $records->first()['type']->id);
    }

    public function test_get_records_filters_by_priority(): void
    {
        $this->actingAs($this->owner);

        $specificPriority = TicketPriority::factory()->create();

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $specificPriority->id,
        ]);

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->set('priorities', [$specificPriority->id])
            ->call('filter');

        $records = $component->instance()->getRecords();
        $this->assertCount(1, $records);
        $this->assertEquals($specificPriority->id, $records->first()['priority']->id);
    }

    public function test_get_records_filters_unaffected_tickets(): void
    {
        $this->actingAs($this->owner);

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'responsible_id' => null,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $responsible = User::factory()->create();
        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'responsible_id' => $responsible->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->set('includeNotAffectedTickets', true)
            ->call('filter');

        $records = $component->instance()->getRecords();
        $this->assertCount(1, $records);
        $this->assertNull($records->first()['responsible']);
    }

    public function test_get_records_empty_for_project_without_tickets(): void
    {
        $this->actingAs($this->owner);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadBoard');

        $records = $component->instance()->getRecords();
        $this->assertCount(0, $records);
    }

    public function test_get_statuses_returns_default_statuses(): void
    {
        $this->actingAs($this->owner);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project]);

        $statuses = $component->instance()->getStatuses();
        $this->assertTrue($statuses->contains('id', $this->status->id));
    }

    public function test_get_statuses_returns_custom_statuses(): void
    {
        $this->actingAs($this->owner);

        $customProject = Project::factory()->customStatuses()->create([
            'owner_id' => $this->owner->id,
        ]);

        $customStatus = TicketStatus::factory()->create([
            'project_id' => $customProject->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $customProject]);

        $statuses = $component->instance()->getStatuses();
        $this->assertTrue($statuses->contains('id', $customStatus->id));
        $this->assertFalse($statuses->contains('id', $this->status->id));
    }

    public function test_record_updated_changes_status_and_order(): void
    {
        $this->actingAs($this->owner);

        $newStatus = TicketStatus::factory()->create();

        $ticket = Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('recordUpdated', $ticket->id, 5, $newStatus->id);

        $ticket->refresh();
        $this->assertEquals($newStatus->id, $ticket->status_id);
        $this->assertEquals(5, $ticket->order);
    }

    public function test_filter_clears_cache(): void
    {
        $this->actingAs($this->owner);

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadBoard');

        $recordsBefore = $component->instance()->getRecords();
        $this->assertCount(1, $recordsBefore);

        Ticket::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        $component->call('filter');

        $recordsAfter = $component->instance()->getRecords();
        $this->assertCount(2, $recordsAfter);
    }

    public function test_get_visible_limit_defaults_to_records_per_status(): void
    {
        $this->actingAs($this->owner);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project]);

        $this->assertEquals(20, $component->instance()->getVisibleLimit($this->status->id));
    }

    public function test_load_more_increases_visible_limit(): void
    {
        $this->actingAs($this->owner);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadMore', $this->status->id);

        $this->assertEquals(40, $component->instance()->getVisibleLimit($this->status->id));
    }

    public function test_filter_resets_visible_limits(): void
    {
        $this->actingAs($this->owner);

        $component = Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadMore', $this->status->id)
            ->call('filter');

        $this->assertEquals(20, $component->instance()->getVisibleLimit($this->status->id));
    }

    public function test_load_more_button_shown_when_records_exceed_limit(): void
    {
        $this->actingAs($this->owner);

        Ticket::factory()->count(25)->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'status_id' => $this->status->id,
            'type_id' => $this->type->id,
            'priority_id' => $this->priority->id,
        ]);

        Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadBoard')
            ->assertSee(__('Load more') . ' (5)');
    }
}
