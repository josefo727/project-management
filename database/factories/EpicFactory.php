<?php

namespace Database\Factories;

use App\Models\Epic;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpicFactory extends Factory
{
    protected $model = Epic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'project_id' => Project::factory(),
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'parent_id' => null,
        ];
    }
}
