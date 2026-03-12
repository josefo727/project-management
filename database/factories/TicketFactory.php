<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'owner_id' => User::factory(),
            'responsible_id' => null,
            'status_id' => TicketStatus::factory(),
            'project_id' => Project::factory(),
            'type_id' => TicketType::factory(),
            'priority_id' => TicketPriority::factory(),
            'epic_id' => null,
            'sprint_id' => null,
            'estimation' => null,
        ];
    }

    public function withResponsible(?User $user = null): static
    {
        return $this->state(fn() => [
            'responsible_id' => $user ?? User::factory(),
        ]);
    }
}
