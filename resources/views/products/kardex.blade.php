@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-start gap-3 mb-4">
        <div>
            <a href="{{ route('products.index') }}"
               class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-1">
                <i class="bi bi-arrow-left"></i> Volver a Productos
            </a>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-clock-history me-2 text-danger"></i>Kardex de Movimientos
            </h4>
            <p class="text-muted mb-0 small">Auditoría detallada de entradas y salidas de stock</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                <i class="bi bi-arrow-down-circle me-1"></i>Entrada
            </span>
            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2">
                <i class="bi bi-arrow-up-circle me-1"></i>Salida / Venta
            </span>
        </div>
    </div>

    {{-- ── DESKTOP TABLE ────────────────────── --}}
    <div class="card border-0 shadow-sm d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha / Hora</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Motivo / Nota</th>
                            <th class="d-none d-lg-table-cell">Usuario</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-center">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 text-muted small">
                                    {{ $log->created_at->format('d/m/Y') }}<br>
                                    {{ $log->created_at->format('H:i:s') }}
                                </td>
                                <td class="fw-bold">{{ $log->product->name }}</td>
                                <td>
                                    @if($log->type == 'sale')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">VENTA POS</span>
                                    @elseif($log->type == 'entry')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">ENTRADA</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">MERMA</span>
                                    @endif
                                </td>
                                <td class="text-muted fst-italic small">{{ $log->note ?? '-' }}</td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border flex-shrink-0"
                                             style="width:26px;height:26px;font-size:.7rem;font-weight:700;">
                                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <small>{{ $log->user->name ?? 'Sistema' }}</small>
                                    </div>
                                </td>
                                <td class="text-center fw-bold {{ $log->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </td>
                                <td class="text-center fw-bold bg-light">{{ $log->new_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                                    No hay movimientos registrados aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
    </div>

    {{-- ── MOBILE CARDS ─────────────────────── --}}
    <div class="d-md-none">
        @forelse($logs as $log)
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1 overflow-hidden pe-2">
                            <div class="fw-bold text-truncate">{{ $log->product->name }}</div>
                            <div class="text-muted" style="font-size:.72rem;">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold {{ $log->quantity > 0 ? 'text-success' : 'text-danger' }} fs-6">
                                {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                            </div>
                            <div class="badge bg-light text-dark border">
                                Saldo: {{ $log->new_stock }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($log->type == 'sale')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary" style="font-size:.7rem;">VENTA POS</span>
                        @elseif($log->type == 'entry')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size:.7rem;">ENTRADA</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" style="font-size:.7rem;">MERMA</span>
                        @endif
                        @if($log->note)
                            <span class="text-muted fst-italic small">{{ $log->note }}</span>
                        @endif
                        <small class="text-muted ms-auto">{{ $log->user->name ?? 'Sistema' }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                No hay movimientos registrados.
            </div>
        @endforelse
        <div class="mt-3">{{ $logs->links() }}</div>
    </div>

</div>
@endsection
