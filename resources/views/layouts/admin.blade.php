<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') – .rosmarino Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @yield('head')
    <style>
        *,*::before,*::after{box-sizing:border-box;}
        :root{
            --green:#9BC3B1; --green-dark:#6fa398; --green-light:#e0f0eb;
            --black:#1a1a1a; --sidebar:#1a1a1a; --sidebar-width:240px;
        }
        body{font-family:'Inter',sans-serif;background:#f7f8fa;color:var(--black);margin:0;}
        a{text-decoration:none;}

        /* Sidebar */
        .admin-sidebar{
            position:fixed;left:0;top:0;bottom:0;width:var(--sidebar-width);
            background:var(--sidebar);color:#fff;overflow-y:auto;z-index:100;
            display:flex;flex-direction:column;
        }
        .admin-sidebar-logo{
            padding:24px 20px;border-bottom:1px solid rgba(255,255,255,0.1);
            font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#fff;
            letter-spacing:-0.5px;
        }
        .admin-sidebar-logo span{color:var(--green);}
        .admin-nav{flex:1;padding:16px 0;}
        .admin-nav-section{font-size:10px;letter-spacing:2px;text-transform:uppercase;
            color:rgba(255,255,255,0.4);padding:16px 20px 6px;font-weight:600;}
        .admin-nav a{
            display:flex;align-items:center;gap:10px;
            padding:10px 20px;color:rgba(255,255,255,0.75);font-size:14px;
            transition:all .2s;border-radius:8px;margin:1px 8px;
        }
        .admin-nav a:hover,.admin-nav a.active{
            color:#fff;background:rgba(154,215,160,0.15);
        }
        .admin-nav a.active{color:var(--green);}
        .admin-nav a i{font-size:16px;width:20px;text-align:center;}
        .admin-sidebar-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,0.1);}
        .admin-sidebar-footer a{color:rgba(255,255,255,0.5);font-size:13px;}
        .admin-sidebar-footer a:hover{color:#fff;}

        /* Main content */
        .admin-main{margin-left:var(--sidebar-width);min-height:100vh;display:flex;flex-direction:column;}
        .admin-topbar{
            background:#fff;border-bottom:1px solid #eee;
            padding:16px 32px;display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;z-index:50;
        }
        .admin-topbar h2{font-size:18px;font-weight:700;margin:0;}
        .admin-content{padding:32px;flex:1;}

        /* Cards */
        .stat-card{background:#fff;border-radius:16px;padding:24px;border:1px solid #eee;}
        .stat-card .stat-value{font-size:32px;font-weight:700;color:var(--black);}
        .stat-card .stat-label{font-size:12px;text-transform:uppercase;letter-spacing:1.5px;color:#888;margin-bottom:8px;}
        .stat-card .stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;}

        /* Tables */
        .admin-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #eee;}
        .admin-table thead th{background:#f9fafb;padding:12px 16px;font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#666;font-weight:600;border-bottom:1px solid #eee;}
        .admin-table tbody td{padding:14px 16px;border-bottom:1px solid #f0f0f0;font-size:14px;}
        .admin-table tbody tr:last-child td{border-bottom:none;}
        .admin-table tbody tr:hover{background:#fafafa;}

        /* Badges */
        .badge-status{display:inline-flex;align-items:center;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;}

        /* Buttons */
        .btn-admin-primary{background:var(--green-dark);color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:background .2s;}
        .btn-admin-primary:hover{background:var(--green);}
        .btn-admin-secondary{background:#fff;color:var(--black);border:1.5px solid #ddd;border-radius:8px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .2s;}
        .btn-admin-secondary:hover{border-color:var(--black);}
        .btn-admin-danger{background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;}
        .btn-admin-danger:hover{background:#dc2626;color:#fff;}

        .form-control,.form-select{border:1.5px solid #ddd;border-radius:8px;padding:10px 14px;font-size:14px;font-family:'Inter',sans-serif;}
        .form-control:focus,.form-select:focus{border-color:var(--green-dark);box-shadow:0 0 0 3px rgba(109,191,119,0.15);outline:none;}
        label.form-label{font-size:13px;font-weight:600;margin-bottom:6px;display:block;}

        @media(max-width:768px){
            .admin-sidebar{transform:translateX(-100%);transition:transform .3s;}
            .admin-sidebar.open{transform:none;}
            .admin-main{margin-left:0;}
        }
    </style>
</head>
<body>

<div class="admin-sidebar">
    <div class="admin-sidebar-logo">
        <span style="font-family:'Simplified Arabic Fixed',serif;font-size:26px;font-weight:700;color:#9bc3b1;letter-spacing:0;">.rosmarino</span>
        <small style="font-size:11px;opacity:0.5;display:block;font-family:'Inter',sans-serif;font-weight:400;letter-spacing:2px;text-transform:uppercase;">Pannello Admin</small>
    </div>
    <nav class="admin-nav">
        <div class="admin-nav-section">Panoramica</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="admin-nav-section">Catalogo</div>
        <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}">
            <i class="bi bi-bag"></i> Prodotti
        </a>
        <a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Categorie
        </a>

        <div class="admin-nav-section">Vendite</div>
        <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Ordini
        </a>
        <a href="{{ route('admin.discounts') }}" class="{{ request()->routeIs('admin.discounts') ? 'active' : '' }}">
            <i class="bi bi-percent"></i> Sconti
        </a>

        <div class="admin-nav-section">Utenti & Marketing</div>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Utenti
        </a>
        <a href="{{ route('admin.campaigns') }}" class="{{ request()->routeIs('admin.campaigns*') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i> Campagne Email
        </a>

        <div class="admin-nav-section">Analisi</div>
        <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Report
        </a>

        <div class="admin-nav-section">Contenuti</div>
        <a href="{{ route('admin.collection') }}" class="{{ request()->routeIs('admin.collection*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Collezione
        </a>
        <a href="{{ route('admin.gallery') }}" class="{{ request()->routeIs('admin.gallery*') ? 'active' : '' }}">
            <i class="bi bi-images"></i> Gallery Home
        </a>
        <a href="{{ route('admin.cookie-consents') }}" class="{{ request()->routeIs('admin.cookie-consents') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> Consensi Cookie
        </a>
        <a href="{{ route('admin.analytics') }}" class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Analytics
        </a>
    </nav>
    <div class="admin-sidebar-footer">
        <a href="{{ route('home') }}"><i class="bi bi-arrow-left me-2"></i>Torna al negozio</a><br>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:8px;">
            @csrf
            <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.5);font-size:13px;cursor:pointer;padding:0;font-family:'Inter',sans-serif;">
                <i class="bi bi-box-arrow-right me-2"></i>Esci
            </button>
        </form>
    </div>
</div>

<div class="admin-main">
    <div class="admin-topbar">
        <h2>@yield('page_title', 'Dashboard')</h2>
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:14px;color:#666;">{{ auth()->user()?->name ?? 'Admin' }}</span>
            <div style="width:36px;height:36px;background:var(--green-light);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-person" style="color:var(--green-dark);"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="margin:16px 32px 0;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 16px;font-size:14px;color:#15803d;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin:16px 32px 0;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;font-size:14px;color:#dc2626;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="admin-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>

