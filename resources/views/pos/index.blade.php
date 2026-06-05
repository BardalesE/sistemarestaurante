@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Punto de Venta</h4>
            <p class="text-muted mb-0 small">Selecciona una mesa para comenzar</p>
        </div>
        {{-- Legend --}}
        <div class="d-flex flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="display:inline-block;width:12px;height:12px;background:#198754;"></span>
                <small class="fw-bold text-muted">Disponible</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="display:inline-block;width:12px;height:12px;background:#dc3545;"></span>
                <small class="fw-bold text-muted">Ocupada</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle" style="display:inline-block;width:12px;height:12px;background:#ffc107;"></span>
                <small class="fw-bold text-muted">Reservada</small>
            </div>
        </div>
    </div>

    {{-- ── AREA TABS ────────────────────────── --}}
    <ul class="nav nav-pills mb-4 flex-nowrap overflow-auto gap-2"
        id="posTabs" role="tablist"
        style="-webkit-overflow-scrolling:touch;padding-bottom:4px;">
        @foreach($areas as $index => $area)
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link {{ $index == 0 ? 'active' : '' }} fw-bold px-4 border"
                        id="tab-{{ $area->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#area-{{ $area->id }}"
                        type="button" role="tab">
                    {{ $area->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="posTabsContent">
        @foreach($areas as $index => $area)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                 id="area-{{ $area->id }}"
                 role="tabpanel">

                {{-- ── DESKTOP CANVAS VIEW ──────────── --}}
                <div class="pos-canvas d-none d-lg-block position-relative border rounded-3 shadow-sm"
                     style="height:650px;overflow:auto;
                            background:#f8fafc;
                            background-image:radial-gradient(#cbd5e1 1px,transparent 1px);
                            background-size:20px 20px;">

                    @foreach($area->tables as $table)
                        @php
                            $order        = $table->orders->first();
                            $reservations = $table->reservations;
                            $isBusy       = $order ? true : false;
                            $hasRes       = $reservations->count() > 0;

                            $cardClass = $isBusy
                                ? 'border-danger border-2'
                                : ($hasRes ? 'border-warning border-2' : 'border-success border-2');
                        @endphp

                        <a href="{{ route('pos.order', $table->id) }}"
                           class="text-decoration-none text-dark">
                            <div class="pos-table-card position-absolute d-flex flex-column align-items-center justify-content-between p-2 rounded-3 bg-white {{ $cardClass }}"
                                 style="width:110px;height:110px;
                                        left:{{ $table->x_pos }}px;
                                        top:{{ $table->y_pos }}px;
                                        transition:all .2s cubic-bezier(.175,.885,.32,1.275);">

                                <div class="w-100 text-center border-bottom pb-1 mb-1">
                                    <span class="fw-bold small text-uppercase" style="font-size:.73rem;">
                                        {{ $table->name }}
                                    </span>
                                </div>

                                <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative w-100">
                                    <i class="bi {{ $isBusy ? 'bi-display-fill text-danger' : 'bi-display text-secondary opacity-50' }} fs-1"></i>

                                    @if($hasRes && !$isBusy)
                                        <div class="position-absolute top-50 start-50 translate-middle badge bg-warning text-dark border border-dark shadow-sm"
                                             style="font-size:.58rem;width:100%;white-space:normal;line-height:1.1;z-index:2;max-height:60px;overflow-y:auto;">
                                            @foreach($reservations as $res)
                                                <div class="{{ !$loop->last ? 'border-bottom border-dark pb-1 mb-1' : '' }}">
                                                    <i class="bi bi-clock-fill"></i>
                                                    <strong>{{ $res->reservation_time->format('H:i') }}</strong>
                                                    <br>{{ Str::limit($res->client_name, 9) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="w-100 text-center mt-1">
                                    @if($isBusy)
                                        <div class="badge bg-danger w-100 py-1 shadow-sm">
                                            <small style="font-size:.6rem;">CONSUMO</small><br>
                                            <span class="fw-bold" style="font-size:.9rem;">
                                                {{ $currency ?? 'S/' }}{{ number_format($order->total, 2) }}
                                            </span>
                                        </div>
                                    @elseif($hasRes)
                                        <div class="badge bg-warning text-dark w-100 py-2 shadow-sm border">
                                            {{ $reservations->count() }} RESERVA(S)
                                        </div>
                                    @else
                                        <div class="badge bg-success w-100 py-2 shadow-sm">LIBRE</div>
                                    @endif
                                </div>

                            </div>
                        </a>
                    @endforeach

                </div>

                {{-- ── MOBILE / TABLET GRID VIEW ────── --}}
                <div class="d-lg-none">
                    @if($area->tables->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-grid fs-1 d-block mb-2 opacity-25"></i>
                            No hay mesas en esta zona.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($area->tables as $table)
                                @php
                                    $order        = $table->orders->first();
                                    $reservations = $table->reservations;
                                    $isBusy       = $order ? true : false;
                                    $hasRes       = $reservations->count() > 0;
                                @endphp

                                <div class="col-6 col-sm-4 col-md-3">
                                    <a href="{{ route('pos.order', $table->id) }}"
                                       class="text-decoration-none">
                                        <div class="card h-100 shadow-sm border-2 pos-mobile-card
                                                    {{ $isBusy ? 'border-danger' : ($hasRes ? 'border-warning' : 'border-success') }}">
                                            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center gap-1">

                                                <i class="bi {{ $isBusy ? 'bi-display-fill text-danger' : 'bi-display' }} fs-2
                                                           {{ !$isBusy && !$hasRes ? 'text-success' : '' }}
                                                           {{ $hasRes && !$isBusy ? 'text-warning' : '' }}"></i>

                                                <div class="fw-bold text-dark" style="font-size:.85rem;">
                                                    {{ $table->name }}
                                                </div>

                                                @if($isBusy)
                                                    <div class="badge bg-danger w-100 mt-1">
                                                        <div style="font-size:.6rem;">CONSUMO</div>
                                                        <div class="fw-bold" style="font-size:.88rem;">
                                                            {{ $currency ?? 'S/' }}{{ number_format($order->total, 2) }}
                                                        </div>
                                                    </div>
                                                @elseif($hasRes)
                                                    <div class="badge bg-warning text-dark w-100 mt-1" style="font-size:.68rem;">
                                                        <i class="bi bi-calendar-check me-1"></i>
                                                        {{ $reservations->count() }} RESERVA(S)
                                                        @foreach($reservations->take(1) as $res)
                                                            <br><strong>{{ $res->reservation_time->format('H:i') }}</strong>
                                                            · {{ Str::limit($res->client_name, 8) }}
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="badge bg-success w-100 mt-1" style="font-size:.7rem;">
                                                        LIBRE
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @endforeach
    </div>

</div>

<style>
/* Desktop canvas table hover */
.pos-table-card:hover {
    transform: scale(1.08) translateY(-4px);
    z-index: 100 !important;
    box-shadow: 0 12px 24px rgba(0,0,0,.12) !important;
    cursor: pointer;
}

/* Mobile card hover */
.pos-mobile-card {
    border-radius: 12px;
    transition: transform .15s, box-shadow .15s;
    -webkit-tap-highlight-color: transparent;
}
.pos-mobile-card:hover,
.pos-mobile-card:active {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.12) !important;
}

/* Scrollbar hide for reservation badge */
.badge::-webkit-scrollbar { width: 0; background: transparent; }

/* Pill tab active */
.nav-pills .nav-link.active {
    background-color: #c0392b;
    color: #fff;
    box-shadow: 0 4px 10px rgba(192,57,43,.3);
}
.nav-pills .nav-link {
    color: #495057;
    background-color: #fff;
}
.nav-pills .nav-link:hover {
    background-color: #fadbd8;
    color: #c0392b;
}
</style>
@endsection
