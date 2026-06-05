@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7 col-xl-6">

            {{-- ── HEADER ──────────────────────────── --}}
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2 mb-4">
                <div>
                    <a href="{{ route('pos.order', $order->table_id) }}"
                       class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-1">
                        <i class="bi bi-arrow-left"></i> Volver a la Orden
                    </a>
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="bi bi-scissors me-2 text-danger"></i>Dividir Cuenta
                    </h4>
                    <p class="text-muted mb-0 small">Selecciona los ítems a cobrar por separado</p>
                </div>
                <div class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 align-self-start align-self-sm-center">
                    <i class="bi bi-table me-1"></i>
                    {{ $order->table->name ?? 'Mesa' }} — Orden #{{ $order->id }}
                </div>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">

                {{-- Card header --}}
                <div class="card-header bg-danger text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-list-check me-2"></i>Ítems de la Orden
                        </h6>
                        <div class="form-check form-check-inline mb-0">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="checkAll"
                                   onclick="toggleAll(this)"
                                   style="width:20px;height:20px;cursor:pointer;">
                            <label class="form-check-label small fw-bold" for="checkAll">Seleccionar todo</label>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pos.split', $order->id) }}" method="POST">
                    @csrf

                    {{-- ── ITEM LIST ─────────────────────── --}}
                    <div class="card-body p-0">
                        @foreach($order->details as $detail)
                            <label class="item-row d-flex align-items-center gap-3 px-3 py-3 border-bottom w-100"
                                   for="item-{{ $detail->id }}"
                                   style="cursor:pointer;transition:background .15s;">

                                <input type="checkbox"
                                       name="selected_items[]"
                                       value="{{ $detail->id }}"
                                       id="item-{{ $detail->id }}"
                                       class="form-check-input item-check flex-shrink-0"
                                       data-price="{{ $detail->price * $detail->quantity }}"
                                       onchange="calculateSplitTotal()"
                                       style="width:22px;height:22px;cursor:pointer;">

                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark" style="font-size:.92rem;">
                                        {{ $detail->product->name }}
                                    </div>
                                    @if($detail->note)
                                        <div class="small text-muted fst-italic text-truncate">
                                            <i class="bi bi-sticky me-1"></i>{{ $detail->note }}
                                        </div>
                                    @endif
                                </div>

                                <div class="text-end flex-shrink-0">
                                    <div class="small text-muted">x{{ $detail->quantity }}</div>
                                    <div class="fw-bold text-danger">
                                        {{ number_format($detail->price * $detail->quantity, 2) }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- ── FOOTER ──────────────────────── --}}
                    <div class="card-footer bg-light p-3 p-md-4">

                        {{-- Total display --}}
                        <div class="d-flex justify-content-between align-items-center bg-white rounded-3 border p-3 mb-3">
                            <div class="text-muted small fw-bold text-uppercase">
                                <i class="bi bi-calculator me-1"></i>Total a Cobrar
                            </div>
                            <div class="fw-bold text-danger" id="splitTotalDisplay"
                                 style="font-size:1.7rem;line-height:1;">
                                0.00
                            </div>
                        </div>

                        {{-- Payment method --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Método de Pago</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check"
                                       name="payment_method" id="splitCash"
                                       value="cash" checked>
                                <label class="btn btn-outline-success fw-bold py-3" for="splitCash">
                                    <i class="bi bi-cash me-1"></i>Efectivo
                                </label>

                                <input type="radio" class="btn-check"
                                       name="payment_method" id="splitCard"
                                       value="card">
                                <label class="btn btn-outline-primary fw-bold py-3" for="splitCard">
                                    <i class="bi bi-credit-card me-1"></i>Tarjeta
                                </label>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="btn btn-danger btn-lg w-100 fw-bold shadow-sm"
                                id="btnSplit"
                                disabled
                                style="height:54px;font-size:1.05rem;">
                            <i class="bi bi-check-circle-fill me-2"></i>Cobrar Selección
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    .item-row:hover { background-color: rgba(192,57,43,.04); }
    .item-row:has(.item-check:checked) { background-color: rgba(192,57,43,.07); }
</style>
@endpush

<script>
    function toggleAll(source) {
        document.querySelectorAll('.item-check').forEach(function(cb) {
            cb.checked = source.checked;
        });
        calculateSplitTotal();
    }

    function calculateSplitTotal() {
        var total  = 0;
        var checks = document.querySelectorAll('.item-check:checked');
        var btn    = document.getElementById('btnSplit');

        checks.forEach(function(cb) {
            total += parseFloat(cb.getAttribute('data-price'));
        });

        document.getElementById('splitTotalDisplay').innerText = total.toFixed(2);

        if (total > 0) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Cobrar ' + total.toFixed(2);
        } else {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Cobrar Selección';
        }
    }
</script>
@endsection
