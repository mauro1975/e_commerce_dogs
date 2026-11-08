@extends('layouts.app')

@section('title', $product->name . ' – .rosmarino')
@section('meta_description', $product->description ? Str::limit(strip_tags($product->description), 155) : $product->name . ' – premium dog accessories.')

@section('head')
<style>
/* â”€â”€ Product page layout â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.pdp-wrap {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 0 56px;
    max-width: 1320px;
    margin: 0 auto;
    padding: 40px 24px 72px;
    align-items: start;
    overflow-x: clip;
}

/* â”€â”€ Desktop image grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.pdp-media-col {
    min-width: 0;
    overflow: hidden;
    scrollbar-width: none;
}
.pdp-media-col::-webkit-scrollbar { display: none; width: 0; height: 0; }
.pdp-media-desktop {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    max-width: 100%;
    background: #fff;
}
.pdp-media-desktop.single-img .pdp-media-item { grid-column: 1 / -1; }
.pdp-media-item {
    overflow: hidden;
    background: #fff;
    line-height: 0;
    font-size: 0;
    isolation: isolate;
}
.pdp-media-item img {
    width: 100%;
    height: auto;
    max-height: min(75vh, 820px);
    display: block;
    border: 0;
    outline: none;
    vertical-align: top;
    /* evita la linea grigia sub-pixel sul bordo destro */
    transform: translateZ(0);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
}

/* â”€â”€ Mobile carousel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.pdp-media-mobile { display: none; }
.pdp-carousel-track { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; gap: 8px; scrollbar-width: none; }
.pdp-carousel-track::-webkit-scrollbar { display: none; }
.pdp-carousel-slide {
    flex: 0 0 100%; scroll-snap-align: start;
    overflow: hidden; background: #fff; line-height: 0;
}
.pdp-carousel-slide img {
    width: 100%; height: auto; max-height: min(72vh, 520px);
    display: block; margin: 0 auto;
}
.pdp-thumbs-strip { display: flex; gap: 6px; margin-top: 8px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; }
.pdp-thumbs-strip::-webkit-scrollbar { display: none; }
.pdp-thumb { flex: 0 0 60px; height: 60px; border-radius: 4px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: border-color .2s; background: #fff; }
.pdp-thumb img { width: 100%; height: 100%; object-fit: contain; object-position: center; display: block; }
.pdp-thumb.active { border-color: var(--black); }

/* â”€â”€ Right panel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.pdp-panel { position: sticky; top: 90px; }
.pdp-breadcrumb { font-size: 12px; color: #888; margin-bottom: 20px; }
.pdp-breadcrumb a { color: #888; text-decoration: none; }
.pdp-breadcrumb a:hover { color: var(--black); }
.pdp-breadcrumb span { margin: 0 5px; }
.pdp-cat-label { font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: var(--green-dark); font-weight: 600; margin-bottom: 8px; }
.pdp-title { font-size: 28px; line-height: 1.2; font-weight: 700; margin-bottom: 16px; }
.pdp-price { margin-bottom: 20px; display: flex; align-items: baseline; gap: 10px; }
.pdp-price-current { font-size: 24px; font-weight: 700; color: var(--black); }
.pdp-price-original { font-size: 16px; color: #aaa; text-decoration: line-through; }
.pdp-price-badge { background: var(--green); color: #fff; font-size: 11px; padding: 2px 7px; border-radius: 3px; font-weight: 700; }
.pdp-features { list-style: none; padding: 0; margin: 0 0 22px; font-size: 14px; line-height: 1.6; color: #444; }
.pdp-features li::before { content: '› '; color: var(--green-dark); font-weight: 700; }
.pdp-divider { border: none; border-top: 1px solid #eee; margin: 20px 0; }
.pdp-option-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px; color: var(--black); display: flex; justify-content: space-between; align-items: center; }
.pdp-option-label span { font-weight: 400; text-transform: none; letter-spacing: 0; color: #666; font-size: 13px; }

/* Size pills */
.pdp-size-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
.pdp-size-pill {
    min-width: 52px; height: 44px; padding: 0 14px;
    border: 1.5px solid #ddd; background: #fff;
    border-radius: 4px; cursor: pointer; font-size: 14px;
    font-weight: 500; transition: all .18s;
    display: flex; align-items: center; justify-content: center;
}
.pdp-size-pill:hover { border-color: var(--black); }
.pdp-size-pill.active { border-color: var(--black); background: var(--black); color: #fff; }

/* Color swatches */
.pdp-color-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
.pdp-color-swatch {
    width: 44px; height: 44px; border-radius: 4px;
    cursor: pointer; border: 2px solid transparent;
    transition: border-color .18s, transform .15s;
    position: relative;
}
.pdp-color-swatch:hover { transform: scale(1.08); }
.pdp-color-swatch.active { border-color: var(--black); outline: 2px solid var(--black); outline-offset: 2px; }
.pdp-color-swatch .swatch-inner { width: 100%; height: 100%; border-radius: 2px; }

/* Qty + cart */
.pdp-cart-row { display: flex; gap: 12px; margin-bottom: 12px; }
.pdp-qty { display: inline-flex; align-items: center; border: 1.5px solid #ddd; border-radius: 4px; overflow: hidden; }
.pdp-qty button { width: 40px; height: 50px; border: none; background: #fff; font-size: 20px; cursor: pointer; color: var(--black); }
.pdp-qty button:hover { background: #f5f5f5; }
.pdp-qty input { width: 48px; height: 50px; border: none; border-left: 1.5px solid #ddd; border-right: 1.5px solid #ddd; text-align: center; font-size: 15px; }
.pdp-add-btn {
    flex: 1; height: 50px;
    background: var(--black); color: #fff;
    border: none; border-radius: 4px;
    font-size: 14px; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase;
    cursor: pointer; transition: background .2s;
}
.pdp-add-btn:hover { background: #333; }
.pdp-add-btn:disabled { background: #aaa; cursor: not-allowed; }

/* Related – horizontal cards */
.pdp-related-section { margin-top: 28px; padding-top: 20px; border-top: 1px solid #eee; }
.pdp-related-title { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #888; font-weight: 600; margin-bottom: 14px; }
.pdp-hcard { display: flex; gap: 12px; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: inherit; transition: opacity .2s; }
.pdp-hcard:hover { opacity: .75; }
.pdp-hcard-img { width: 72px; height: 72px; flex-shrink: 0; border-radius: 4px; overflow: hidden; background: #f5f5f3; }
.pdp-hcard-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pdp-hcard-name { font-size: 13px; font-weight: 600; line-height: 1.3; margin-bottom: 4px; }
.pdp-hcard-price { font-size: 13px; color: #666; }

/* Stock badge */
.pdp-stock-low { font-size: 13px; color: #e67e22; margin-top: 8px; }
.pdp-stock-out { font-size: 13px; color: #e74c3c; margin-top: 8px; }

/* Below-fold related grid */
.pdp-below-related { max-width: 1320px; margin: 0 auto; padding: 0 24px 72px; }
.pdp-below-related h2 { font-size: 22px; font-weight: 700; margin-bottom: 28px; }
.related-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 24px; }

/* â”€â”€ Responsive â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
@media(max-width: 1024px) {
    .pdp-wrap { grid-template-columns: 1fr 360px; gap: 0 32px; }
}
@media (min-width: 769px) {
    .pdp-media-mobile {
        display: none !important;
        visibility: hidden;
        height: 0;
        overflow: hidden;
        margin: 0;
        pointer-events: none;
    }
}
@media(max-width: 768px) {
    .pdp-wrap { grid-template-columns: 1fr; padding: 20px 16px 48px; }
    .pdp-media-desktop { display: none; }
    .pdp-media-mobile { display: block; margin-bottom: 8px; visibility: visible; height: auto; pointer-events: auto; }
    .pdp-panel { position: static; }
    .related-grid { grid-template-columns: repeat(2,1fr); gap: 12px; }
    .pdp-title { font-size: 22px; }
}
</style>
@endsection

@section('content')
@php
    $imgs = is_array($product->images) ? $product->images : (json_decode($product->images, true) ?? []);
    if(empty($imgs) && $product->image) $imgs = [$product->image];
    $placeholder = 'https://placehold.co/600x600/f8f8f6/9ad7a0?text='.urlencode($product->name);
    $colors = is_array($product->available_colors) ? $product->available_colors : (json_decode($product->available_colors ?? '[]', true) ?? []);
    $sizes  = is_array($product->available_sizes)  ? $product->available_sizes  : (json_decode($product->available_sizes  ?? '[]', true) ?? []);

    // Per-color image map for JS (full asset URLs)
    $colorImgsMap = [];
    foreach ($colors as $col) {
        $colName = $col['name'] ?? '';
        $colImgs = !empty($col['images']) ? $col['images'] : (!empty($col['image']) ? [$col['image']] : []);
        $colorImgsMap[$colName] = array_map(fn($p) => asset($p), $colImgs);
    }

    // Override initial display with first color's images if available
    if (!empty($colors)) {
        $firstColImgs = !empty($colors[0]['images']) ? $colors[0]['images'] : (!empty($colors[0]['image']) ? [$colors[0]['image']] : []);
        if (!empty($firstColImgs)) $imgs = $firstColImgs;
    }
@endphp

<div class="pdp-wrap">

    {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• LEFT: image grid (desktop) / carousel (mobile) â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="pdp-media-col">
        {{-- Desktop gallery --}}
        <div class="pdp-media-desktop {{ count($imgs) <= 1 ? 'single-img' : '' }}">
            @forelse($imgs as $img)
                <div class="pdp-media-item">
                    <img src="{{ asset($img) }}" alt="{{ $product->name }}" loading="lazy">
                </div>
            @empty
                <div class="pdp-media-item">
                    <img src="{{ $placeholder }}" alt="{{ $product->name }}">
                </div>
            @endforelse
        </div>

        {{-- Mobile carousel --}}
        <div class="pdp-media-mobile">
            <div class="pdp-carousel-track" id="mobileCarousel">
                @forelse($imgs as $i => $img)
                    <div class="pdp-carousel-slide">
                        <img src="{{ asset($img) }}" alt="{{ $product->name }} {{ $i+1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                    </div>
                @empty
                    <div class="pdp-carousel-slide">
                        <img src="{{ $placeholder }}" alt="{{ $product->name }}">
                    </div>
                @endforelse
            </div>
            @if(count($imgs) > 1)
            <div class="pdp-thumbs-strip" id="mobileThumbStrip">
                @foreach($imgs as $i => $img)
                    <div class="pdp-thumb {{ $i===0?'active':'' }}" data-index="{{ $i }}" onclick="scrollToSlide({{ $i }})">
                        <img src="{{ asset($img) }}" alt="">
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• RIGHT: product panel â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
    <div class="pdp-panel">

        {{-- Breadcrumb --}}
        <nav class="pdp-breadcrumb">
            <a href="{{ route('home') }}">{{ __('front.pdp_home') }}</a>
            @if($product->category)
                <span>/</span>
                <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
            @endif
            <span>/</span>
            {{ $product->name }}
        </nav>

        {{-- Category + Title --}}
        @if($product->category)
            <p class="pdp-cat-label">{{ $product->category->name }}</p>
        @endif
        <h1 class="pdp-title">{{ $product->name }}</h1>

        {{-- Price --}}
        <div class="pdp-price">
            @if($product->discount_percent > 0)
                <span class="pdp-price-current">€{{ number_format($product->final_price, 2) }}</span>
                <span class="pdp-price-original">€{{ number_format($product->price, 2) }}</span>
                <span class="pdp-price-badge">-{{ $product->discount_percent }}%</span>
            @else
                <span class="pdp-price-current">€{{ number_format($product->price, 2) }}</span>
            @endif
        </div>

        {{-- Description as feature list --}}
        @if($product->description)
        <ul class="pdp-features">
            @foreach(array_filter(explode("\n", $product->description)) as $line)
                <li>{{ trim($line) }}</li>
            @endforeach
        </ul>
        @endif

        <hr class="pdp-divider">

        <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Color --}}
            @if(!empty($colors))
            <div style="margin-bottom:20px;">
                <div class="pdp-option-label">
                    {{ __('front.pdp_color_label') }} <span id="colorLabel">{{ $colors[0]['name'] ?? '' }}</span>
                </div>
                <div class="pdp-color-list">
                    @foreach($colors as $i => $color)
                        <div class="pdp-color-swatch {{ $i===0?'active':'' }}"
                             title="{{ $color['name'] ?? '' }}"
                             onclick="selectColor('{{ $color['name'] ?? '' }}', this)"
                             data-color="{{ $color['name'] ?? '' }}">
                            <div class="swatch-inner" style="background:{{ $color['hex'] ?? '#ccc' }};"></div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="color" id="selectedColor" value="{{ $colors[0]['name'] ?? '' }}">
            </div>
            @endif

            {{-- Size --}}
            @if(!empty($sizes))
            <div style="margin-bottom:22px;">
                <div class="pdp-option-label">
                    {{ __('front.pdp_size_label') }} <span id="sizeLabel">{{ __('front.pdp_select_size') }}</span>
                </div>
                <div class="pdp-size-list">
                    @foreach($sizes as $size)
                        <button type="button" class="pdp-size-pill"
                                onclick="selectSize('{{ $size }}', this)"
                                data-size="{{ $size }}">{{ $size }}</button>
                    @endforeach
                </div>
                <input type="hidden" name="size" id="selectedSize" value="">
            </div>
            @endif

            {{-- Qty + Add to cart --}}
            <div class="pdp-cart-row">
                <div class="pdp-qty">
                    <button type="button" onclick="changeQty(-1)" aria-label="-">−</button>
                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ $product->stock ?? 99 }}">
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
                <button type="submit" class="pdp-add-btn"
                    @if(($product->stock ?? 0) === 0) disabled @endif>
                    @if(($product->stock ?? 0) === 0)
                        {{ __('front.pdp_out_of_stock') }}
                    @else
                        <i class="bi bi-bag-plus me-2"></i>{{ __('front.pdp_add_to_bag') }}
                    @endif
                </button>
            </div>

            @if(($product->stock ?? 0) < 10 && ($product->stock ?? 0) > 0)
                <p class="pdp-stock-low"><i class="bi bi-exclamation-triangle me-1"></i>{{ __('front.pdp_low_stock', ['count' => $product->stock]) }}</p>
            @endif
        </form>

        @if($product->sku)
            <p style="font-size:12px;color:#aaa;margin-top:14px;">SKU: {{ $product->sku }}</p>
        @endif

        {{-- Related products as horizontal cards (in panel) --}}
        @if(isset($related) && $related->count() > 0)
        <div class="pdp-related-section">
            <p class="pdp-related-title">{{ __('front.pdp_related_title') }}</p>
            @foreach($related->take(4) as $rp)
            @php
                $rImgs  = is_array($rp->images) ? $rp->images : (json_decode($rp->images, true) ?? []);
                $rThumb = !empty($rImgs) ? asset($rImgs[0]) : 'https://placehold.co/80x80/f8f8f6/9ad7a0?text=' . urlencode($rp->name);
            @endphp
            <a href="{{ route('product.show', $rp->slug) }}" class="pdp-hcard">
                <div class="pdp-hcard-img">
                    <img src="{{ $rThumb }}" alt="{{ $rp->name }}" loading="lazy">
                </div>
                <div>
                    <p class="pdp-hcard-name">{{ $rp->name }}</p>
                    <p class="pdp-hcard-price">€{{ number_format($rp->final_price ?? $rp->price, 2) }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @endif

    </div>{{-- /.pdp-panel --}}
</div>{{-- /.pdp-wrap --}}

{{-- Below-fold related grid (remaining products) --}}
@if(isset($related) && $related->count() > 4)
<div class="pdp-below-related">
    <h2>{{ __('front.pdp_you_may_like') }}</h2>
    <div class="related-grid">
        @foreach($related->skip(4) as $rp)
            @include('partials.product-card', ['product' => $rp])
        @endforeach
    </div>
</div>
@elseif(isset($related) && $related->count() > 0 && $related->count() <= 4)
{{-- show full grid when there are â‰¤4 related and none shown in panel... already shown above --}}
@endif

@push('scripts')
<script>
const colorImages = @json($colorImgsMap);
function selectColor(name, el) {
    document.querySelectorAll('.pdp-color-swatch').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('selectedColor').value = name;
    document.getElementById('colorLabel').textContent = name;
    // Swap images for this color
    const imgs = colorImages[name] || [];
    if (imgs.length) updatePdpImages(imgs, name);
}
function updatePdpImages(imgs, label) {
    // Desktop grid
    const desktop = document.querySelector('.pdp-media-desktop');
    if (desktop) {
        desktop.className = 'pdp-media-desktop' + (imgs.length <= 1 ? ' single-img' : '');
        desktop.innerHTML = imgs.map(src =>
            `<div class="pdp-media-item"><img src="${src}" alt="${label}" loading="lazy"></div>`
        ).join('');
    }
    // Mobile carousel
    const track = document.getElementById('mobileCarousel');
    if (track) {
        track.innerHTML = imgs.map((src, i) =>
            `<div class="pdp-carousel-slide"><img src="${src}" alt="${label} ${i+1}" loading="${i===0?'eager':'lazy'}"></div>`
        ).join('');
    }
    // Thumb strip
    const strip = document.getElementById('mobileThumbStrip');
    if (strip) {
        strip.style.display = imgs.length > 1 ? '' : 'none';
        strip.innerHTML = imgs.length > 1 ? imgs.map((src, i) =>
            `<div class="pdp-thumb ${i===0?'active':''}" data-index="${i}" onclick="scrollToSlide(${i})"><img src="${src}" alt=""></div>`
        ).join('') : '';
    }
}
function selectSize(size, el) {
    document.querySelectorAll('.pdp-size-pill').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('selectedSize').value = size;
    document.getElementById('sizeLabel').textContent = size;
}
function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    input.value = Math.max(1, parseInt(input.value) + delta);
}
// Mobile carousel: scroll-snap + sync thumbnails
function scrollToSlide(index) {
    const track = document.getElementById('mobileCarousel');
    if (!track) return;
    const slide = track.querySelectorAll('.pdp-carousel-slide')[index];
    if (slide) slide.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
    document.querySelectorAll('.pdp-thumb').forEach((t, i) => t.classList.toggle('active', i === index));
}
(function() {
    const track = document.getElementById('mobileCarousel');
    if (!track) return;
    track.addEventListener('scroll', () => {
        const slides = track.querySelectorAll('.pdp-carousel-slide');
        let closest = 0, minDist = Infinity;
        slides.forEach((s, i) => {
            const dist = Math.abs(s.getBoundingClientRect().left - track.getBoundingClientRect().left);
            if (dist < minDist) { minDist = dist; closest = i; }
        });
        document.querySelectorAll('.pdp-thumb').forEach((t, i) => t.classList.toggle('active', i === closest));
    }, { passive: true });
})();
</script>
@endpush
@endsection

