@once
<style>
    .pc-swatches { display:flex; flex-wrap:wrap; gap:5px; margin:8px 0 4px; }
    .pc-swatch   { width:28px; height:34px; border-radius:4px; border:2px solid #e5e5e5;
                   cursor:pointer; transition:transform .15s, border-color .15s; flex-shrink:0;
                   padding:2px; background:#fff; box-sizing:border-box; overflow:hidden;
                   display:flex; align-items:center; justify-content:center; }
    .pc-swatch img { width:100%; height:100%; object-fit:contain; object-position:center; display:block; pointer-events:none; }
    .pc-swatch:hover { transform:scale(1.15); }
    .pc-swatch.active { border-color:var(--black) !important; transform:scale(1.15); }

    .pc-sizes    { display:flex; flex-wrap:wrap; gap:5px; margin:6px 0 8px; }
    .pc-size     { min-width:32px; height:28px; padding:0 6px; border:1.5px solid #ddd; border-radius:5px;
                   font-size:11px; font-weight:600; background:#fff; cursor:pointer; transition:all .15s;
                   display:flex; align-items:center; justify-content:center; color:var(--black); }
    .pc-size:hover  { border-color:var(--black); }
    .pc-size.active { border-color:var(--black); background:var(--black); color:#fff; }

    .pc-selection-hint { font-size:11px; color:#e74c3c; margin-bottom:4px; display:none; }
</style>
<script>
function pcSelectColor(btn, pid) {
    btn.closest('.pc-swatches').querySelectorAll('.pc-swatch').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
    const form = document.getElementById('pcForm_' + pid);
    form.querySelector('[name="color"]').value = btn.dataset.color;
    // Update color label
    const lbl = document.getElementById('pcColorLbl_' + pid);
    if (lbl) lbl.textContent = btn.dataset.color;
}
function pcSelectSize(btn, pid) {
    btn.closest('.pc-sizes').querySelectorAll('.pc-size').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
    const form = document.getElementById('pcForm_' + pid);
    form.querySelector('[name="size"]').value = btn.dataset.size;
    document.getElementById('pcHint_' + pid)?.style && (document.getElementById('pcHint_' + pid).style.display = 'none');
}
function pcSubmit(e, pid, needSize) {
    if (needSize) {
        const form = document.getElementById('pcForm_' + pid);
        if (!form.querySelector('[name="size"]').value) {
            e.preventDefault();
            const hint = document.getElementById('pcHint_' + pid);
            if (hint) hint.style.display = 'block';
            return false;
        }
    }
}
</script>
@endonce

@php
    $pid    = $product->id;
    $colors = $product->available_colors ?? [];
    $sizes  = $product->available_sizes  ?? [];
    // Ensure arrays (model casts handle this, but guard just in case)
    if (is_string($colors)) $colors = json_decode($colors, true) ?? [];
    if (is_string($sizes))  $sizes  = json_decode($sizes,  true) ?? [];
    $catalogHex = \App\Models\ProductColor::hexMap();
    $firstColor = !empty($colors) ? (is_array($colors[0]) ? $colors[0]['name'] : $colors[0]) : null;
    $firstSize  = !empty($sizes)  ? $sizes[0] : null;
    $imgs       = is_array($product->images) ? $product->images : (json_decode($product->images, true) ?? []);
    $thumb      = !empty($imgs)
        ? asset($imgs[0])
        : 'https://placehold.co/400x480/f8f8f6/9ad7a0?text=' . urlencode($product->name);
@endphp

<div class="product-card">
    <a href="{{ route('product.show', $product->slug) }}" class="product-card-img-wrap">
        <img src="{{ $thumb }}" alt="{{ $product->name }}" loading="lazy">
        @if($product->is_best_seller ?? false)
            <span class="product-badge">{{ __('front.pc_best_seller') }}</span>
        @elseif($product->is_featured ?? false)
            <span class="product-badge" style="background:#1a1a1a;">{{ __('front.pc_featured') }}</span>
        @endif
    </a>

    <div class="product-card-body">
        @if($product->category)
            <p class="product-category">{{ $product->category->name }}</p>
        @endif
        <h4 class="product-name">
            <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
        </h4>
        <div class="product-price">
            @if($product->discount_percent > 0)
                <span class="price-old">€{{ number_format($product->price, 2) }}</span>
                <span class="price-now">€{{ number_format($product->final_price, 2) }}</span>
            @else
                <span class="price-now">€{{ number_format($product->price, 2) }}</span>
            @endif
        </div>

        {{-- Colour swatches --}}
        @if(!empty($colors))
        <div class="pc-swatches">
            @foreach($colors as $c)
                @php
                    $cName = is_array($c) ? $c['name'] : $c;
                    $cHex = $catalogHex[$cName] ?? (is_array($c) ? ($c['hex'] ?? '#ccc') : '#ccc');
                    $cImgs = is_array($c)
                        ? (!empty($c['images']) ? $c['images'] : (!empty($c['image']) ? [$c['image']] : []))
                        : [];
                @endphp
                <button type="button"
                        class="pc-swatch {{ $loop->first ? 'active' : '' }}"
                        style="@if(empty($cImgs)) background-color:{{ $cHex }}; @endif outline-offset:2px;"
                        title="{{ $cName }}"
                        data-color="{{ $cName }}"
                        onclick="pcSelectColor(this, {{ $pid }})">
                    @if(!empty($cImgs))
                        <img src="{{ asset($cImgs[0]) }}" alt="">
                    @endif
                </button>
            @endforeach
        </div>
        <p style="font-size:11px; color:var(--text-muted); margin:-2px 0 4px;">
            {{ __('front.pc_colour') }}: <span id="pcColorLbl_{{ $pid }}">{{ $firstColor }}</span>
        </p>
        @endif

        {{-- Size buttons --}}
        @if(!empty($sizes))
        <div class="pc-sizes">
            @foreach($sizes as $s)
                <button type="button"
                        class="pc-size {{ $loop->first ? 'active' : '' }}"
                        data-size="{{ $s }}"
                        onclick="pcSelectSize(this, {{ $pid }})">
                    {{ $s }}
                </button>
            @endforeach
        </div>
        <p id="pcHint_{{ $pid }}" class="pc-selection-hint">{{ __('front.pc_select_size') }}</p>
        @endif

        {{-- Add to cart form --}}
        <form id="pcForm_{{ $pid }}" action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form mt-1"
              onsubmit="pcSubmit(event, {{ $pid }}, {{ !empty($sizes) ? 'true' : 'false' }})">
            @csrf
            <input type="hidden" name="product_id" value="{{ $pid }}">
            <input type="hidden" name="color"      value="{{ $firstColor }}">
            <input type="hidden" name="size"       value="{{ $firstSize }}">
            <input type="hidden" name="quantity"   value="1">
            <button type="submit" class="btn-add-cart">
                <i class="bi bi-bag-plus me-1"></i> {{ __('front.pc_add_to_bag') }}
            </button>
        </form>
    </div>
</div>

