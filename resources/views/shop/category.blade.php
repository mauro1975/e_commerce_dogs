@extends('layouts.app')

@section('title', $category->name . ' – .rosmarino')
@section('meta_description', $category->description ?? 'Browse our ' . $category->name . ' collection.')

@section('head')
<style>
    .cat-page-header { margin-bottom:40px; }
    .cat-page-title { font-size:clamp(28px,4vw,40px); font-weight:700; margin:8px 0 12px; }
    .cat-page-desc { font-size:16px; color:var(--text-muted); line-height:1.7; max-width:720px; margin:0; }

    .cat-filter-bar { display:flex; align-items:center; justify-content:space-between;
                      flex-wrap:wrap; gap:12px; margin-bottom:32px; }
    .sort-select { border:1.5px solid #ddd; border-radius:8px; padding:8px 14px;
                   font-size:14px; background:#fff; cursor:pointer; outline:none; }
    .sort-select:focus { border-color:var(--black); }

    .cat-products-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:28px 20px; }
    @media(max-width:1200px){ .cat-products-grid { grid-template-columns:repeat(3,1fr); } }
    @media(max-width:768px) { .cat-products-grid { grid-template-columns:repeat(2,1fr); gap:20px 14px; } }
    @media(max-width:480px) { .cat-products-grid { grid-template-columns:1fr; } }

    .cat-product-card {
        display:flex; flex-direction:column; height:100%;
        text-decoration:none; color:inherit;
        background:#fff;
        border-radius:14px;
        overflow:hidden;
        border:1px solid rgba(0,0,0,0.06);
        box-shadow:0 4px 18px rgba(0,0,0,0.07);
        transition:transform .25s ease, box-shadow .25s ease;
    }
    .cat-product-card:hover {
        transform:translateY(-4px);
        box-shadow:0 12px 32px rgba(0,0,0,0.12);
    }
    .cat-product-card:hover .cat-product-img img { transform:scale(1.03); }
    .cat-product-img { aspect-ratio:3/4; overflow:hidden; background:#f5f5f5; flex-shrink:0; }
    .cat-product-img img { width:100%; height:100%; object-fit:cover; display:block;
                           transition:transform .45s ease; }
    .cat-product-body { padding:16px 18px 20px; flex:1; display:flex; flex-direction:column; }
    .cat-product-name {
        font-family:'Playfair Display',serif;
        font-size:17px; font-weight:600; margin:0 0 8px; color:var(--black); line-height:1.35;
    }
    .cat-product-price { font-size:16px; font-weight:600; margin:0 0 10px; color:var(--black); letter-spacing:.02em; }
    .cat-product-price-old { font-weight:400; color:var(--text-muted); text-decoration:line-through; margin-right:6px; font-size:14px; }
    .cat-product-desc { font-size:13px; line-height:1.65; color:var(--text-muted); margin:0; margin-top:auto; }
</style>
@endsection

@section('content')
<div style="max-width:1400px; margin:0 auto; padding:40px 24px 80px;">

    <nav aria-label="breadcrumb" style="margin-bottom:28px;">
        <ol class="breadcrumb" style="font-size:13px; background:none; padding:0; margin:0;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:var(--green-dark);">{{ __('front.cat_breadcrumb_home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('collection') }}" style="color:var(--green-dark);">{{ __('front.cat_breadcrumb_collection') }}</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <header class="cat-page-header">
        <p style="font-size:12px;letter-spacing:4px;text-transform:uppercase;color:var(--green-dark);font-weight:600;margin:0 0 8px;">
            {{ __('front.cat_breadcrumb_collection') }}
        </p>
        <h1 class="cat-page-title">{{ $category->name }}</h1>
        @if($category->description)
            <p class="cat-page-desc">{{ $category->description }}</p>
        @endif
    </header>

    @php $activeSort = request('sort'); @endphp

    <div class="cat-filter-bar">
        <span style="font-size:14px;color:var(--text-muted);">
            <strong style="color:var(--black);">{{ $products->total() }}</strong>
            {{ $products->total() === 1 ? __('front.cat_product_singular') : __('front.cat_product_plural') }}
        </span>
        <form method="GET" action="{{ route('category.show', $category->slug) }}">
            <select name="sort" class="sort-select" onchange="this.form.submit()">
                <option value="">{{ __('front.cat_sort_label') }}: {{ __('front.cat_sort_featured') }}</option>
                <option value="price_asc"  {{ $activeSort === 'price_asc'  ? 'selected' : '' }}>{{ __('front.cat_sort_price_asc') }}</option>
                <option value="price_desc" {{ $activeSort === 'price_desc' ? 'selected' : '' }}>{{ __('front.cat_sort_price_desc') }}</option>
                <option value="newest"     {{ $activeSort === 'newest'     ? 'selected' : '' }}>{{ __('front.cat_sort_newest') }}</option>
            </select>
        </form>
    </div>

    @if($products->count() > 0)
        <div class="cat-products-grid">
            @foreach($products as $product)
                @php
                    $imgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?? []);
                    $thumb = !empty($imgs)
                        ? asset($imgs[0])
                        : 'https://placehold.co/400x500/f8f8f6/9ad7a0?text=' . urlencode($product->name);
                    $desc = $product->description ? strip_tags($product->description) : '';
                @endphp
                <a href="{{ route('product.show', $product->slug) }}" class="cat-product-card">
                    <div class="cat-product-img">
                        <img src="{{ $thumb }}" alt="{{ $product->name }}" loading="lazy">
                    </div>
                    <div class="cat-product-body">
                        <h2 class="cat-product-name">{{ $product->name }}</h2>
                        <p class="cat-product-price">
                            @if($product->discount_percent > 0)
                                <span class="cat-product-price-old">€{{ number_format($product->price, 2) }}</span>
                            @endif
                            €{{ number_format($product->final_price, 2) }}
                        </p>
                        @if($desc !== '')
                            <p class="cat-product-desc">{{ Str::limit($desc, 140) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-bag" style="font-size:48px; color:var(--green);"></i>
            <h5 class="mt-3">{{ __('front.cat_empty_title') }}</h5>
            <p class="text-muted mb-4">{{ __('front.cat_empty_text') }}</p>
            <a href="{{ route('collection') }}" class="btn-black d-inline-block">{{ __('front.cat_breadcrumb_collection') }}</a>
        </div>
    @endif

</div>
@endsection
