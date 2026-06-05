@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('clients.index') }}"
           class="btn btn-light border shadow-sm flex-shrink-0">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-sm-inline ms-1">Volver</span>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Perfil de Cliente</h4>
            <p class="text-muted mb-0 small">Historial y preferencias</p>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── CLIENT CARD ──────────────────── --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-4">
                    <div class="mb-3 position-relative d-inline-block">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold border mx-auto"
                             style="width:90px;height:90px;font-size:2.5rem;background:#fadbd8;color:#c0392b;">
                            {{ strtoupper(substr($client->name, 0, 1)) }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-{{ $badgeColor }} border border-white shadow-sm"
                              style="font-size:.85rem;">
                            {{ $rank }}
                        </span>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $client->name }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                        {{ $client->address ?? 'Sin dirección' }}
                    </p>

                    <div class="bg-light rounded-3 p-3 text-start">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <small class="text-muted fw-bold">DOCUMENTO</small>
                            <small class="fw-bold">{{ $client->document_number ?? '-' }}</small>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <small class="text-muted fw-bold">TELÉFONO</small>
                            <small class="fw-bold">{{ $client->phone ?? '-' }}</small>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <small class="text-muted fw-bold">EMAIL</small>
                            <small class="fw-bold text-truncate ms-2" style="max-width:160px;">
                                {{ $client->email ?? '-' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── STATS + ORDERS ───────────────── --}}
        <div class="col-12 col-md-8">

            {{-- Stat cards --}}
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="card border-0 shadow-sm bg-danger text-white h-100">
                        <div class="card-body py-3 px-3">
                            <small class="opacity-75 fw-bold text-uppercase" style="font-size:.65rem;">
                                Total Gastado
                            </small>
                            <h5 class="fw-bold mb-0 mt-1">S/{{ number_format($totalSpent, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm bg-success text-white h-100">
                        <div class="card-body py-3 px-3">
                            <small class="opacity-75 fw-bold text-uppercase" style="font-size:.65rem;">
                                Visitas
                            </small>
                            <h5 class="fw-bold mb-0 mt-1">{{ $visitCount }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                        <div class="card-body py-3 px-3">
                            <small class="fw-bold text-uppercase" style="font-size:.65rem;">
                                Plato Fav.
                            </small>
                            <div class="fw-bold mt-1 text-truncate small"
                                 title="{{ $favoriteProduct }}">
                                {{ $favoriteProduct }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-clock-history me-2 text-danger"></i>Historial de Pedidos
                    </h6>
                </div>

                {{-- Desktop table --}}
                <div class="d-none d-sm-block">
                    <div class="table-responsive" style="max-height:380px;overflow-y:auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">Fecha</th>
                                    <th>Folio</th>
                                    <th>Mesa</th>
                                    <th class="text-end pe-4">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="ps-4">
                                            {{ $order->created_at->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->table->name ?? 'Barra' }}</td>
                                        <td class="text-end pe-4 fw-bold text-danger">
                                            S/ {{ number_format($order->total, 2) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('sales.ticket', $order->id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-link text-dark">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                                            Aún no tiene pedidos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile cards --}}
                <div class="d-sm-none p-3">
                    @forelse($orders as $order)
                        <div class="card border mb-2 shadow-none">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small">
                                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                            · {{ $order->table->name ?? 'Barra' }}
                                        </div>
                                        <div class="text-muted" style="font-size:.72rem;">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-danger">
                                            S/{{ number_format($order->total, 2) }}
                                        </span>
                                        <a href="{{ route('sales.ticket', $order->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-dark">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                            Sin pedidos registrados.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
