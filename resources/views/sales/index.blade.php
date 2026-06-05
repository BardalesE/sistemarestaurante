@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column gap-3 mb-4">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
            <div>
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-cash-coin me-2 text-danger"></i>Caja y Movimientos
                </h4>
                <p class="text-muted mb-0 small">Control de Ingresos y Egresos</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sales.daily.report', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   target="_blank"
                   class="btn btn-dark fw-bold">
                    <i class="bi bi-printer me-1"></i>
                    <span class="d-none d-sm-inline">Imprimir </span>Corte Z
                </a>
                <button class="btn btn-danger fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#expenseModal">
                    <i class="bi bi-dash-circle me-1"></i>
                    <span class="d-none d-sm-inline">Registrar </span>Salida
                </button>
            </div>
        </div>

        {{-- Date filter --}}
        <form action="{{ route('sales.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2 bg-white p-3 rounded-3 shadow-sm border flex-wrap">
                <i class="bi bi-calendar-range text-muted"></i>
                <input type="date" name="start_date"
                       class="form-control form-control-sm"
                       style="max-width:150px;"
                       value="{{ $startDate }}">
                <span class="text-muted fw-bold">–</span>
                <input type="date" name="end_date"
                       class="form-control form-control-sm"
                       style="max-width:150px;"
                       value="{{ $endDate }}">
                <button type="submit" class="btn btn-danger btn-sm fw-bold px-4">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- ── STAT CARDS ──────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body py-3">
                    <small class="opacity-75 text-uppercase fw-bold" style="font-size:.68rem;">Venta Total</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ number_format($totalSales, 2) }}</h4>
                    <small class="opacity-75">{{ $orders->count() }} operaciones</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body py-3">
                    <small class="opacity-75 text-uppercase fw-bold" style="font-size:.68rem;">Entrada Efectivo</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ number_format($totalCash, 2) }}</h4>
                    <small class="opacity-75">Dinero Físico</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-danger text-white h-100">
                <div class="card-body py-3">
                    <small class="opacity-75 text-uppercase fw-bold" style="font-size:.68rem;">Gastos / Salidas</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ number_format($totalExpenses, 2) }}</h4>
                    <small class="opacity-75">{{ $expenses->count() }} movimientos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 {{ $balance >= 0 ? 'bg-dark text-white' : 'bg-warning text-dark' }}">
                <div class="card-body py-3">
                    <small class="{{ $balance >= 0 ? 'opacity-75' : '' }} text-uppercase fw-bold" style="font-size:.68rem;">Balance Caja</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ number_format($balance, 2) }}</h4>
                    <small class="{{ $balance >= 0 ? 'opacity-75' : '' }}">Efectivo - Gastos</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABS ─────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 p-0">
            <ul class="nav nav-tabs ps-3 pt-3 gap-1" id="salesTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold"
                            data-bs-toggle="tab"
                            data-bs-target="#sales"
                            type="button">
                        <i class="bi bi-receipt me-1"></i>
                        <span class="d-none d-sm-inline">Historial de </span>Ventas
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold text-danger"
                            data-bs-toggle="tab"
                            data-bs-target="#expenses"
                            type="button">
                        <i class="bi bi-journal-minus me-1"></i>
                        <span class="d-none d-sm-inline">Historial de </span>Gastos
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="salesTabsContent">

                {{-- VENTAS --}}
                <div class="tab-pane fade show active" id="sales" role="tabpanel">

                    {{-- Desktop table --}}
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Hora</th>
                                    <th>Folio</th>
                                    <th>Cliente</th>
                                    <th>Mesa</th>
                                    <th>Método</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $order->created_at->format('H:i') }}</td>
                                        <td class="fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            {{ $order->client_name }}
                                            <br><small class="text-muted">{{ $order->document_type }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $order->table->name ?? 'Barra' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->payment_method == 'cash' ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-primary bg-opacity-10 text-primary border border-primary' }}">
                                                {{ $order->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($order->total, 2) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('sales.ticket', $order->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-dark">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                                            No hay ventas en este rango.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="d-md-none p-3">
                        @forelse($orders as $order)
                            <div class="card border mb-2 shadow-none">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-muted small">{{ $order->client_name }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-danger fs-6">{{ number_format($order->total, 2) }}</div>
                                            <div class="text-muted" style="font-size:.7rem;">{{ $order->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-light text-dark border">
                                                {{ $order->table->name ?? 'Barra' }}
                                            </span>
                                            <span class="badge {{ $order->payment_method == 'cash' ? 'bg-success' : 'bg-primary' }}">
                                                {{ $order->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta' }}
                                            </span>
                                        </div>
                                        <a href="{{ route('sales.ticket', $order->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-dark">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                                No hay ventas en este rango.
                            </div>
                        @endforelse
                    </div>

                </div>

                {{-- GASTOS --}}
                <div class="tab-pane fade" id="expenses" role="tabpanel">

                    {{-- Desktop table --}}
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Hora</th>
                                    <th>Descripción / Motivo</th>
                                    <th>Registrado Por</th>
                                    <th class="text-end text-danger">Monto</th>
                                    <th class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $expense->created_at->format('d/m H:i') }}</td>
                                        <td class="fw-bold">{{ $expense->description }}</td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>{{ $expense->user->name }}
                                            </small>
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            -{{ number_format($expense->amount, 2) }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('expenses.destroy', $expense->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Eliminar este gasto?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No hay gastos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="d-md-none p-3">
                        @forelse($expenses as $expense)
                            <div class="card border mb-2 shadow-none">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="fw-bold flex-grow-1 pe-2">{{ $expense->description }}</div>
                                        <div class="fw-bold text-danger flex-shrink-0">
                                            -{{ number_format($expense->amount, 2) }}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>{{ $expense->user->name }}
                                            · {{ $expense->created_at->format('d/m H:i') }}
                                        </small>
                                        <form action="{{ route('expenses.destroy', $expense->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Eliminar este gasto?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">No hay gastos registrados.</div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── EXPENSE MODAL ────────────────────────── --}}
<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('expenses.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Registrar Salida de Dinero</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    <small>Esta acción restará dinero del efectivo en caja.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción del Gasto</label>
                    <input type="text" name="description" class="form-control"
                           placeholder="Ej: Compra de hielo, Pago proveedor..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Monto a Retirar</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="amount"
                               class="form-control fs-4 fw-bold text-danger"
                               placeholder="0.00" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Registrar Salida</button>
            </div>
        </form>
    </div>
</div>
@endsection
