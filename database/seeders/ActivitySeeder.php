<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ActivitySeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Planificación',
            'description' => 'Actividades relacionadas con la planificación',
        ],
        [
            'name' => 'Reuniones',
            'description' => 'Actividades relacionadas con los dailies y las reuniones de trabajo',
        ],
        [
            'name' => 'Programación',
            'description' => 'Actividades relacionadas con la programación',
        ],
        [
            'name' => 'Pruebas',
            'description' => 'Actividades relacionadas con las pruebas',
        ],
        [
            'name' => 'Aprendizaje',
            'description' => 'Actividades relacionadas con el aprendizaje y la formación',
        ],
        [
            'name' => 'Investigación',
            'description' => 'Actividades relacionadas con la investigación',
        ],
        [
            'name' => 'Otro',
            'description' => 'Otras actividades',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Activity::query()->truncate();
        Schema::enableForeignKeyConstraints();
        foreach ($this->data as $item) {
            Activity::query()
                ->firstOrCreate(['name' => $item['name']],$item);
        }
    }
}
