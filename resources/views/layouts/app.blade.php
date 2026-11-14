<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="margin:0;padding:0;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Commerce Dogs')</title>
    <meta name="description" content="@yield('meta_description', 'Premium dog accessories – collars, leashes, harnesses, coats & more.')">
    <meta name="keywords" content="@yield('meta_keywords', 'dog collar, dog leash, dog harness, dog coat, dog accessories')">
    <meta property="og:title" content="@yield('title', 'E-Commerce Dogs')">
    <meta property="og:description" content="@yield('meta_description', 'Premium dog accessories')">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ url()->current() }}">
    <style>html,body{margin:0!important;padding:0!important;display:block;}</style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <style>
      
        @font-face {
            font-family: 'Simplified Arabic Fixed';
            src: url('{{ asset("font/Simplified Arabic Fixed Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        :root {
            --green:#9BC3B1; --green-dark:#6fa398; --green-light:#d4e8e2; --white:#ffffff; --black:#1a1a1a; --gray:#f5f5f5; --text-muted:#777; --border:#e8e8e8;
            --font-sans:'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-serif:'Instrument Serif', Georgia, serif;
        }
        * { box-sizing:border-box; }
        html { margin:0; padding:0; }
        body { font-family:var(--font-sans); background:#fff; color:var(--black); margin:0; padding:0; }
        h1,h2,h3,h4,h5,h6 { font-family:var(--font-serif); font-weight:400; letter-spacing:-0.01em; }
        .nav-links a, .sort-select, .btn-checkout, .btn-primary-green, .btn-outline-green, .btn-black,
        .mobile-nav a, .announcement-bar, input, select, textarea, button:not(.pc-swatch):not(.pc-size) {
            font-family:var(--font-sans);
        }
        .font-logo { font-family:'Simplified Arabic Fixed', serif; }
        .announcement-bar { background:var(--green); color:#fff; text-align:center; padding:8px; font-size:13px; letter-spacing:0.5px; margin:0; margin-top: 0 !important; }
        .site-header { position:sticky; top:0; z-index:1000; background:#fff; border-bottom:1px solid var(--border); }
        .navbar-main { display:flex; align-items:center; justify-content:space-between; padding:16px 40px; max-width:1400px; margin:0 auto; }
        .navbar-brand-logo { display:inline-flex; align-items:center; font-family:'Simplified Arabic Fixed',serif; font-size:30px; font-weight:700; color:#9bc3b1; text-decoration:none; letter-spacing:0; line-height:1; }
        .navbar-brand-logo:hover { color:#6fa398; text-decoration:none; }
        .nav-links { display:flex; gap:32px; list-style:none; margin:0; padding:0; }
        .nav-links a { color:var(--black); text-decoration:none; font-size:13px; font-weight:500; letter-spacing:1.5px; text-transform:uppercase; transition:color .2s; }
        .nav-links a:hover,.nav-links a.active { color:var(--green-dark); }
        .nav-actions { display:flex; align-items:center; gap:20px; }
        .nav-actions a { color:var(--black); text-decoration:none; font-size:20px; transition:color .2s; position:relative; }
        .nav-actions a:hover { color:var(--green-dark); }
        .cart-badge { position:absolute; top:-8px; right:-8px; background:var(--green); color:#fff; border-radius:50%; width:18px; height:18px; font-size:10px; display:flex; align-items:center; justify-content:center; font-weight:600; }
        .cart-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1099; display:none; }
        .cart-sidebar { position:fixed; right:0; top:0; width:420px; max-width:95vw; height:100vh; background:#fff; z-index:1100; transform:translateX(100%); transition:transform .3s ease; display:flex; flex-direction:column; box-shadow:-4px 0 24px rgba(0,0,0,0.12); }
        .cart-sidebar.open { transform:translateX(0); }
        .cart-sidebar-header { padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .cart-sidebar-body { flex:1; overflow-y:auto; padding:16px 24px; }
        .cart-sidebar-footer { padding:16px 24px; border-top:1px solid var(--border); background:#fafafa; }
        .cart-item { display:flex; gap:12px; padding:12px 0; border-bottom:1px solid var(--border); }
        .cart-item img { width:72px; height:72px; object-fit:cover; border-radius:8px; background:var(--gray); }
        .cart-item-info { flex:1; }
        .cart-item-name { font-weight:600; font-size:14px; margin-bottom:4px; }
        .cart-item-meta { color:var(--text-muted); font-size:12px; }
        .cart-item-price { font-weight:600; font-size:14px; color:var(--green-dark); white-space:nowrap; }
        .cart-qty-btn { width:28px; height:28px; border:1px solid var(--border); background:#fff; cursor:pointer; font-size:14px; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; }
        .cart-remove { cursor:pointer; color:#ccc; font-size:16px; background:none; border:none; padding:0; }
        .cart-remove:hover { color:#e53935; }
        .btn-checkout { width:100%; background:var(--black); color:#fff; border:none; padding:14px; font-size:14px; font-weight:600; letter-spacing:1px; text-transform:uppercase; cursor:pointer; border-radius:4px; transition:background .2s; text-decoration:none; display:block; text-align:center; }
        .btn-checkout:hover { background:var(--green-dark); color:#fff; }
        .btn-primary-green { background:var(--green); color:#fff; border:none; padding:12px 28px; font-weight:600; font-size:13px; letter-spacing:1px; text-transform:uppercase; cursor:pointer; border-radius:2px; transition:background .2s; text-decoration:none; display:inline-block; }
        .btn-primary-green:hover { background:var(--green-dark); color:#fff; }
        .btn-outline-green { background:transparent; color:var(--green-dark); border:2px solid var(--green); padding:10px 24px; font-weight:600; font-size:13px; letter-spacing:1px; text-transform:uppercase; cursor:pointer; border-radius:2px; transition:all .2s; text-decoration:none; display:inline-block; }
        .btn-outline-green:hover { background:var(--green); color:#fff; }
        .btn-black { background:var(--black); color:#fff; border:none; padding:12px 28px; font-weight:600; font-size:13px; letter-spacing:1px; text-transform:uppercase; cursor:pointer; border-radius:2px; transition:background .2s; text-decoration:none; display:inline-block; }
        .btn-black:hover { background:#333; color:#fff; }
        .product-card { border:none; background:#fff; transition:box-shadow .2s; overflow:hidden; }
        .product-card:hover { box-shadow:0 4px 24px rgba(0,0,0,0.08); }
        .product-card .product-img { width:100%; aspect-ratio:4/5; object-fit:cover; background:var(--gray); transition:transform .3s; display:block; }
        .product-card:hover .product-img { transform:scale(1.03); }
        .product-card-body { padding:12px 0; }
        .product-card-name { font-size:14px; font-weight:500; color:var(--black); text-decoration:none; }
        .product-card-name:hover { color:var(--green-dark); }
        .product-price { font-weight:600; color:var(--black); font-size:14px; }
        .product-price-old { text-decoration:line-through; color:var(--text-muted); font-size:13px; margin-right:6px; }
        .badge-discount { background:var(--green); color:#fff; font-size:11px; padding:2px 8px; border-radius:2px; font-weight:600; }
        .cat-card { position:relative; overflow:hidden; border-radius:4px; cursor:pointer; display:block; text-decoration:none; background:#1a1a1a; }
        .cat-card img { width:100%; aspect-ratio:3/4; object-fit:cover; display:block; transition:transform .5s ease, filter .35s ease; }
        .cat-card::after {
            content:'';
            position:absolute; inset:0;
            background:rgba(0,0,0,0.32);
            z-index:1;
            transition:background .35s ease;
            pointer-events:none;
        }
        .cat-card:hover img { transform:scale(1.06); }
        .cat-card:hover::after { background:rgba(0,0,0,0.26); }
        .cat-card-label {
            position:absolute; inset:0; z-index:2;
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            text-align:center;
            padding:24px 20px;
            color:#fff;
            pointer-events:none;
        }
        .cat-card-label h3 {
            font-size:clamp(17px,2.2vw,22px);
            font-weight:600;
            margin:0;
            letter-spacing:.04em;
            text-transform:uppercase;
            text-shadow:0 2px 16px rgba(0,0,0,0.45);
        }
        .cat-card-label p {
            text-shadow:0 1px 10px rgba(0,0,0,0.4);
        }
        .flash-container { position:fixed; top:80px; right:20px; z-index:9999; width:320px; }
        .cookie-banner {
            position:fixed; bottom:24px; left:50%; transform:translateX(-50%);
            width:min(680px, calc(100vw - 32px));
            background:#fff; color:var(--black);
            border-radius:16px; box-shadow:0 8px 40px rgba(0,0,0,.18);
            z-index:9000; border:1px solid #eee;
            font-size:13px;
        }
        .cookie-banner a { color:var(--green-dark); }
        .cookie-panel { padding:20px 24px; }
        .cookie-panel-detail { display:none; padding:0 24px 20px; border-top:1px solid #f0f0f0; }
        .cookie-toggle { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f5f5f5; }
        .cookie-toggle:last-child { border-bottom:none; }
        .cookie-switch { position:relative; width:40px; height:22px; flex-shrink:0; }
        .cookie-switch input { opacity:0; width:0; height:0; }
        .cookie-switch-slider { position:absolute; inset:0; background:#ddd; border-radius:22px; cursor:pointer; transition:.2s; }
        .cookie-switch-slider:before { content:''; position:absolute; width:16px; height:16px; left:3px; top:3px; background:#fff; border-radius:50%; transition:.2s; }
        .cookie-switch input:checked + .cookie-switch-slider { background:var(--green-dark); }
        .cookie-switch input:checked + .cookie-switch-slider:before { transform:translateX(18px); }
        .cookie-switch input:disabled + .cookie-switch-slider { opacity:.5; cursor:not-allowed; }
        footer { background:#1a1a1a; color:#ccc; padding:60px 40px 30px; margin-top:80px; }
        footer .footer-brand { font-family:'Simplified Arabic Fixed',serif; font-size:30px; font-weight:normal; color:#fff; margin-bottom:12px; }
        footer a { color:#ccc; text-decoration:none; font-size:13px; line-height:2; }
        footer a:hover { color:var(--green); }
        footer h6 { color:#fff; font-size:12px; letter-spacing:2px; text-transform:uppercase; margin-bottom:16px; }
        .footer-bottom { border-top:1px solid #333; margin-top:40px; padding-top:20px; font-size:12px; color:#666; text-align:center; }
        .section-title { text-align:center; margin-bottom:40px; }
        .section-title h2 { font-size:36px; font-weight:700; margin-bottom:8px; }
        .section-title p { color:var(--text-muted); font-size:15px; }
        .hamburger-btn { display:none; flex-direction:column; justify-content:center; gap:5px; background:none; border:none; cursor:pointer; padding:4px; width:32px; height:32px; }
        .hamburger-btn span { display:block; width:22px; height:2px; background:var(--black); border-radius:2px; transition:all .3s; }
        .hamburger-btn.open span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
        .hamburger-btn.open span:nth-child(2) { opacity:0; }
        .hamburger-btn.open span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }
        .mobile-nav { display:none; background:#fff; border-top:1px solid var(--border); }
        .mobile-nav.open { display:block; }
        .mobile-nav ul { list-style:none; margin:0; padding:0 20px; }
        .mobile-nav ul li a { display:block; padding:13px 0; font-size:13px; font-weight:500; letter-spacing:1.5px; text-transform:uppercase; color:var(--black); text-decoration:none; border-bottom:1px solid var(--border); }
        .mobile-nav ul li:last-child a { border-bottom:none; }
        .mobile-nav ul li a:hover, .mobile-nav ul li a.active { color:var(--green-dark); }
        .mobile-nav-bottom { display:flex; align-items:center; justify-content:space-between; padding:12px 20px 16px; border-top:1px solid var(--border); margin-top:4px; }
        .mobile-nav-bottom-link { font-size:14px; color:var(--black); text-decoration:none; display:flex; align-items:center; gap:8px; font-weight:500; }
        .mobile-nav-bottom-link i { font-size:20px; }
        .mobile-lang-btn { background:var(--gray); border:1px solid var(--border); padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:var(--black); text-decoration:none; }
        .mobile-lang-btn:hover { background:var(--green-light); color:var(--green-dark); text-decoration:none; }
        @media(max-width:768px){.navbar-main{padding:12px 20px;position:relative;} .nav-links{display:none;} .announcement-bar{display:none;} .hamburger-btn{display:flex;} .navbar-brand-logo{position:absolute;left:50%;transform:translateX(-50%);} .nav-lang-desktop{display:none;} .nav-login-desktop{display:none;} footer{padding:40px 20px 20px;}}

        /* ── Scroll animations ── */
        .fade-up {
            opacity:0;
            transform:translateY(32px);
            transition:opacity .65s ease, transform .65s ease;
        }
        .fade-up.visible {
            opacity:1;
            transform:translateY(0);
        }
        
    </style>
    @yield('head')
</head>
<body style="margin:0;padding:0;display:flow-root;">

{{-- ── Page Loader ────────────────────────────────────────────── --}}
<div id="page-loader">
    <div id="page-loader-inner">
        <span id="page-loader-brand">.rosmarino</span>
        <div id="page-loader-bar-wrap">
            <div id="page-loader-bar"></div>
        </div>
    </div>
</div>

<div class="announcement-bar">{{ __('front.announcement') }}</div>
@php $cartItems = session('cart', []); $cartCount = collect($cartItems)->sum('quantity'); @endphp

<header class="site-header">
    <div class="navbar-main">
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <a href="{{ route('home') }}" class="navbar-brand-logo">.rosmarino</a>
        <ul class="nav-links">
            <li><a href="{{ route('collection') }}" class="{{ request()->routeIs('collection*','category*') ? 'active' : '' }}">{{ __('front.nav_collection') }}</a></li>
            <li><a href="{{ route('best-sellers') }}" class="{{ request()->routeIs('best-sellers') ? 'active' : '' }}">{{ __('front.nav_best_sellers') }}</a></li>
            <li><a href="{{ route('photos') }}" class="{{ request()->routeIs('photos') ? 'active' : '' }}">{{ __('front.nav_photos') }}</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('front.nav_about') }}</a></li>
        </ul>
        <div class="nav-actions">
            @auth
                <a href="{{ route('account') }}" title="{{ __('front.nav_my_account') }}" class="nav-login-desktop"><i class="bi bi-person"></i></a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" title="{{ __('front.nav_admin_panel') }}"><i class="bi bi-grid"></i></a>
                @endif
            @else
                <a href="{{ route('login') }}" title="{{ __('front.nav_login') }}" class="nav-login-desktop"><i class="bi bi-person"></i></a>
            @endauth
            <a href="#" id="cartToggle" title="{{ __('front.nav_cart') }}">
                <i class="bi bi-bag"></i>
                @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
            </a>
            <a href="{{ route('lang.switch', app()->getLocale() === 'it' ? 'en' : 'it') }}" title="{{ __('front.lang_switch_label') }}" class="nav-lang-desktop" style="font-size:13px;font-weight:600;letter-spacing:1px;">{{ __('front.lang_switch') }}</a>
        </div>
    </div>
    <nav class="mobile-nav" id="mobileNav">
        <ul>
            <li><a href="{{ route('collection') }}" class="{{ request()->routeIs('collection*','category*') ? 'active' : '' }}">{{ __('front.nav_collection') }}</a></li>
            <li><a href="{{ route('best-sellers') }}" class="{{ request()->routeIs('best-sellers') ? 'active' : '' }}">{{ __('front.nav_best_sellers') }}</a></li>
            <li><a href="{{ route('photos') }}" class="{{ request()->routeIs('photos') ? 'active' : '' }}">{{ __('front.nav_photos') }}</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('front.nav_about') }}</a></li>
        </ul>
        <div class="mobile-nav-bottom">
            @auth
                <a href="{{ route('account') }}" class="mobile-nav-bottom-link"><i class="bi bi-person"></i>{{ __('front.nav_my_account') }}</a>
            @else
                <a href="{{ route('login') }}" class="mobile-nav-bottom-link"><i class="bi bi-person"></i>{{ __('front.nav_login') }}</a>
            @endauth
            <a href="{{ route('lang.switch', app()->getLocale() === 'it' ? 'en' : 'it') }}" class="mobile-lang-btn">{{ __('front.lang_switch') }}</a>
        </div>
    </nav>
</header>

<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-sidebar-header">
        <h5 class="mb-0" style="font-size:18px;">{{ __('front.cart_title') }} <span class="text-muted" style="font-size:14px;font-family:var(--font-sans);">({{ $cartCount }})</span></h5>
        <button class="btn-close" id="cartClose"></button>
    </div>
    <div class="cart-sidebar-body">
        @if(count($cartItems) > 0)
            @foreach($cartItems as $key => $item)
                <div class="cart-item">
                    <img src="{{ !empty($item['image']) ? asset($item['image']) : 'https://placehold.co/72x72/f5f5f5/999?text=🐾' }}" alt="{{ $item['name'] }}">
                    <div class="cart-item-info">
                        <div class="cart-item-name">{{ $item['name'] }}</div>
                        <div class="cart-item-meta">{{ ucfirst($item['color']) }} · {{ strtoupper($item['size']) }}</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <form method="POST" action="{{ route('cart.update') }}" class="d-inline">
                                @csrf <input type="hidden" name="key" value="{{ $key }}">
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                <button class="cart-qty-btn">−</button>
                            </form>
                            <span style="font-weight:600;">{{ $item['quantity'] }}</span>
                            <form method="POST" action="{{ route('cart.update') }}" class="d-inline">
                                @csrf <input type="hidden" name="key" value="{{ $key }}">
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                <button class="cart-qty-btn">+</button>
                            </form>
                            <form method="POST" action="{{ route('cart.remove') }}" class="d-inline ms-auto">
                                @csrf <input type="hidden" name="key" value="{{ $key }}">
                                <button class="cart-remove"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="cart-item-price">€{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="bi bi-bag" style="font-size:48px;color:#ddd;"></i>
                <p class="mt-3 text-muted">{{ __('front.cart_empty') }}</p>
                <a href="{{ route('collection') }}" class="btn-primary-green mt-2">{{ __('front.cart_shop_now') }}</a>
            </div>
        @endif
    </div>
    @if(count($cartItems) > 0)
        <div class="cart-sidebar-footer">
            <div class="d-flex justify-content-between mb-3">
                <span style="font-weight:600;">{{ __('front.cart_subtotal') }}</span>
                <span style="font-weight:600;">€{{ number_format(collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }}</span>
            </div>
            <a href="{{ route('checkout') }}" class="btn-checkout">{{ __('front.cart_checkout_btn') }}</a>
            <p class="text-center text-muted mt-2" style="font-size:12px;">{{ __('front.cart_shipping_note') }}</p>
        </div>
    @endif
</div>

<div class="flash-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<main>@yield('content')</main>

<footer>
    <div style="max-width:1400px;margin:0 auto;">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="footer-brand">.rosmarino</div>
                <p style="font-size:14px;line-height:1.8;">{{ __('front.footer_tagline') }}</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#"><i class="bi bi-pinterest fs-5"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2 mb-4">
                <h6>{{ __('front.footer_shop') }}</h6>
                <div class="d-flex flex-column">
                    <a href="{{ route('collection') }}">{{ __('front.footer_collection') }}</a>
                    <a href="{{ route('best-sellers') }}">{{ __('front.footer_best_sellers') }}</a>
                    <a href="{{ route('photos') }}">{{ __('front.footer_photos') }}</a>
                </div>
            </div>
            <div class="col-6 col-lg-2 mb-4">
                <h6>{{ __('front.footer_info') }}</h6>
                <div class="d-flex flex-column">
                    <a href="{{ route('about') }}">{{ __('front.footer_about') }}</a>
                    <a href="#">{{ __('front.footer_faq') }}</a>
                    <a href="#">{{ __('front.footer_shipping') }}</a>
                </div>
            </div>
            <div class="col-6 col-lg-2 mb-4">
                <h6>{{ __('front.footer_account') }}</h6>
                <div class="d-flex flex-column">
                    @auth
                        <a href="{{ route('account') }}">{{ __('front.footer_my_account') }}</a>
                        <a href="{{ route('account.orders') }}">{{ __('front.footer_my_orders') }}</a>
                        <a href="#" onclick="event.preventDefault();document.getElementById('footerLogout').submit();">{{ __('front.footer_logout') }}</a>
                        <form id="footerLogout" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
                    @else
                        <a href="{{ route('login') }}">{{ __('front.footer_login') }}</a>
                        <a href="{{ route('register') }}">{{ __('front.footer_register') }}</a>
                    @endauth
                </div>
            </div>
            <div class="col-6 col-lg-2 mb-4">
                <h6>{{ __('front.footer_legal') }}</h6>
                <div class="d-flex flex-column">
                    <a href="#">{{ __('front.footer_privacy') }}</a>
                    <a href="#">{{ __('front.footer_terms') }}</a>
                    <a href="#">{{ __('front.footer_cookies') }}</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">© {{ date('Y') }} .rosmarino. {{ __('front.footer_rights') }} | <a href="{{ route('sitemap') }}">Sitemap</a></div>
    </div>
</footer>

@if(!session('cookie_consent'))
<div class="cookie-banner" id="cookieBanner">
    <div class="cookie-panel">
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px;">
            <span style="font-size:22px;line-height:1;">🍪</span>
            <div>
                <p style="font-weight:700;font-size:14px;margin:0 0 4px;">{{ __('front.cookie_title') }}</p>
                <p style="margin:0;color:#555;font-size:13px;line-height:1.5;">{{ __('front.cookie_msg') }}
                    <a href="#" id="cookieDetailToggle" style="white-space:nowrap;">{{ __('front.cookie_learn') }}</a>
                </p>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="cookieSave('all')" class="btn btn-sm btn-success px-4" style="font-size:13px;border-radius:8px;">{{ __('front.cookie_accept') }}</button>
            <button onclick="document.getElementById('cookieDetailPanel').style.display='block';document.getElementById('cookieDetailToggle').style.display='none';" class="btn btn-sm btn-outline-secondary px-3" style="font-size:13px;border-radius:8px;" id="cookieCustomBtn">{{ __('front.cookie_customize') }}</button>
            <button onclick="cookieSave('necessary')" class="btn btn-sm btn-link px-2" style="font-size:12px;color:#999;">{{ __('front.cookie_necessary') }}</button>
        </div>
    </div>

    <div class="cookie-panel-detail" id="cookieDetailPanel">
        <div class="cookie-toggle">
            <div>
                <p style="font-weight:600;margin:0 0 2px;font-size:13px;">{{ __('front.cookie_cat_necessary') }}</p>
                <p style="margin:0;font-size:12px;color:#999;">{{ __('front.cookie_cat_necessary_desc') }}</p>
            </div>
            <label class="cookie-switch">
                <input type="checkbox" checked disabled>
                <span class="cookie-switch-slider"></span>
            </label>
        </div>
        <div class="cookie-toggle">
            <div>
                <p style="font-weight:600;margin:0 0 2px;font-size:13px;">{{ __('front.cookie_cat_analytics') }}</p>
                <p style="margin:0;font-size:12px;color:#999;">{{ __('front.cookie_cat_analytics_desc') }}</p>
            </div>
            <label class="cookie-switch">
                <input type="checkbox" id="ckAnalytics" checked>
                <span class="cookie-switch-slider"></span>
            </label>
        </div>
        <div class="cookie-toggle">
            <div>
                <p style="font-weight:600;margin:0 0 2px;font-size:13px;">{{ __('front.cookie_cat_marketing') }}</p>
                <p style="margin:0;font-size:12px;color:#999;">{{ __('front.cookie_cat_marketing_desc') }}</p>
            </div>
            <label class="cookie-switch">
                <input type="checkbox" id="ckMarketing">
                <span class="cookie-switch-slider"></span>
            </label>
        </div>
        <div style="margin-top:14px;">
            <button onclick="cookieSave('custom')" class="btn btn-sm btn-success px-4" style="font-size:13px;border-radius:8px;">{{ __('front.cookie_save') }}</button>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const cartToggle=document.getElementById('cartToggle'),cartSidebar=document.getElementById('cartSidebar'),cartOverlay=document.getElementById('cartOverlay');
    function openCart(){cartSidebar.classList.add('open');cartOverlay.style.display='block';document.body.style.overflow='hidden';}
    function closeCart(){cartSidebar.classList.remove('open');cartOverlay.style.display='none';document.body.style.overflow='';}
    function refreshCartSidebar(){
        return fetch(window.location.href).then(r=>r.text()).then(html=>{
            const doc=new DOMParser().parseFromString(html,'text/html');
            const ns=doc.querySelector('#cartSidebar');
            if(ns) cartSidebar.innerHTML=ns.innerHTML;
            const nb=doc.querySelector('.cart-badge');
            const ct=document.getElementById('cartToggle');
            if(ct){
                let ob=ct.querySelector('.cart-badge');
                if(nb){if(ob)ob.textContent=nb.textContent;else{const b=document.createElement('span');b.className='cart-badge';b.textContent=nb.textContent;ct.appendChild(b);}}
                else if(ob) ob.remove();
            }
        });
    }
    if(cartToggle) cartToggle.addEventListener('click',e=>{e.preventDefault();openCart();});
    if(cartOverlay) cartOverlay.addEventListener('click',closeCart);
    if(cartSidebar) cartSidebar.addEventListener('click',function(e){
        if(e.target.closest('#cartClose')){closeCart();return;}
        const btn=e.target.closest('.cart-qty-btn,.cart-remove');
        if(btn){
            e.preventDefault();
            const form=btn.closest('form');
            if(!form) return;
            const fd=new FormData(form);
            fetch(form.action,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:fd})
                .then(()=>refreshCartSidebar()).catch(()=>{});
        }
    });
    setTimeout(()=>document.querySelectorAll('.flash-container .alert').forEach(el=>bootstrap.Alert.getOrCreateInstance(el)?.close()),4000);
    const hamburgerBtn=document.getElementById('hamburgerBtn'),mobileNav=document.getElementById('mobileNav');
    if(hamburgerBtn&&mobileNav){hamburgerBtn.addEventListener('click',function(){const open=mobileNav.classList.toggle('open');hamburgerBtn.classList.toggle('open',open);hamburgerBtn.setAttribute('aria-expanded',open);});}
    const acceptCookies=document.getElementById('acceptCookies');
    if(acceptCookies) acceptCookies.addEventListener('click',()=>{
        fetch('{{ route("cookie.consent") }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}})
        .then(()=>document.getElementById('cookieBanner').style.display='none');
    });

    function cookieSave(choice){
        const analytics = choice==='all' ? true : (choice==='necessary' ? false : document.getElementById('ckAnalytics')?.checked ?? false);
        const marketing = choice==='all' ? true : (choice==='necessary' ? false : document.getElementById('ckMarketing')?.checked ?? false);
        fetch('{{ route("cookie.consent") }}',{
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
            body: JSON.stringify({choice, analytics, marketing})
        }).then(()=>{
            const b=document.getElementById('cookieBanner');
            if(b){ b.style.transition='opacity .3s'; b.style.opacity='0'; setTimeout(()=>b.remove(),300); }
        });
    }

    // ── Fade-up scroll animations ──
    (function(){
        const SELECTORS = [
            '.section-title',
            '.product-card',
            '.cat-card',
            '.feature',
            '.cat-hero > *',
            '.cat-panel > *',
            'section > p, section > h1, section > h2, section > h3',
            '.features-strip .feature',
        ].join(',');

        function init(){
            document.querySelectorAll(SELECTORS).forEach(function(el,i){
                if(el.closest('.hero-gallery, .hero-content, .cart-sidebar, .site-header, footer')) return;
                el.classList.add('fade-up');
                el.style.transitionDelay = (Math.min(i % 6, 5) * 80) + 'ms';
            });
            const io = new IntersectionObserver(function(entries){
                entries.forEach(function(e){
                    if(e.isIntersecting){
                        e.target.classList.add('visible');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12 });
            document.querySelectorAll('.fade-up').forEach(function(el){ io.observe(el); });
        }

        if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();
    })();
</script>
@yield('scripts')
@stack('scripts')

<style>
    #page-loader {
        position: fixed;
        inset: 0;
        background: #fff;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #page-loader-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    #page-loader-brand {
        font-family: 'Simplified Arabic Fixed', serif;
        font-size: 42px;
        font-weight: 700;
        color: #9bc3b1;
        letter-spacing: 0;
        line-height: 1;
    }
    #page-loader-bar-wrap {
        width: 120px;
        height: 2px;
        background: #e8e8e8;
        border-radius: 2px;
        overflow: hidden;
    }
    #page-loader-bar {
        height: 100%;
        width: 0;
        background: #9bc3b1;
        border-radius: 2px;
        animation: loaderFill 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes loaderFill {
        0%   { width: 0; }
        60%  { width: 75%; }
        100% { width: 100%; }
    }
</style>
<script>
    (function () {
        var loader = document.getElementById('page-loader');
        function hideLoader() {
            loader.classList.add('hidden');
        }
        if (document.readyState === 'complete') {
            setTimeout(hideLoader, 950);
        } else {
            window.addEventListener('load', function () {
                setTimeout(hideLoader, 950);
            });
        }
    })();
</script>
</body>
</html>
