<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'            => 'Maglioncini',
                'name_en'         => 'Sweaters',
                'slug'            => 'maglioncini',
                'description'     => 'Maglioncini e capi in maglia per cane, morbidi e di qualità.',
                'description_en'  => 'Soft, premium knitwear and sweaters for dogs.',
                'sort_order'      => 1,
                'is_active'       => true,
            ],
            [
                'name'            => 'Collari',
                'name_en'         => 'Collars',
                'slug'            => 'collars',
                'description'     => 'Collari premium per cani in vari stili, materiali e colori.',
                'description_en'  => 'Premium dog collars in various styles, materials and colors.',
                'sort_order'      => 2,
                'is_active'       => true,
            ],
            [
                'name'            => 'Guinzagli',
                'name_en'         => 'Leashes',
                'slug'            => 'leashes',
                'description'     => 'Guinzagli resistenti ed eleganti per ogni passeggiata.',
                'description_en'  => 'Durable and stylish leashes for every walk.',
                'sort_order'      => 3,
                'is_active'       => true,
            ],
            [
                'name'            => 'Porta sacchetti',
                'name_en'         => 'Bag Holders',
                'slug'            => 'bag-holders',
                'description'     => 'Porta sacchetti pratici per proprietari responsabili.',
                'description_en'  => 'Convenient poop bag holders for responsible dog owners.',
                'sort_order'      => 4,
                'is_active'       => true,
            ],
            [
                'name'            => 'Sacchetti ricarica',
                'name_en'         => 'Bag Refills',
                'slug'            => 'sacchetti-ricarica',
                'description'     => 'Rotoli e ricariche sacchetti compatibili con i nostri dispenser.',
                'description_en'  => 'Refill rolls compatible with our bag dispensers.',
                'sort_order'      => 5,
                'is_active'       => true,
            ],
            [
                'name'            => 'Coperte',
                'name_en'         => 'Blankets',
                'slug'            => 'coperte',
                'description'     => 'Coperte morbide e calde per casa, auto e viaggi.',
                'description_en'  => 'Soft, warm blankets for home, car and travel.',
                'sort_order'      => 6,
                'is_active'       => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $activeSlugs = array_column($categories, 'slug');
        Category::whereNotIn('slug', $activeSlugs)->update(['is_active' => false]);

        $coats = Category::where('slug', 'coats')->first();
        $demoCoatSlugs = ['winter-puffer-coat', 'raincoat-with-hood'];
        $demoCoats = Product::whereIn('slug', $demoCoatSlugs);
        if ($coats) {
            $demoCoats->update(['category_id' => $coats->id, 'is_active' => false]);
        } else {
            $demoCoats->update(['is_active' => false]);
        }
    }
}