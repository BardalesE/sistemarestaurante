<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante' }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        
        .sidebar {
            width: 260px; height: 100vh; position: fixed; top: 0; left: 0;
            background: #ffffff; border-right: 1px solid #e9ecef;
            display: flex; flex-direction: column; z-index: 1000;
        }
        .sidebar-header { padding: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f0f0f0; }
        .logo-box { width: 40px; height: 40px; background: #0d6efd; color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; }
        .brand-name { font-weight: 800; font-size: 18px; color: #0d6efd; letter-spacing: -0.5px; }
        
        .sidebar-menu { padding: 20px 10px; flex-grow: 1; overflow-y: auto; }
        .menu-category { font-size: 0.75rem; font-weight: 700; color: #adb5bd; text-transform: uppercase; margin: 15px 10px 5px; letter-spacing: 0.5px; }
        
        .nav-link { color: #495057; font-weight: 500; padding: 10px 15px; border-radius: 8px; transition: all 0.2s; margin-bottom: 2px; display: flex; align-items: center; }
        .nav-link i { font-size: 1.1rem; margin-right: 12px; color: #6c757d; }
        .nav-link:hover { background-color: #f1f3f5; color: #0d6efd; }
        .nav-link:hover i { color: #0d6efd; }
        .nav-link.active { background-color: #e7f1ff; color: #0d6efd; font-weight: 600; }
        .nav-link.active i { color: #0d6efd; }

        .sidebar-footer { padding: 15px; border-top: 1px solid #f0f0f0; }
        .user-card { background: #f8f9fa; padding: 10px; border-radius: 10px; display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 35px; height: 35px; background: #0d6efd; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
        
        .main-content { margin-left: 260px; padding: 30px; min-height: 100vh; }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        @php
            $logo = \App\Models\Setting::where('key', 'company_logo')->value('value');
            $name = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'Mi Restaurante';
            $role = Auth::user()->role;
        @endphp

        @if($logo)
            <img src="{{ asset('storage/'.$logo) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
        @else
            <div class="logo-box"><i class="bi bi-shop"></i></div>
        @endif
        
        <div class="brand-name text-truncate" title="{{ $name }}">{{ $name }}</div>
    </div>

    <div class="sidebar-menu">
        
        @if(in_array($role, ['admin', 'cashier']))
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        @endif

        @if($role === 'admin')
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Reportes Gerenciales
            </a>
        @endif

        <div class="menu-category">Operaciones</div>
        
        <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-cart4"></i> Punto de Venta (POS)
        </a>

        <a href="{{ route('reservations.index') }}" class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Reservas y Agenda
        </a>

        @if(in_array($role, ['admin', 'cashier']))
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Caja / Historial
            </a>
        @endif

        <a href="{{ route('kitchen.index') }}" class="nav-link {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
            <i class="bi bi-fire"></i> Monitor Cocina (KDS)
        </a>

        <div class="menu-category">Gestión</div>
        <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Clientes (CRM)
        </a>

        @if($role === 'admin')
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Categorías
            </a>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i> Inventario
            </a>
            <a href="{{ route('tables.index') }}" class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap-fill"></i> Mesas / Salón
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Configuración
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Usuarios
            </a>
            <a href="{{ route('system.index') }}" class="nav-link {{ request()->routeIs('system.*') ? 'active' : '' }} text-danger">
                <i class="bi bi-exclamation-octagon-fill"></i> Reset Sistema
            </a>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
            <div style="flex-grow:1; overflow:hidden;">
                <div class="fw-bold small text-truncate">{{ Auth::user()->name ?? 'Usuario' }}</div>
                <div class="text-muted" style="font-size: 11px;">
                    @if($role == 'admin') <span class="badge bg-danger">Administrador</span>
                    @elseif($role == 'cashier') <span class="badge bg-primary">Cajero</span>
                    @else <span class="badge bg-success">Mozo</span>
                    @endif
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger border-0 p-1" title="Salir"><i class="bi bi-box-arrow-right"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
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
</body>
</html>