<?php

namespace Tests\Feature\Filament\Pages;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class KanbanTest extends TestCase
{
    use DatabaseTransactions;

    private User $owner;
    private User $member;
    private User $outsider;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->member = User::factory()->create();
        $this->outsider = User::factory()->create();

        $projectStatus = ProjectStatus::factory()->create();
        TicketStatus::factory()->default()->create();
        TicketType::factory()->create(['is_default' => true]);
        TicketPriority::factory()->create(['is_default' => true]);

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'status_id' => $projectStatus->id,
            'type' => 'kanban',
        ]);

        $this->project->users()->attach($this->member->id, ['role' => 'member']);
    }

    public function test_owner_can_access_kanban(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get(route('filament.pages.kanban/{project}', ['project' => $this->project]));

        $response->assertSuccessful();
    }

    public function test_member_can_access_kanban(): void
    {
        $this->actingAs($this->member);

        $response = $this->get(route('filament.pages.kanban/{project}', ['project' => $this->project]));

        $response->assertSuccessful();
    }

    public function test_non_member_gets_403(): void
    {
        $this->actingAs($this->outsider);

        $response = $this->get(route('filament.pages.kanban/{project}', ['project' => $this->project]));

        $response->assertForbidden();
    }

    public function test_scrum_project_redirects_to_scrum(): void
    {
        $scrumProject = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'type' => 'scrum',
        ]);

        $this->actingAs($this->owner);

        $response = $this->get(route('filament.pages.kanban/{project}', ['project' => $scrumProject]));

        $response->assertRedirect(route('filament.pages.scrum/{project}', ['project' => $scrumProject]));
    }

    public function test_board_loaded_starts_false(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->assertSet('boardLoaded', false);
    }

    public function test_load_board_sets_loaded_true(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(\App\Filament\Pages\Kanban::class, ['project' => $this->project])
            ->call('loadBoard')
            ->assertSet('boardLoaded', true);
    }
}
