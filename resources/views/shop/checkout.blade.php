@extends('layouts.app')

@section('title', 'Checkout – .rosmarino')

@section('head')
<style>
    .checkout-wrap { display:grid; grid-template-columns:1fr 380px; gap:32px; max-width:1200px; margin:0 auto; padding:60px 40px; }
    @media(max-width:900px){ .checkout-wrap { grid-template-columns:1fr; } .order-summary-col { order:-1; } }
    .step-label { font-size:12px;letter-spacing:3px;text-transform:uppercase;color:var(--green-dark);font-weight:600;margin-bottom:6px; }
    .checkout-section { background:#fff;border:1.5px solid #eee;border-radius:16px;padding:28px; margin-bottom:20px; }
    .form-control, .form-select { border:1.5px solid #ddd; border-radius:8px; padding:12px 14px; font-size:15px; }
    .form-control:focus, .form-select:focus { border-color:var(--green); box-shadow:0 0 0 3px rgba(154,215,160,0.2); }
    .order-item-row { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f0f0f0; }
    .order-item-row:last-child { border-bottom:none; }
    .order-item-img { width:64px;height:64px;border-radius:8px;object-fit:cover;background:var(--gray); }
    .discount-input-wrap { display:flex; gap:8px; }
    .discount-input-wrap input { flex:1; }
</style>
@endsection

@section('content')
<div class="checkout-wrap">

    {{-- Left: Customer Details --}}
    <div>
        <div class="step-label">{{ __('front.checkout_step') }}</div>
        <h1 style="font-size:28px;margin-bottom:28px;">{{ __('front.checkout_title') }}</h1>

        <form action="{{ route('checkout.submit') }}" method="POST" id="checkoutForm">
            @csrf

            {{-- Contact --}}
            <div class="checkout-section">
                <h5 style="font-family:var(--font-sans);font-weight:700;margin-bottom:20px;">{{ __('front.checkout_contact') }}</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_first_name') }} *</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', auth()->user()->name ?? '') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_last_name') }} *</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_email') }} *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_phone') }}</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>
            </div>

            {{-- Shipping --}}
            <div class="checkout-section">
                <h5 style="font-family:var(--font-sans);font-weight:700;margin-bottom:20px;">{{ __('front.checkout_shipping') }}</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_address') }} *</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address') }}" required>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_city') }} *</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                               value="{{ old('city') }}" required>
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_postal') }} *</label>
                        <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                               value="{{ old('postal_code') }}" required>
                        @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;">{{ __('front.checkout_country') }} *</label>
                        <select name="country" class="form-select" required>
                            <option value="GR" {{ old('country')=='GR'?'selected':'' }}>Greece</option>
                            <option value="DE" {{ old('country')=='DE'?'selected':'' }}>Germany</option>
                            <option value="FR" {{ old('country')=='FR'?'selected':'' }}>France</option>
                            <option value="IT" {{ old('country')=='IT'?'selected':'' }}>Italy</option>
                            <option value="ES" {{ old('country')=='ES'?'selected':'' }}>Spain</option>
                            <option value="NL" {{ old('country')=='NL'?'selected':'' }}>Netherlands</option>
                            <option value="GB" {{ old('country')=='GB'?'selected':'' }}>United Kingdom</option>
                            <option value="US" {{ old('country')=='US'?'selected':'' }}>United States</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="checkout-section">
                <h5 style="font-family:var(--font-sans);font-weight:700;margin-bottom:16px;">{{ __('front.checkout_notes') }} <span style="font-weight:400;font-size:14px;color:var(--text-muted);">{{ __('front.checkout_notes_opt') }}</span></h5>
                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('front.checkout_notes_ph') }}">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn-black w-100" style="padding:16px;font-size:16px;">
                {{ __('front.checkout_continue') }} <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
    </div>

    {{-- Right: Order Summary --}}
    <div class="order-summary-col">
        <div class="checkout-section" style="position:sticky;top:100px;">
            <h5 style="font-family:var(--font-sans);font-weight:700;margin-bottom:20px;">{{ __('front.checkout_summary') }}</h5>

            @foreach($cart as $key => $item)
            <div class="order-item-row">
                <div style="position:relative;">
                    <img class="order-item-img" src="{{ !empty($item['image']) ? asset($item['image']) : 'https://placehold.co/64x64/f8f8f6/9ad7a0?text=Dog' }}" alt="{{ $item['name'] }}">
                    <span style="position:absolute;top:-6px;right:-6px;background:var(--black);color:#fff;border-radius:50%;width:20px;height:20px;font-size:11px;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ $item['quantity'] }}</span>
                </div>
                <div style="flex:1;">
                    <p style="margin:0;font-weight:600;font-size:14px;">{{ $item['name'] }}</p>
                    @if(!empty($item['color']) || !empty($item['size']))
                        <p style="margin:2px 0 0;font-size:12px;color:var(--text-muted);">{{ $item['color'] }}{{ !empty($item['color']) && !empty($item['size']) ? ' / ' : '' }}{{ $item['size'] }}</p>
                    @endif
                </div>
                <p style="margin:0;font-weight:600;font-size:14px;">€{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
            </div>
            @endforeach

            {{-- Discount Code --}}
            @if(!session('discount'))
            <div style="margin:16px 0;">
                <form action="{{ route('cart.discount') }}" method="POST">
                    @csrf
                    <div class="discount-input-wrap">
                        <input type="text" name="code" class="form-control" placeholder="{{ __('front.checkout_discount_ph') }}" style="font-size:14px;">
                        <button type="submit" class="btn-outline-green" style="white-space:nowrap;font-size:14px;">{{ __('front.checkout_apply') }}</button>
                    </div>
                    @if(session('discount_error'))
                        <p style="color:#e74c3c;font-size:12px;margin-top:6px;">{{ session('discount_error') }}</p>
                    @endif
                </form>
            </div>
            @endif

            <div style="border-top:1.5px solid #eee;margin-top:16px;padding-top:16px;">
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:14px;color:var(--text-muted);">{{ __('front.checkout_subtotal') }}</span>
                    <span style="font-size:14px;font-weight:600;">€{{ number_format($subtotal, 2) }}</span>
                </div>
                @if(session('discount'))
                <div class="d-flex justify-content-between mb-2" style="color:var(--green-dark);">
                    <span style="font-size:14px;">{{ __('front.checkout_discount_line', ['code' => session('discount.code')]) }}</span>
                    <span style="font-size:14px;font-weight:600;">−€{{ number_format(session('discount.amount'), 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:14px;color:var(--text-muted);">{{ __('front.checkout_shipping_line') }}</span>
                    <span style="font-size:14px;font-weight:600;">{{ $subtotal >= 60 ? __('front.checkout_free') : '€4.90' }}</span>
                </div>
                <div class="d-flex justify-content-between" style="border-top:1.5px solid #eee;padding-top:12px;margin-top:8px;">
                    <span style="font-size:16px;font-weight:700;">{{ __('front.checkout_total') }}</span>
                    <span style="font-size:18px;font-weight:700;">€{{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

