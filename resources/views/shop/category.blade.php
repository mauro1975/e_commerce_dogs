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

    .cat-products-grid--cols-5 {
        grid-template-columns:repeat(5, minmax(0, 1fr));
        gap:22px 14px;
    }
    @media(max-width:992px){ .cat-products-grid--cols-5 { grid-template-columns:repeat(3, minmax(0, 1fr)); } }
    @media(max-width:768px) { .cat-products-grid--cols-5 { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:20px 14px; } }
    @media(max-width:480px) { .cat-products-grid--cols-5 { grid-template-columns:1fr; } }
    .cat-products-grid--cols-5 .cat-product-name { font-size:19px; }
    .cat-products-grid--cols-5 .cat-product-body { padding:14px 14px 18px; }

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
    .cat-product-img { aspect-ratio:3/4; overflow:hidden; background:#f5f5f5; flex-shrink:0; }
    .cat-product-img img {
        width:100%; height:100%; object-fit:cover; object-position:center center;
        display:block;
    }
    /* Maglioncini: foto intere su bianco, niente zoom hover (evita moiré sulle righe) */
    .cat-products-grid--maglioncini .cat-product-img { background:#fff; }
    .cat-products-grid--maglioncini .cat-product-img img {
        object-fit:contain;
        padding:12px 10px;
        box-sizing:border-box;
    }
    .cat-product-body { padding:16px 18px 20px; flex:1; display:flex; flex-direction:column; }
    .cat-product-name {
        font-family:var(--font-serif);
        font-size:22px; font-weight:400; margin:0 0 10px; color:var(--black); line-height:1.35;
        display:flex; flex-wrap:wrap; align-items:baseline; gap:6px 8px;
    }
    .cat-product-cashmere {
        font-family:var(--font-sans);
        font-size:11px;
        font-weight:600;
        letter-spacing:.4px;
        text-transform:uppercase;
        color:var(--green-dark);
        white-space:nowrap;
    }
    .cat-product-price { font-size:16px; font-weight:600; margin:0; margin-top:auto; color:var(--black); letter-spacing:.02em; }
    .cat-product-price-old { font-weight:400; color:var(--text-muted); text-decoration:line-through; margin-right:6px; font-size:14px; }
</style>
@endsection

@section('content')
@php $isMaglionciniCat = $category->slug === 'maglioncini'; @endphp
<div style="max-width:{{ $isMaglionciniCat ? 'min(100%, 1860px)' : '1400px' }}; margin:0 auto; padding:40px 24px 80px;">

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

    @php
        $useColorGrid = $isMaglionciniCat && isset($categoryItems) && $categoryItems->isNotEmpty();
        $productTotal = $useColorGrid
            ? $categoryItems->count()
            : (($productsPaginated ?? true) ? $products->total() : $products->count());
    @endphp

    <div class="cat-filter-bar">
        <span style="font-size:14px;color:var(--text-muted);">
            <strong style="color:var(--black);">{{ $productTotal }}</strong>
            {{ $productTotal === 1 ? __('front.cat_product_singular') : __('front.cat_product_plural') }}
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

    @if($productTotal > 0)
        <div class="cat-products-grid{{ $isMaglionciniCat ? ' cat-products-grid--cols-5 cat-products-grid--maglioncini' : '' }}">
            @if($useColorGrid)
                @foreach($categoryItems as $item)
                    @php
                        $product = $item['product'];
                        $colorName = $item['color'];
                        $cardTitle = $colorName
                            ? trim($product->name . ' ' . $colorName)
                            : $product->name;
                        $thumbPath = $item['thumb'];
                        $thumb = $thumbPath
                            ? asset($thumbPath)
                            : 'https://placehold.co/400x500/f8f8f6/9ad7a0?text=' . urlencode($cardTitle);
                        $productUrl = route('product.show', $product->slug);
                        if ($colorName) {
                            $productUrl .= '?color=' . rawurlencode($colorName);
                        }
                        $showCashmereBadge = str_starts_with($product->name, 'Maglioncino');
                    @endphp
                    <a href="{{ $productUrl }}" class="cat-product-card">
                        <div class="cat-product-img">
                            <img src="{{ $thumb }}" alt="{{ $cardTitle }}" loading="lazy">
                        </div>
                        <div class="cat-product-body">
                            <h2 class="cat-product-name">
                                <span>{{ $cardTitle }}</span>
                                @if($showCashmereBadge)
                                    <span class="cat-product-cashmere">{{ __('front.cat_sweater_cashmere') }}</span>
                                @endif
                            </h2>
                            <p class="cat-product-price">
                                @if($product->discount_percent > 0)
                                    <span class="cat-product-price-old">€{{ number_format($product->price, 2) }}</span>
                                @endif
                                €{{ number_format($product->final_price, 2) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            @else
                @foreach($products as $product)
                    @php
                        $imgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?? []);
                        $thumb = !empty($imgs)
                            ? asset($imgs[0])
                            : 'https://placehold.co/400x500/f8f8f6/9ad7a0?text=' . urlencode($product->name);
                        $showCashmereBadge = $isMaglionciniCat && str_starts_with($product->name, 'Maglioncino');
                    @endphp
                    <a href="{{ route('product.show', $product->slug) }}" class="cat-product-card">
                        <div class="cat-product-img">
                            <img src="{{ $thumb }}" alt="{{ $product->name }}" loading="lazy">
                        </div>
                        <div class="cat-product-body">
                            <h2 class="cat-product-name">
                                <span>{{ $product->name }}</span>
                                @if($showCashmereBadge)
                                    <span class="cat-product-cashmere">{{ __('front.cat_sweater_cashmere') }}</span>
                                @endif
                            </h2>
                            <p class="cat-product-price">
                                @if($product->discount_percent > 0)
                                    <span class="cat-product-price-old">€{{ number_format($product->price, 2) }}</span>
                                @endif
                                €{{ number_format($product->final_price, 2) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        @if($productsPaginated ?? true)
        <div class="mt-5 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
        @endif
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
