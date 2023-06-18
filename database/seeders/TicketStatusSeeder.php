<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Pendiente',
            'color' => '#FFCC00',
            'is_default' => true,
            'order' => 1,
        ],
        [
            'name' => 'Leído',
            'color' => '#6633CC',
            'is_default' => false,
            'order' => 2,
        ],
        [
            'name' => 'En curso',
            'color' => '#3399FF',
            'is_default' => false,
            'order' => 3,
        ],
        [
            'name' => 'En revisión',
            'color' => '#FF6600',
            'is_default' => false,
            'order' => 4,
        ],
        [
            'name' => 'Completado',
            'color' => '#33CC33',
            'is_default' => false,
            'order' => 5,
        ],
        [
            'name' => 'Cancelado',
            'color' => '#FF0000',
            'is_default' => false,
            'order' => 6,
        ],
        [
            'name' => 'Pausado',
            'color' => '#999999',
            'is_default' => false,
            'order' => 7,
        ],
        [
            'name' => 'Devuelto',
            'color' => '#F200F7',
            'is_default' => false,
            'order' => 8,
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
            TicketStatus::query()
                ->firstOrCreate(['name' => $item['name']],$item);
        }
    }
}
