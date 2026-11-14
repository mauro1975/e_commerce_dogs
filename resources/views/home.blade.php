@extends('layouts.app')

@section('title', '.rosmarino – Premium Dog Accessories')
@section('meta_description', 'Discover our collection of premium dog accessories: collars, leashes, harnesses, coats and more.')

@section('head')
<style>
    .hero { position:relative; background:#1a1a1a; overflow:hidden; min-height:85vh; display:flex; align-items:center; }

    /* Gallery */
    .hero-gallery { position:absolute; inset:0; }
    .hero-slide { position:absolute; inset:0; opacity:0; transition:opacity 1.2s ease; }
    .hero-slide.active { opacity:1; }
    .hero-slide img {
        width:100%; height:100%; object-fit:cover;
        transform-origin:center center;
    }
    .hero-slide::after {
        content:''; position:absolute; inset:0;
        background:linear-gradient(to right, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.15) 60%, rgba(0,0,0,0) 100%);
    }
    /* Ken Burns zoom animations */
    @keyframes kb-in  { from { transform:scale(1);    } to { transform:scale(1.12); } }
    @keyframes kb-out { from { transform:scale(1.12); } to { transform:scale(1);    } }
    .hero-slide.active img   { animation:kb-in  7s ease forwards; }
    .hero-slide.leaving img  { animation:kb-out 7s ease forwards; }

    /* Dot indicators */
    .hero-dots { position:absolute; bottom:28px; left:50%; transform:translateX(-50%); display:flex; gap:8px; z-index:3; }
    .hero-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.4); border:none; padding:0; cursor:pointer; transition:background .3s,transform .3s; }
    .hero-dot.active { background:#fff; transform:scale(1.3); }

    .hero-content { position:relative; z-index:2; max-width:580px; padding:80px 40px 80px 80px; color:#fff; }
    .hero-eyebrow { font-size:12px; letter-spacing:4px; text-transform:uppercase; color:var(--green-dark); font-weight:600; margin-bottom:16px; }
    .hero-title { font-size:clamp(40px,6vw,80px); font-weight:700; line-height:1.05; margin-bottom:24px; }
    .hero-sub { font-size:17px; color:var(--text-muted); max-width:480px; margin-bottom:40px; line-height:1.7; }
    .hero-actions { display:flex; gap:16px; flex-wrap:wrap; }

    @keyframes hero-fade-up {
        from { opacity:0; transform:translateY(28px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .hero-content .hero-eyebrow { animation:hero-fade-up .7s ease both; animation-delay:.15s; }
    .hero-content .hero-title   { animation:hero-fade-up .7s ease both; animation-delay:.35s; }
    .hero-content .hero-sub     { animation:hero-fade-up .7s ease both; animation-delay:.55s; }
    .hero-content .hero-actions { animation:hero-fade-up .7s ease both; animation-delay:.75s; }

    .cat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
    @media(max-width:992px){.cat-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:576px){.cat-grid{grid-template-columns:1fr 1fr;}}

    .features-strip { background:var(--green-light); padding:40px; }
    .features-strip .feature i { font-size:28px; color:var(--green-dark); margin-bottom:10px; display:block; }
    .features-strip .feature h6 { font-weight:600; font-size:14px; margin-bottom:4px; font-family:var(--font-sans); }
    .features-strip .feature p { font-size:13px; color:var(--text-muted); margin:0; }

    .bs-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:28px; }
    @media(max-width:992px){ .bs-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:576px){ .bs-grid { grid-template-columns:repeat(2,1fr); gap:14px; } }
    .bs-card { display:block; text-decoration:none; color:inherit; }
    .bs-card-img { display:block; width:100%; aspect-ratio:3/4; object-fit:cover; overflow:hidden; background:var(--gray); }
    .bs-card-img img { width:100%; height:100%; object-fit:cover; transition:transform .45s ease; display:block; }
    .bs-card:hover .bs-card-img img { transform:scale(1.05); }
    .bs-card-name { font-size:14px; font-weight:500; margin:10px 0 4px; color:var(--black); }
    .bs-card-price { font-size:14px; font-weight:700; color:var(--black); }
    .bs-card-price-old { font-size:13px; font-weight:400; color:var(--text-muted); text-decoration:line-through; margin-right:6px; }
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero-gallery" id="heroGallery">
        @php
            $heroSlides = $galleryPhotos->isNotEmpty()
                ? $galleryPhotos->map(fn($p) => ['src' => asset($p->path), 'alt' => $p->alt ?? ''])
                : collect([
                    ['src' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=1600&auto=format&fit=crop', 'alt' => ''],
                    ['src' => 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=1600&auto=format&fit=crop', 'alt' => ''],
                    ['src' => 'https://images.unsplash.com/photo-1601758124510-52d02ddb7cbd?w=1600&auto=format&fit=crop', 'alt' => ''],
                    ['src' => 'https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?w=1600&auto=format&fit=crop', 'alt' => ''],
                ]);
        @endphp
        @foreach($heroSlides as $i => $slide)
        <div class="hero-slide {{ $i === 0 ? 'active' : '' }}">
            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}">
        </div>
        @endforeach
    </div>
    <div class="hero-dots" id="heroDots"></div>
    <div class="hero-content">
        <p class="hero-eyebrow" style="color:var(--green);">{{ __('front.home_hero_eyebrow') }}</p>
        <h1 class="hero-title" style="color:#fff;">{!! __('front.home_hero_title') !!}</h1>
        <p class="hero-sub" style="color:rgba(255,255,255,0.75);">{{ __('front.home_hero_sub') }}</p>
        <div class="hero-actions">
            <a href="{{ route('collection') }}" class="btn-black">{{ __('front.home_btn_collection') }}</a>
            <a href="{{ route('best-sellers') }}" class="btn-outline-green">{{ __('front.home_btn_best_sellers') }}</a>
        </div>
    </div>
</section>

{{-- Features Strip --}}
<div class="features-strip">
    <div style="max-width:1400px;margin:0 auto;">
        <div class="row g-4">
            <div class="col-6 col-md-3 feature text-center">
                <i class="bi bi-truck d-block"></i>
                <h6>{{ __('front.feat_shipping_title') }}</h6>
                <p>{{ __('front.feat_shipping_desc') }}</p>
            </div>
            <div class="col-6 col-md-3 feature text-center">
                <i class="bi bi-shield-check d-block"></i>
                <h6>{{ __('front.feat_quality_title') }}</h6>
                <p>{{ __('front.feat_quality_desc') }}</p>
            </div>
            <div class="col-6 col-md-3 feature text-center">
                <i class="bi bi-arrow-return-left d-block"></i>
                <h6>{{ __('front.feat_returns_title') }}</h6>
                <p>{{ __('front.feat_returns_desc') }}</p>
            </div>
            <div class="col-6 col-md-3 feature text-center">
                <i class="bi bi-headset d-block"></i>
                <h6>{{ __('front.feat_support_title') }}</h6>
                <p>{{ __('front.feat_support_desc') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Shop by Category --}}
<section style="padding:80px 40px; max-width:1400px; margin:0 auto;">
    <div class="section-title">
        <p style="font-size:12px;letter-spacing:4px;text-transform:uppercase;color:var(--green-dark);font-weight:600;">{{ __('front.home_cat_eyebrow') }}</p>
        <h2>{{ __('front.home_cat_heading') }}</h2>
    </div>

    @if($categories->count() > 0)
        <div class="cat-grid">
            @foreach($categories as $cat)
                @php
                    $coverUrl = $cat->image
                        ? asset($cat->image)
                        : 'https://placehold.co/400x500/e0f0eb/6fa398?text=' . urlencode($cat->name);
                @endphp
                <a href="{{ route('category.show', $cat->slug) }}" class="cat-card">
                    <img src="{{ $coverUrl }}" alt="{{ $cat->name }}" loading="lazy">
                    <div class="cat-card-label">
                        <h3>{{ $cat->name }}</h3>
                        <p style="margin:4px 0 0;font-size:13px;opacity:0.85;">{{ __('front.home_cat_explore') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <p class="text-muted">{{ __('front.home_cat_empty') }}</p>
            <a href="{{ route('collection') }}" class="btn-black mt-3 d-inline-block">{{ __('front.home_cat_browse') }}</a>
        </div>
    @endif
</section>

{{-- Best Sellers --}}
@if(isset($bestSellers) && $bestSellers->count() > 0)
<section style="padding:80px 40px; background:var(--gray);">
    <div style="max-width:1400px;margin:0 auto;">
        <div class="section-title">
            <p style="font-size:12px;letter-spacing:4px;text-transform:uppercase;color:var(--green-dark);font-weight:600;">{{ __('front.home_bs_eyebrow') }}</p>
            <h2>{{ __('front.home_bs_heading') }}</h2>
        </div>
        <div class="bs-grid">
            @foreach($bestSellers as $product)
                @php
                    $bsImgs  = is_array($product->images) ? $product->images : (json_decode($product->images, true) ?? []);
                    $bsThumb = !empty($bsImgs) ? asset($bsImgs[0]) : 'https://placehold.co/400x500/f5f5f5/999?text='.urlencode($product->name);
                @endphp
                <a href="{{ route('product.show', $product->slug) }}" class="bs-card">
                    <div class="bs-card-img">
                        <img src="{{ $bsThumb }}" alt="{{ $product->name }}" loading="lazy">
                    </div>
                    <p class="bs-card-name">{{ $product->name }}</p>
                    <p class="bs-card-price">
                        @if($product->discount_percent > 0)
                            <span class="bs-card-price-old">€{{ number_format($product->price, 2) }}</span>
                        @endif
                        €{{ number_format($product->final_price, 2) }}
                    </p>
                </a>
            @endforeach
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('best-sellers') }}" class="btn-outline-green">{{ __('front.home_bs_view_all') }}</a>
        </div>
    </div>
</section>
@endif

{{-- Brand Story CTA --}}
<section style="padding:100px 40px; text-align:center; max-width:800px; margin:0 auto;">
    <p style="font-size:12px;letter-spacing:4px;text-transform:uppercase;color:var(--green-dark);font-weight:600;">{{ __('front.home_story_eyebrow') }}</p>
    <h2 style="font-size:clamp(32px,4vw,42px); margin-bottom:20px;">{{ __('front.home_story_heading') }}</h2>
    <p style="font-size:16px; color:var(--text-muted); line-height:1.8; margin-bottom:36px;">
        {{ __('front.home_story_text') }}
    </p>
    <a href="{{ route('about') }}" class="btn-black">{{ __('front.home_story_btn') }}</a>
</section>

@endsection

@section('scripts')
<script>
(function(){
    const slides = Array.from(document.querySelectorAll('.hero-slide'));
    const dotsEl = document.getElementById('heroDots');
    let current = 0, timer;

    // Build dots
    slides.forEach(function(_,i){
        const d = document.createElement('button');
        d.className = 'hero-dot' + (i===0 ? ' active' : '');
        d.setAttribute('aria-label','Slide '+(i+1));
        d.addEventListener('click', function(){ goTo(i); });
        dotsEl.appendChild(d);
    });

    function restartAnim(slide){
        const img = slide.querySelector('img');
        const clone = img.cloneNode(true);
        img.replaceWith(clone);
    }

    function goTo(idx){
        clearInterval(timer);
        const prev = slides[current];
        prev.classList.add('leaving');
        prev.classList.remove('active');
        setTimeout(function(){ prev.classList.remove('leaving'); }, 1300);
        current = idx;
        slides[current].classList.add('active');
        restartAnim(slides[current]);
        document.querySelectorAll('.hero-dot').forEach(function(d,i){
            d.classList.toggle('active', i===current);
        });
        startTimer();
    }

    function startTimer(){ timer = setInterval(function(){ goTo((current+1) % slides.length); }, 6000); }

    // Kick off zoom on first slide immediately
    restartAnim(slides[0]);
    startTimer();
})();
</script>
@endsection

