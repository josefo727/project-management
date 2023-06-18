<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Baja',
            'color' => '#00FF00',
            'is_default' => false,
        ],
        [
            'name' => 'Normal',
            'color' => '#FFFF00',
            'is_default' => true,
        ],
        [
            'name' => 'Alta',
            'color' => '#FFA500',
            'is_default' => false,
        ],
        [
            'name' => 'Urgente',
            'color' => '#FF0000',
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
            TicketPriority::query()
                ->firstOrCreate(['name' => $item['name']],$item);
        }
    }
}
