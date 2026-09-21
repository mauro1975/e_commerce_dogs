<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'            => 'Collari',
                'name_en'         => 'Collars',
                'slug'            => 'collars',
                'description'     => 'Collari premium per cani in vari stili, materiali e colori.',
                'description_en'  => 'Premium dog collars in various styles, materials and colors.',
                'sort_order'      => 1,
                'is_active'       => true,
            ],
            [
                'name'            => 'Guinzagli',
                'name_en'         => 'Leashes',
                'slug'            => 'leashes',
                'description'     => 'Guinzagli resistenti ed eleganti per ogni passeggiata.',
                'description_en'  => 'Durable and stylish leashes for every walk.',
                'sort_order'      => 2,
                'is_active'       => true,
            ],
            [
                'name'            => 'Cappotti',
                'name_en'         => 'Coats',
                'slug'            => 'coats',
                'description'     => 'Cappotti caldi e di moda per il comfort del tuo cane.',
                'description_en'  => 'Warm and fashionable coats to keep your dog comfortable.',
                'sort_order'      => 3,
                'is_active'       => true,
            ],
            [
                'name'            => 'Pettorine',
                'name_en'         => 'Harnesses',
                'slug'            => 'harnesses',
                'description'     => 'Pettorine comode per un controllo e una sicurezza migliori.',
                'description_en'  => 'Comfortable harnesses for better control and safety.',
                'sort_order'      => 4,
                'is_active'       => true,
            ],
            [
                'name'            => 'Porta sacchetti',
                'name_en'         => 'Bag Holders',
                'slug'            => 'bag-holders',
                'description'     => 'Porta sacchetti pratici per proprietari responsabili.',
                'description_en'  => 'Convenient poop bag holders for responsible dog owners.',
                'sort_order'      => 5,
                'is_active'       => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
