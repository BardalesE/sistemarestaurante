@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column gap-3 mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
            <div>
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-bar-chart-fill me-2 text-danger"></i>Reportes Gerenciales
                </h4>
                <p class="text-muted mb-0 small">Análisis detallado de rendimiento</p>
            </div>
        </div>

        {{-- Date filter --}}
        <form action="{{ route('reports.index') }}" method="GET">
            <div class="d-flex align-items-end gap-2 bg-white p-3 rounded-3 shadow-sm border flex-wrap">
                <div>
                    <label class="small text-muted fw-bold d-block mb-1">Desde</label>
                    <input type="date" name="start_date"
                           class="form-control form-control-sm"
                           value="{{ $startDate }}">
                </div>
                <div>
                    <label class="small text-muted fw-bold d-block mb-1">Hasta</label>
                    <input type="date" name="end_date"
                           class="form-control form-control-sm"
                           value="{{ $endDate }}">
                </div>
                <button class="btn btn-danger btn-sm fw-bold px-4">
                    <i class="bi bi-search me-1"></i>Analizar
                </button>
            </div>
        </form>
    </div>

    {{-- ── CHARTS ───────────────────────────── --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-pie-chart-fill me-2 text-danger"></i>Ingresos por Categoría
                    </h6>
                </div>
                <div class="card-body" style="min-height:260px;">
                    <canvas id="categoryChart" style="max-height:280px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-person-badge me-2 text-success"></i>Ranking de Ventas por Personal
                    </h6>
                </div>
                <div class="card-body" style="min-height:260px;">
                    <canvas id="waiterChart" style="max-height:280px;"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- ── PRODUCT RANKINGS ─────────────────── --}}
    <div class="row g-4">

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-trophy-fill me-2"></i>Top 5: Platos Estrella
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center">Vendidos</th>
                                    <th class="text-end pe-4">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $prod)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $prod->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success rounded-pill">{{ $prod->qty }}</span>
                                        </td>
                                        <td class="text-end pe-4 text-success fw-bold">
                                            {{ $currency }}{{ number_format($prod->revenue, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Sin datos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>Menos Vendidos
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-end pe-4">Cant. Vendida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($worstProducts as $prod)
                                    <tr>
                                        <td class="ps-4 text-secondary">{{ $prod->name }}</td>
                                        <td class="text-end pe-4 fw-bold">{{ $prod->qty }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">Sin datos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var ctxCat = document.getElementById('categoryChart');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: @json($catLabels),
                datasets: [{
                    data: @json($catValues),
                    backgroundColor: [
                        '#c0392b','#198754','#ffc107','#0d6efd','#6f42c1','#0dcaf0','#fd7e14'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    }

    var ctxWait = document.getElementById('waiterChart');
    if (ctxWait) {
        new Chart(ctxWait, {
            type: 'bar',
            data: {
                labels: @json($waiterLabels),
                datasets: [{
                    label: 'Ventas Totales ({{ $currency }})',
                    data: @json($waiterValues),
                    backgroundColor: 'rgba(25,135,84,.75)',
                    borderColor: 'rgba(25,135,84,1)',
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
    }
})();
</script>
@endsection
