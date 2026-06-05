@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-fire me-2 text-danger"></i>Monitor de Cocina (KDS)
            </h4>
            <p class="text-muted mb-0 small">Pedidos pendientes de preparación</p>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark border">
                    <i class="bi bi-circle-fill text-danger me-1"></i>Pendiente
                </span>
                <span class="badge bg-white text-dark border">
                    <i class="bi bi-circle-fill text-warning me-1"></i>Preparando
                </span>
            </div>
            <div id="reloj" class="fw-bold fs-5 font-monospace bg-dark text-success px-3 py-1 rounded-3">
                00:00:00
            </div>
        </div>
    </div>

    {{-- ── ORDERS GRID ──────────────────────── --}}
    <div class="row g-3">
        @forelse($orders as $order)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">

                    {{-- Card header --}}
                    <div class="card-header text-white d-flex justify-content-between align-items-center py-3
                                {{ $order->details->contains('status', 'cooking') ? 'bg-warning text-dark' : 'bg-danger' }}">
                        <div>
                            <h6 class="fw-bold mb-0">Mesa: {{ $order->table->name }}</h6>
                            <small class="{{ $order->details->contains('status', 'cooking') ? 'text-dark opacity-75' : 'opacity-75' }}">
                                Folio #{{ $order->id }}
                            </small>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-clock-history"></i>
                            <span class="d-block fw-bold">{{ $order->created_at->format('H:i') }}</span>
                        </div>
                    </div>

                    {{-- Items list --}}
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($order->details as $detail)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 gap-2">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary rounded-pill fw-bold flex-shrink-0"
                                                  style="font-size:.85rem;min-width:28px;">
                                                {{ $detail->quantity }}
                                            </span>
                                            <span class="fw-bold {{ $detail->status == 'served' ? 'text-decoration-line-through text-muted' : '' }} text-truncate">
                                                {{ $detail->product->name }}
                                            </span>
                                        </div>
                                        @if($detail->note)
                                            <div class="ms-4 mt-1">
                                                <span class="badge bg-warning text-dark border border-dark"
                                                      style="font-size:.7rem;white-space:normal;">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $detail->note }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <form action="{{ route('kitchen.update', $detail) }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        @if($detail->status == 'pending')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger fw-bold px-3">
                                                <i class="bi bi-play-fill me-1"></i>Empezar
                                            </button>
                                        @elseif($detail->status == 'cooking')
                                            <button type="submit"
                                                    class="btn btn-sm btn-warning fw-bold px-3">
                                                <i class="bi bi-check-lg me-1"></i>Listo
                                            </button>
                                        @else
                                            <span class="badge bg-success py-2 px-3">
                                                <i class="bi bi-check2-all"></i>
                                            </span>
                                        @endif
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="opacity-60">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
                    <h3 class="mt-3 text-muted">Todo en orden, Chef.</h3>
                    <p class="text-muted">No hay pedidos pendientes en este momento.</p>
                    <div class="badge bg-light text-muted border mt-2" style="font-size:.8rem;">
                        <i class="bi bi-arrow-clockwise me-1"></i> Auto-recarga cada 15 seg.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
setInterval(function () {
    var now = new Date();
    document.getElementById('reloj').innerText = now.toLocaleTimeString();
}, 1000);

setTimeout(function () {
    window.location.reload();
}, 15000);
</script>
@endsection
