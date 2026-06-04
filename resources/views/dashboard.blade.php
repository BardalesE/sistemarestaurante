@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Panel de Control</h2>
            <p class="text-muted mb-0">Resumen de operaciones: {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary btn-lg shadow-sm fw-bold">
            <i class="bi bi-cart-plus me-2"></i> Ir al POS
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <h6 class="text-uppercase text-muted fw-bold small">Venta de Hoy</h6>
                    <h2 class="fw-bold text-primary mb-0">{{ $currency }}{{ number_format($totalSalesToday, 2) }}</h2>
                    <small class="text-muted">{{ $ordersCountToday }} órdenes cerradas</small>
                    <i class="bi bi-graph-up position-absolute top-0 end-0 m-3 text-primary opacity-25 fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <h6 class="text-uppercase text-muted fw-bold small">Mesas Atendiendo</h6>
                    <h2 class="fw-bold text-success mb-0">{{ \App\Models\Order::where('status', 'pending')->count() }}</h2>
                    <small class="text-muted">Clientes activos ahora</small>
                    <i class="bi bi-shop position-absolute top-0 end-0 m-3 text-success opacity-25 fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <h6 class="text-uppercase text-muted fw-bold small">Stock Crítico</h6>
                    <h2 class="fw-bold text-danger mb-0">{{ $lowStockProducts->count() }}</h2>
                    <small class="text-muted">Productos por agotar</small>
                    <i class="bi bi-exclamation-triangle position-absolute top-0 end-0 m-3 text-danger opacity-25 fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Evolución de Ventas (7 Días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">🏆 Platos Más Vendidos</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topProducts as $product)
                            <li class="list-group-item d-flex align-items-center py-3 border-0 border-bottom">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" class="rounded me-3" width="40" height="40" style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;"><i class="bi bi-image"></i></div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold">{{ $product->name }}</h6>
                                    <small class="text-muted">Vendidos: {{ $product->total_qty }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">#{{ $loop->iteration }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">Sin datos suficientes</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i> Reponer Inventario Urgente</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Stock Actual</th>
                                    <th class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $prod)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $prod->name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $prod->category->name }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-danger fs-6">{{ $prod->stock }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('products.adjust', $prod->id) }}" method="POST" class="d-flex justify-content-end gap-1">
                                                @csrf
                                                <input type="hidden" name="type" value="add">
                                                <input type="number" name="quantity" class="form-control form-control-sm" style="width: 70px;" placeholder="Cant." required>
                                                <button class="btn btn-sm btn-outline-success"><i class="bi bi-plus-lg"></i> Reponer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle fs-1 text-success"></i><br>
                                            ¡Todo en orden! No hay productos con bajo stock.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h4 class="fw-bold"><i class="bi bi-lightning-charge-fill me-2"></i> Acciones Rápidas</h4>
                        <p class="mb-0 opacity-75">Atajos para la gestión diaria del restaurante.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#createProductModal"><i class="bi bi-plus-circle me-1"></i> Nuevo Producto</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-light fw-bold"><i class="bi bi-people me-1"></i> Registrar Personal</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels), // Días de la semana
                datasets: [{
                    label: 'Ventas Totales ({{ $currency }})',
                    data: @json($chartValues),
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false } // Ocultar leyenda redundante
                }
            }
        });
    }
</script>

@includeIf('products.create_modal')

@endsection