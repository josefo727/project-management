<?php

namespace Database\Factories;

use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketStatusFactory extends Factory
{
    protected $model = TicketStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'color' => fake()->hexColor(),
            'is_default' => false,
            'order' => 0,
            'project_id' => null,
        ];
    }

    public function default(): static
    {
        return $this->state(fn() => ['is_default' => true]);
    }
}
