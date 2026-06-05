@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── PAGE HEADER ─────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Panel de Control</h4>
            <p class="text-muted mb-0 small">
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
            </p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-danger fw-bold shadow-sm">
            <i class="bi bi-cart-plus me-2"></i> Ir al POS
        </a>
    </div>

    {{-- ── STAT CARDS ──────────────────────── --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:52px;height:52px;background:rgba(192,57,43,.1);">
                            <i class="bi bi-graph-up fs-3 text-danger"></i>
                        </div>
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size:.7rem;letter-spacing:.5px;">
                                Venta de Hoy
                            </p>
                            <h3 class="fw-bold text-danger mb-0" style="font-size:1.6rem;">
                                {{ $currency }}{{ number_format($totalSalesToday, 2) }}
                            </h3>
                            <small class="text-muted">{{ $ordersCountToday }} órdenes cerradas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:52px;height:52px;background:rgba(25,135,84,.1);">
                            <i class="bi bi-shop fs-3 text-success"></i>
                        </div>
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size:.7rem;letter-spacing:.5px;">
                                Mesas Atendiendo
                            </p>
                            <h3 class="fw-bold text-success mb-0" style="font-size:1.6rem;">
                                {{ \App\Models\Order::where('status', 'pending')->count() }}
                            </h3>
                            <small class="text-muted">Clientes activos ahora</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:52px;height:52px;background:rgba(220,53,69,.1);">
                            <i class="bi bi-exclamation-triangle fs-3 text-danger"></i>
                        </div>
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size:.7rem;letter-spacing:.5px;">
                                Stock Crítico
                            </p>
                            <h3 class="fw-bold text-danger mb-0" style="font-size:1.6rem;">
                                {{ $lowStockProducts->count() }}
                            </h3>
                            <small class="text-muted">Productos por agotar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── CHART + TOP PRODUCTS ─────────────── --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-bar-chart-line me-2 text-danger"></i>
                        Evolución de Ventas — Últimos 7 Días
                    </h6>
                </div>
                <div class="card-body px-3 pb-3" style="min-height:220px;">
                    <canvas id="salesChart" style="max-height:260px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0">🏆 Platos Más Vendidos</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topProducts as $product)
                            <li class="list-group-item d-flex align-items-center py-3 border-0 border-bottom">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="rounded me-3 border flex-shrink-0"
                                         width="40" height="40"
                                         style="object-fit:cover;">
                                @else
                                    <div class="bg-light rounded me-3 border d-flex align-items-center justify-content-center text-muted flex-shrink-0"
                                         style="width:40px;height:40px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate" style="font-size:.88rem;">
                                        {{ $product->name }}
                                    </div>
                                    <small class="text-muted">{{ $product->total_qty }} vendidos</small>
                                </div>
                                <span class="badge bg-danger rounded-pill ms-2 flex-shrink-0">
                                    #{{ $loop->iteration }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-5">
                                <i class="bi bi-trophy fs-2 d-block mb-2 opacity-25"></i>
                                Sin datos suficientes
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>

    {{-- ── LOW STOCK TABLE ─────────────────── --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-3 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-box-seam me-2"></i> Reponer Inventario Urgente
                    </h6>
                </div>

                {{-- Desktop table --}}
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-end pe-4">Reponer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $prod)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $prod->name }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $prod->category->name }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger fs-6">{{ $prod->stock }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('products.adjust', $prod->id) }}"
                                                  method="POST"
                                                  class="d-flex justify-content-end gap-2">
                                                @csrf
                                                <input type="hidden" name="type" value="add">
                                                <input type="number" name="quantity"
                                                       class="form-control form-control-sm"
                                                       style="width:75px;"
                                                       placeholder="Cant." required min="1">
                                                <button class="btn btn-sm btn-outline-success fw-bold">
                                                    <i class="bi bi-plus-lg"></i> Reponer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                            ¡Todo en orden! No hay productos con bajo stock.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile cards --}}
                <div class="d-md-none p-3">
                    @forelse($lowStockProducts as $prod)
                        <div class="card border mb-2 shadow-none">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $prod->name }}</div>
                                        <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                                            {{ $prod->category->name }}
                                        </span>
                                    </div>
                                    <span class="badge bg-danger fs-6">{{ $prod->stock }}</span>
                                </div>
                                <form action="{{ route('products.adjust', $prod->id) }}"
                                      method="POST"
                                      class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="type" value="add">
                                    <input type="number" name="quantity"
                                           class="form-control form-control-sm flex-grow-1"
                                           placeholder="Cantidad" required min="1">
                                    <button class="btn btn-sm btn-success fw-bold px-3">
                                        <i class="bi bi-plus-lg"></i> Reponer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                            ¡Todo en orden!
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    {{-- ── QUICK ACTIONS ────────────────────── --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background:linear-gradient(135deg,#c0392b,#e74c3c);">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <div>
                            <h5 class="fw-bold text-white mb-1">
                                <i class="bi bi-lightning-charge-fill me-2"></i> Acciones Rápidas
                            </h5>
                            <p class="mb-0 text-white opacity-75 small">
                                Atajos para la gestión diaria del restaurante.
                            </p>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button class="btn btn-light fw-bold text-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#createProductModal">
                                <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
                            </button>
                            <a href="{{ route('users.index') }}"
                               class="btn btn-outline-light fw-bold">
                                <i class="bi bi-people me-1"></i> Registrar Personal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var ctx = document.getElementById('salesChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Ventas Totales ({{ $currency }})',
                data: @json($chartValues),
                backgroundColor: 'rgba(192, 57, 43, 0.75)',
                borderColor: 'rgba(192, 57, 43, 1)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
})();
</script>

@includeIf('products.create_modal')
@endsection
