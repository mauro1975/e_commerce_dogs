<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\CollarSizeOptions;
use App\Support\LeashSizeOptions;
use App\Support\PdpSizeGuide;
use App\Support\ProductStock;
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

        $sizes = is_array($product->available_sizes)
            ? $product->available_sizes
            : (json_decode($product->available_sizes ?? '[]', true) ?? []);

        $isCollarProduct = in_array($product->category?->slug, ['collari', 'collars'], true);
        $isLeashProduct = in_array($product->category?->slug, ['guinzagli', 'leashes'], true);
        $isHarnessProduct = in_array($product->category?->slug, ['pettorine', 'harnesses'], true);
        // Collari/guinzagli: select con etichette catalogo; pettorine e maglioncini: pillole taglia
        $isCatalogSizeProduct = $isCollarProduct || $isLeashProduct;

        $catalogSizeRows = [];
        if ($isCollarProduct) {
            $catalogSizeRows = CollarSizeOptions::forProductSizes($product->category, $sizes);
        } elseif ($isLeashProduct) {
            $catalogSizeRows = LeashSizeOptions::forProductSizes($product->category, $sizes);
        }

        $showSizeChart = ($product->category?->slug === 'maglioncini') || (! $isCatalogSizeProduct && ! empty($sizes));
        $sizeGuide = $showSizeChart
            ? PdpSizeGuide::build($product, $sizes)
            : ['rows' => [], 'guideRows' => [], 'active' => false, 'maxChest' => 1.0];

        $pdpGuideRows = $sizeGuide['guideRows'];
        $pdpSizeGuideActive = $sizeGuide['active'];

        $variantStockMap = [];
        $fallbackStock = (int) ($product->stock ?? 0);
        $usesProductPool = ProductStock::usesProductPool($product);
        foreach ($product->variants as $variant) {
            $colorKey = (string) $variant->color;
            $sizeKey = (string) $variant->size;
            $variantStockMap[$colorKey][$sizeKey] = $usesProductPool
                ? $fallbackStock
                : (int) $variant->stock;
        }

        return view('shop.product', compact(
            'product',
            'related',
            'sizes',
            'showSizeChart',
            'pdpGuideRows',
            'pdpSizeGuideActive',
            'isCollarProduct',
            'isLeashProduct',
            'isHarnessProduct',
            'isCatalogSizeProduct',
            'catalogSizeRows',
            'variantStockMap',
            'fallbackStock',
            'usesProductPool'
        ));
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

    public function shippingReturns()
    {
        return view('shop.shipping_returns');
    }
}
