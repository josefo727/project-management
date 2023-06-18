<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    private array $data = [
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
        foreach ($this->data as $item) {
            Activity::query()
                ->firstOrCreate(['name' => $item['name']],$item);
        }
    }
}
