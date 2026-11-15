<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ShopController extends Controller
{
    public function collection()
    {
        $categories = Category::with('products')->where('is_active', true)->orderBy('sort_order')->get();
        return view('shop.collection', compact('categories'));
    }

    public function category(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $query = Product::where('category_id', $category->id)->where('is_active', true);

        match($request->input('sort')) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->latest(),
            default      => $query->orderBy('name'),
        };

        $categoryItems = collect();

        if ($slug === 'maglioncini') {
            $products = $query->get();
            $categoryItems = $this->maglionciniItemsByColor($products);
            $productsPaginated = false;
        } else {
            $products = $query->paginate(12)->withQueryString();
            $productsPaginated = true;
        }

        return view('shop.category', compact('category', 'products', 'productsPaginated', 'categoryItems'));
    }

    /**
     * One grid card per sweater color (image + title include color name).
     *
     * @param  Collection<int, Product>  $products
     * @return Collection<int, array{product: Product, color: ?string, thumb: ?string}>
     */
    private function maglionciniItemsByColor(Collection $products): Collection
    {
        $items = collect();

        foreach ($products as $product) {
            $colors = $product->available_colors ?? [];
            if (! is_array($colors)) {
                $colors = json_decode($colors, true) ?? [];
            }

            $fallbackImgs = is_array($product->images)
                ? $product->images
                : (json_decode($product->images ?? '[]', true) ?? []);

            if ($colors === []) {
                $items->push([
                    'product' => $product,
                    'color'   => null,
                    'thumb'   => $fallbackImgs[0] ?? $product->image,
                ]);
                continue;
            }

            foreach ($colors as $col) {
                $colorName = is_array($col) ? trim((string) ($col['name'] ?? '')) : trim((string) $col);
                if ($colorName === '') {
                    continue;
                }

                $colorImgs = [];
                if (is_array($col)) {
                    $colorImgs = ! empty($col['images'])
                        ? $col['images']
                        : (! empty($col['image']) ? [$col['image']] : []);
                }

                $thumb = $colorImgs[0] ?? $fallbackImgs[0] ?? $product->image;

                $items->push([
                    'product' => $product,
                    'color'   => $colorName,
                    'thumb'   => $thumb,
                ]);
            }
        }

        return $items;
    }

    public function product(string $slug)
    {
        $product  = Product::where('slug', $slug)->where('is_active', true)->with('category', 'variants')->firstOrFail();
        $related  = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }

    public function bestSellers()
    {
        $products = Product::where('is_best_seller', true)->where('is_active', true)->paginate(12);
        return view('shop.best_sellers', compact('products'));
    }

    public function photos()
    {
        $categories = Category::where('is_active', true)->with('products')->get();
        return view('shop.photos', compact('categories'));
    }

    public function about()
    {
        return view('shop.about');
    }
}
