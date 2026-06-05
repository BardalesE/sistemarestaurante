<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 260px;
            --topbar-h: 60px;
            --bottomnav-h: 65px;
            --brand:      #c0392b;
            --brand-dark: #962d22;
            --sidebar-bg: #1c1c2e;
            --sidebar-txt: rgba(255,255,255,.82);
            --sidebar-hover: rgba(255,255,255,.09);
            --sidebar-border: rgba(255,255,255,.08);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ─────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        .sidebar-header {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .logo-box {
            width: 42px; height: 42px;
            background: var(--brand);
            color: #fff;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800;
            flex-shrink: 0;
        }

        .brand-name {
            font-weight: 800; font-size: 16px;
            color: #fff; letter-spacing: -.3px; line-height: 1.2;
        }

        .sidebar-menu {
            padding: 10px 8px;
            flex-grow: 1;
            overflow-y: auto; overflow-x: hidden;
        }
        .sidebar-menu::-webkit-scrollbar { width: 3px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 3px; }

        .menu-category {
            font-size: .67rem; font-weight: 700;
            color: rgba(255,255,255,.3);
            text-transform: uppercase;
            margin: 14px 12px 5px;
            letter-spacing: 1px;
        }

        .nav-link {
            color: var(--sidebar-txt);
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 8px;
            transition: all .17s;
            margin-bottom: 2px;
            display: flex; align-items: center; gap: 10px;
            font-size: .88rem;
            white-space: nowrap;
        }
        .nav-link i {
            font-size: 1.05rem;
            color: rgba(255,255,255,.4);
            width: 20px; text-align: center; flex-shrink: 0;
        }
        .nav-link:hover { background: var(--sidebar-hover); color: #fff; }
        .nav-link:hover i { color: rgba(255,255,255,.85); }
        .nav-link.active { background: var(--brand); color: #fff; font-weight: 600; }
        .nav-link.active i { color: #fff; }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .user-card {
            background: rgba(255,255,255,.07);
            padding: 10px 12px; border-radius: 10px;
            display: flex; align-items: center; gap: 10px;
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: var(--brand); color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 15px; flex-shrink: 0;
        }

        /* ── OVERLAY ─────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1040;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }

        /* ── MOBILE TOP BAR ──────────────────────── */
        .mobile-topbar {
            display: none;
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: var(--sidebar-bg);
            align-items: center;
            padding: 0 14px;
            z-index: 1030;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
            gap: 10px;
        }
        .hamburger-btn {
            background: none; border: none;
            color: #fff; font-size: 1.45rem;
            padding: 6px 8px; border-radius: 7px;
            cursor: pointer; line-height: 1;
            flex-shrink: 0;
        }
        .hamburger-btn:hover { background: rgba(255,255,255,.1); }
        .mobile-brand {
            font-weight: 800; font-size: 15px;
            color: #fff; flex-grow: 1;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        /* ── MAIN CONTENT ────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            padding: 28px 28px 28px;
            min-height: 100vh;
        }

        /* ── BOTTOM NAV (mobile) ─────────────────── */
        .mobile-bottom-nav {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0;
            height: var(--bottomnav-h);
            background: #fff;
            border-top: 1px solid #e5e7eb;
            z-index: 1030;
            box-shadow: 0 -4px 20px rgba(0,0,0,.08);
        }
        .bottom-nav-items {
            display: flex; height: 100%;
        }
        .bottom-nav-item {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 3px;
            color: #9ca3af;
            text-decoration: none;
            font-size: .62rem; font-weight: 600;
            border: none; background: none;
            cursor: pointer;
            transition: color .15s;
            padding: 6px 4px;
            -webkit-tap-highlight-color: transparent;
        }
        .bottom-nav-item i { font-size: 1.35rem; }
        .bottom-nav-item.active,
        .bottom-nav-item:hover { color: var(--brand); }
        .bottom-nav-item.active i { transform: translateY(-2px); }

        /* ── GENERAL ─────────────────────────────── */
        .card { border-radius: 12px; }
        .alert { border-radius: 10px; }
        .btn { border-radius: 8px; }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content {
                margin-left: 0;
                padding: calc(var(--topbar-h) + 16px) 14px calc(var(--bottomnav-h) + 12px);
            }
            .mobile-topbar { display: flex; }
            .mobile-bottom-nav { display: block; }
            body.sidebar-open { overflow: hidden; }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding-left: 12px;
                padding-right: 12px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

@php
    $logo  = \App\Models\Setting::where('key', 'company_logo')->value('value');
    $name  = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante';
    $role  = Auth::user()->role;
@endphp

{{-- ── MOBILE TOP BAR ──────────────────────── --}}
<div class="mobile-topbar" id="mobileTopbar">
    <button class="hamburger-btn" id="sidebarToggle" aria-label="Abrir menú">
        <i class="bi bi-list"></i>
    </button>
    <span class="mobile-brand">{{ $name }}</span>
    <a href="{{ route('pos.index') }}" class="btn btn-sm btn-danger fw-bold px-3 rounded-pill flex-shrink-0">
        <i class="bi bi-cart-plus"></i>
    </a>
</div>

{{-- ── OVERLAY ──────────────────────────────── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── SIDEBAR ──────────────────────────────── --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        @if($logo)
            <img src="{{ asset('storage/'.$logo) }}"
                 style="width:42px;height:42px;object-fit:cover;border-radius:10px;flex-shrink:0;">
        @else
            <div class="logo-box"><i class="bi bi-shop"></i></div>
        @endif
        <div style="overflow:hidden;">
            <div class="brand-name text-truncate" title="{{ $name }}">{{ $name }}</div>
            <div style="font-size:.68rem;color:rgba(255,255,255,.35);font-weight:500;letter-spacing:.4px;">
                Sistema Restaurante
            </div>
        </div>
    </div>

    <div class="sidebar-menu">

        @if(in_array($role, ['admin', 'cashier']))
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
        @endif

        @if($role === 'admin')
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i><span>Reportes Gerenciales</span>
            </a>
        @endif

        <div class="menu-category">Operaciones</div>

        <a href="{{ route('pos.index') }}"
           class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-cart4"></i><span>Punto de Venta (POS)</span>
        </a>

        <a href="{{ route('reservations.index') }}"
           class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i><span>Reservas y Agenda</span>
        </a>

        @if(in_array($role, ['admin', 'cashier']))
            <a href="{{ route('sales.index') }}"
               class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i><span>Caja / Historial</span>
            </a>
        @endif

        <a href="{{ route('kitchen.index') }}"
           class="nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
            <i class="bi bi-fire"></i><span>Monitor Cocina (KDS)</span>
        </a>

        <div class="menu-category">Gestión</div>

        <a href="{{ route('clients.index') }}"
           class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i><span>Clientes (CRM)</span>
        </a>

        @if($role === 'admin')
            <a href="{{ route('categories.index') }}"
               class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i><span>Categorías</span>
            </a>
            <a href="{{ route('products.index') }}"
               class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i><span>Inventario</span>
            </a>
            <a href="{{ route('tables.index') }}"
               class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap-fill"></i><span>Mesas / Salón</span>
            </a>
            <a href="{{ route('settings.index') }}"
               class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i><span>Configuración</span>
            </a>
            <a href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i><span>Usuarios</span>
            </a>
            <a href="{{ route('system.index') }}"
               class="nav-link {{ request()->routeIs('system.*') ? 'active' : '' }}"
               style="color:#f87171;">
                <i class="bi bi-exclamation-octagon-fill" style="color:#f87171;"></i>
                <span>Reset Sistema</span>
            </a>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
            <div style="flex-grow:1;overflow:hidden;min-width:0;">
                <div style="color:#fff;font-weight:600;font-size:.85rem;" class="text-truncate">
                    {{ Auth::user()->name ?? 'Usuario' }}
                </div>
                <div style="margin-top:3px;">
                    @if($role == 'admin')
                        <span class="badge bg-danger" style="font-size:.6rem;">Admin</span>
                    @elseif($role == 'cashier')
                        <span class="badge bg-primary" style="font-size:.6rem;">Cajero</span>
                    @else
                        <span class="badge bg-success" style="font-size:.6rem;">Mozo</span>
                    @endif
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                @csrf
                <button class="btn btn-sm border-0 p-1" title="Cerrar sesión"
                        style="color:rgba(255,255,255,.4);background:none;">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── MOBILE BOTTOM NAV ────────────────────── --}}
<nav class="mobile-bottom-nav" aria-label="Navegación rápida">
    <div class="bottom-nav-items">
        <a href="{{ route('pos.index') }}"
           class="bottom-nav-item {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-cart4"></i><span>POS</span>
        </a>
        <a href="{{ route('kitchen.index') }}"
           class="bottom-nav-item {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
            <i class="bi bi-fire"></i><span>Cocina</span>
        </a>
        <a href="{{ route('reservations.index') }}"
           class="bottom-nav-item {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i><span>Reservas</span>
        </a>
        @if(in_array($role, ['admin', 'cashier']))
            <a href="{{ route('sales.index') }}"
               class="bottom-nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i><span>Caja</span>
            </a>
            <a href="{{ route('dashboard') }}"
               class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i><span>Panel</span>
            </a>
        @else
            <a href="{{ route('clients.index') }}"
               class="bottom-nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i><span>Clientes</span>
            </a>
        @endif
    </div>
</nav>

{{-- ── MAIN CONTENT ─────────────────────────── --}}
<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var sidebar  = document.getElementById('sidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var toggle   = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.classList.add('sidebar-open');
    }
    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    if (toggle)  toggle.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Auto-close on nav-link click (mobile only)
    if (window.innerWidth <= 991) {
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });
    }
})();
</script>
@stack('scripts')
</body>
</html>
