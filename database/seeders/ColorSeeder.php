<?php

namespace Database\Seeders;

use App\Models\ProductColor;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Anice',          'name_en' => 'Anise',          'hex' => '#E8EDE3', 'sort_order' => 1],
            ['name' => 'Verde Matcha',   'name_en' => 'Matcha Green',   'hex' => '#8FAF8A', 'sort_order' => 2],
            ['name' => 'Rosa Petalo',    'name_en' => 'Petal Pink',     'hex' => '#F2C4CB', 'sort_order' => 3],
            ['name' => 'Rosa Corallo',   'name_en' => 'Coral Pink',     'hex' => '#F88379', 'sort_order' => 4],
            ['name' => 'Lampone',        'name_en' => 'Raspberry',      'hex' => '#C72C48', 'sort_order' => 5],
            ['name' => 'Melone',         'name_en' => 'Melon',          'hex' => '#FFB07C', 'sort_order' => 6],
            ['name' => 'Nocciola',       'name_en' => 'Hazelnut',       'hex' => '#C4A67A', 'sort_order' => 7],
            ['name' => 'Verde Carciofo', 'name_en' => 'Artichoke Green','hex' => '#6B7F5C', 'sort_order' => 8],
            ['name' => 'Lavanda',        'name_en' => 'Lavender',       'hex' => '#B8A9C9', 'sort_order' => 9],
            ['name' => 'Bubblegum',      'name_en' => 'Bubblegum',      'hex' => '#FF8EC7', 'sort_order' => 10],
            ['name' => 'Grigio/Verde Giada',  'name_en' => 'Grey/Jade Green',  'hex' => '#98A89E', 'sort_order' => 11],
            ['name' => 'Grigio/Rosa Petalo',   'name_en' => 'Grey/Petal Pink',  'hex' => '#C8B8BC', 'sort_order' => 12],
            ['name' => 'Grigio/Verde Matcha',  'name_en' => 'Grey/Matcha Green','hex' => '#A5AD9E', 'sort_order' => 13],
        ];

        $activeNames = array_column($colors, 'name');

        ProductColor::whereNotIn('name', $activeNames)->update(['is_active' => false]);

        foreach ($colors as $color) {
            ProductColor::updateOrCreate(
                ['name' => $color['name']],
                array_merge($color, ['is_active' => true])
            );
        }
    }
}
