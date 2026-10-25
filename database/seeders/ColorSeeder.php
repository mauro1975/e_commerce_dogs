<?php

namespace Database\Seeders;

use App\Models\ProductColor;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Arancione', 'name_en' => 'Orange',  'hex' => '#E8832A', 'sort_order' => 1],
            ['name' => 'Rosa',      'name_en' => 'Pink',    'hex' => '#F4A7B9', 'sort_order' => 2],
            ['name' => 'Verde',     'name_en' => 'Green',   'hex' => '#9BC3B1', 'sort_order' => 3],
            ['name' => 'Marrone',   'name_en' => 'Brown',   'hex' => '#8B5E3C', 'sort_order' => 4],
            ['name' => 'Nero',      'name_en' => 'Black',   'hex' => '#1a1a1a', 'sort_order' => 5],
            ['name' => 'Beige',     'name_en' => 'Beige',   'hex' => '#f5f0e8', 'sort_order' => 6],
        ];

        foreach ($colors as $color) {
            ProductColor::updateOrCreate(
                ['name' => $color['name']],
                array_merge($color, ['is_active' => true])
            );
        }
    }
}
