<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $collars    = Category::where('slug', 'collars')->first();
        $leashes    = Category::where('slug', 'leashes')->first();
        $coats      = Category::where('slug', 'coats')->first();
        $harnesses  = Category::where('slug', 'harnesses')->first();
        $bagHolders = Category::where('slug', 'bag-holders')->first();

        $colorSets = [
            'classic' => json_encode([
                ['name' => 'Black',  'hex' => '#1a1a1a'],
                ['name' => 'Brown',  'hex' => '#8B4513'],
                ['name' => 'Red',    'hex' => '#e74c3c'],
            ]),
            'pastels' => json_encode([
                ['name' => 'Mint',   'hex' => '#9ad7a0'],
                ['name' => 'Blush',  'hex' => '#f4a7b9'],
                ['name' => 'Sky',    'hex' => '#87ceeb'],
            ]),
            'neutral' => json_encode([
                ['name' => 'Beige',  'hex' => '#f5f0e8'],
                ['name' => 'Grey',   'hex' => '#999999'],
                ['name' => 'Navy',   'hex' => '#1a237e'],
            ]),
        ];

        $sizes = json_encode(['XS', 'S', 'M', 'L', 'XL']);
        $collarSizes = json_encode(['XS', 'S', 'M', 'L']);

        $products = [
            // Collars
            [
                'category_id'       => $collars?->id,
                'name'              => 'Collare in pelle vera',
                'name_en'           => 'Genuine Leather Collar',
                'slug'              => 'genuine-leather-collar',
                'description'       => 'Realizzato a mano in pelle pieno fiore di prima qualità. Morbido sul collo del cane, resistente per l\'uso quotidiano. Fibbia e anello a D in ottone.',
                'description_en'    => 'Handcrafted from premium full-grain leather. Soft on your dog\'s neck, durable for everyday use. Brass buckle and D-ring.',
                'price'             => 29.99,
                'available_colors'  => $colorSets['classic'],
                'available_sizes'   => $collarSizes,
                'is_active'         => true,
                'is_featured'       => true,
                'is_best_seller'    => true,
                'discount_percent'  => 0,
            ],
            [
                'category_id'       => $collars?->id,
                'name'              => 'Collare nylon pastello',
                'name_en'           => 'Pastel Nylon Collar',
                'slug'              => 'pastel-nylon-collar',
                'description'       => 'Collare in nylon leggero e idrorepellente in tonalità pastello. Ideale per cani attivi.',
                'description_en'    => 'Lightweight and water-resistant nylon collar in beautiful pastel tones. Perfect for active dogs.',
                'price'             => 14.99,
                'available_colors'  => $colorSets['pastels'],
                'available_sizes'   => $collarSizes,
                'is_active'         => true,
                'is_featured'       => false,
                'is_best_seller'    => false,
                'discount_percent'  => 10,
            ],
            // Leashes
            [
                'category_id'       => $leashes?->id,
                'name'              => 'Guinzaglio in pelle intrecciata',
                'name_en'           => 'Braided Leather Leash',
                'slug'              => 'braided-leather-leash',
                'description'       => 'Elegante guinzaglio in pelle intrecciata, lunghezza 1,5 m. Impugnatura imbottita comoda. Disponibile in colori classici.',
                'description_en'    => 'Elegant braided leather leash, 1.5m length. Comfortable padded handle. Available in classic colors.',
                'price'             => 34.99,
                'available_colors'  => $colorSets['classic'],
                'available_sizes'   => null,
                'is_active'         => true,
                'is_featured'       => true,
                'is_best_seller'    => true,
                'discount_percent'  => 0,
            ],
            [
                'category_id'       => $leashes?->id,
                'name'              => 'Guinzaglio retrattile',
                'name_en'           => 'Retractable Dog Leash',
                'slug'              => 'retractable-dog-leash',
                'description'       => 'Si estende fino a 5 metri per la massima libertà. Freno e blocco con un pulsante. Adatto a cani fino a 25 kg.',
                'description_en'    => 'Extends up to 5 meters for maximum freedom. One-button brake and lock. Suitable for dogs up to 25kg.',
                'price'             => 22.99,
                'available_colors'  => $colorSets['neutral'],
                'available_sizes'   => null,
                'is_active'         => true,
                'is_featured'       => false,
                'is_best_seller'    => false,
                'discount_percent'  => 0,
            ],
            // Coats
            [
                'category_id'       => $coats?->id,
                'name'              => 'Cappotto imbottito invernale',
                'name_en'           => 'Winter Puffer Coat',
                'slug'              => 'winter-puffer-coat',
                'description'       => 'Tiene al caldo il tuo cane nelle passeggiate invernali. Esterno idrorepellente, fodera interna morbida. Copertura completa del ventre.',
                'description_en'    => 'Keeps your dog warm during cold winter walks. Water-repellent outer shell, cosy inner lining. Full belly coverage.',
                'price'             => 49.99,
                'available_colors'  => $colorSets['neutral'],
                'available_sizes'   => $sizes,
                'is_active'         => true,
                'is_featured'       => true,
                'is_best_seller'    => false,
                'discount_percent'  => 15,
            ],
            [
                'category_id'       => $coats?->id,
                'name'              => 'Impermeabile con cappuccio',
                'name_en'           => 'Raincoat with Hood',
                'slug'              => 'raincoat-with-hood',
                'description'       => 'Completamente impermeabile con cappuccio regolabile. Cinghia ventrale in velcro per una vestibilità sicura. Facile da pulire.',
                'description_en'    => 'Fully waterproof with adjustable hood. Velcro belly strap for secure fit. Easy to clean.',
                'price'             => 39.99,
                'available_colors'  => $colorSets['pastels'],
                'available_sizes'   => $sizes,
                'is_active'         => true,
                'is_featured'       => false,
                'is_best_seller'    => true,
                'discount_percent'  => 0,
            ],
            // Harnesses
            [
                'category_id'       => $harnesses?->id,
                'name'              => 'Pettorina anti-tiro step-in',
                'name_en'           => 'No-Pull Step-In Harness',
                'slug'              => 'no-pull-step-in-harness',
                'description'       => 'Design step-in facile con agganci anteriori e posteriori. Petto imbottito per il comfort. Strisce riflettenti per la sicurezza notturna.',
                'description_en'    => 'Easy step-in design with front and back leash attachments. Padded chest plate for comfort. Reflective strips for night safety.',
                'price'             => 44.99,
                'available_colors'  => $colorSets['classic'],
                'available_sizes'   => $sizes,
                'is_active'         => true,
                'is_featured'       => true,
                'is_best_seller'    => true,
                'discount_percent'  => 0,
            ],
            [
                'category_id'       => $harnesses?->id,
                'name'              => 'Pettorina da trekking',
                'name_en'           => 'Adventure Hiking Harness',
                'slug'              => 'adventure-hiking-harness',
                'description'       => 'Pettorina robusta per avventure all\'aperto. Maniglia sul dorso per assistenza su terreni difficili. Punti di regolazione multipli.',
                'description_en'    => 'Heavy-duty harness for outdoor adventures. Handle on back for assistance on rough terrain. Multiple adjustment points.',
                'price'             => 59.99,
                'available_colors'  => $colorSets['neutral'],
                'available_sizes'   => $sizes,
                'is_active'         => true,
                'is_featured'       => false,
                'is_best_seller'    => false,
                'discount_percent'  => 0,
            ],
            // Bag Holders
            [
                'category_id'       => $bagHolders?->id,
                'name'              => 'Porta sacchetti in pelle',
                'name_en'           => 'Leather Bag Holder Keychain',
                'slug'              => 'leather-bag-holder-keychain',
                'description'       => 'Elegante pochette in pelle da agganciare a guinzaglio o borsa. Include 1 rotolo di sacchetti. Non dimenticare mai i sacchetti.',
                'description_en'    => 'Stylish leather pouch that attaches to any leash or bag. Includes 1 roll of bags. Never forget bags again.',
                'price'             => 12.99,
                'available_colors'  => $colorSets['classic'],
                'available_sizes'   => null,
                'is_active'         => true,
                'is_featured'       => false,
                'is_best_seller'    => false,
                'discount_percent'  => 0,
            ],
            [
                'category_id'       => $bagHolders?->id,
                'name'              => 'Dispenser sacchetti in silicone',
                'name_en'           => 'Silicone Bag Dispenser',
                'slug'              => 'silicone-bag-dispenser',
                'description'       => 'Dispenser in silicone vivace e facile da individuare. Contiene rotoli standard. Colori divertenti. Include 2 rotoli di sacchetti ecologici.',
                'description_en'    => 'Bright and easy-to-find silicone dispenser. Holds standard rolls. Comes in fun colors. Includes 2 rolls of eco-friendly bags.',
                'price'             => 9.99,
                'available_colors'  => $colorSets['pastels'],
                'available_sizes'   => null,
                'is_active'         => true,
                'is_featured'       => false,
                'is_best_seller'    => false,
                'discount_percent'  => 20,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
