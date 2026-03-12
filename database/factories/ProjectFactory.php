<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'owner_id' => User::factory(),
            'status_id' => ProjectStatus::factory(),
            'ticket_prefix' => fake()->unique()->lexify('???'),
            'status_type' => 'default',
            'type' => 'kanban',
        ];
    }

    public function scrum(): static
    {
        return $this->state(fn() => ['type' => 'scrum']);
    }

    public function customStatuses(): static
    {
        return $this->state(fn() => ['status_type' => 'custom']);
    }
}
