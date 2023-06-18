<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    private array $data = [
		[
			'name' => 'En planificación',
			'color' => '#6633CC',
			'is_default' => false,
		],
		[
			'name' => 'Pendiente',
			'color' => '#FFCC00',
			'is_default' => true,
		],
		[
			'name' => 'En curso',
			'color' => '#3399FF',
			'is_default' => false,
		],
		[
			'name' => 'Completado',
			'color' => '#33CC33',
			'is_default' => false,
		],
		[
			'name' => 'Cancelado',
			'color' => '#FF0000',
			'is_default' => false,
		],
		[
			'name' => 'Pausado',
			'color' => '#999999',
			'is_default' => false,
		],
		[
			'name' => 'Revisión',
			'color' => '#FF6600',
			'is_default' => false,
		],
		[
			'name' => 'En producción',
			'color' => '#00CC66',
			'is_default' => false,
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
            ProjectStatus::query()
                ->firstOrCreate(['name' => $item['name']],$item);
        }
    }
}
