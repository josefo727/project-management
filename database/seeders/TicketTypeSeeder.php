<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TicketTypeSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Daily',
            'icon' => 'heroicon-o-clock',
            'color' => '#FF1493',
            'is_default' => false,
        ],
        [
            'name' => 'Reunión de trabajo',
            'icon' => 'heroicon-o-users',
            'color' => '#FF4500',
            'is_default' => false,
        ],
        [
            'name' => 'Tarea',
            'icon' => 'heroicon-o-check-circle',
            'color' => '#00FF00',
            'is_default' => true,
        ],
        [
            'name' => 'Evolución',
            'icon' => 'heroicon-o-clipboard-list',
            'color' => '#FFFF00',
            'is_default' => false,
        ],
        [
            'name' => 'Error',
            'icon' => 'heroicon-o-x',
            'color' => '#FF0000',
            'is_default' => false,
        ],
        [
            'name' => 'Mejora',
            'icon' => 'heroicon-o-sparkles',
            'color' => '#FFA500',
            'is_default' => false,
        ],
        [
            'name' => 'Consulta',
            'icon' => 'heroicon-o-information-circle',
            'color' => '#808080',
            'is_default' => false,
        ],
        [
            'name' => 'Investigación',
            'icon' => 'heroicon-o-search',
            'color' => '#FF8C00',
            'is_default' => false,
        ],
        [
            'name' => 'Mantenimiento',
            'icon' => 'fas-screwdriver-wrench',
            'color' => '#800080',
            'is_default' => false,
        ],
        [
            'name' => 'Soporte',
            'icon' => 'heroicon-o-hand',
            'color' => '#0000FF',
            'is_default' => false,
        ],
        [
            'name' => 'Documentación',
            'icon' => 'heroicon-o-document',
            'color' => '#663399',
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
        Schema::disableForeignKeyConstraints();
        TicketType::query()->truncate();
        Schema::enableForeignKeyConstraints();
        foreach ($this->data as $item) {
            TicketType::query()
                ->firstOrCreate(['name' => $item['name']],$item);
        }
    }
}
