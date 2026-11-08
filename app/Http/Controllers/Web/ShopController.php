<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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

        $products = $query->paginate(12)->withQueryString();

        return view('shop.category', compact('category', 'products'));
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
