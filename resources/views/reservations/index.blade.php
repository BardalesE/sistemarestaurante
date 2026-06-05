@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-calendar-check-fill me-2 text-danger"></i>Reservas
            </h4>
            <p class="text-muted mb-0 small">Agenda y control de visitas futuras</p>
        </div>
        <button class="btn btn-danger fw-bold shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createReservationModal">
            <i class="bi bi-plus-lg me-1"></i> Nueva Reserva
        </button>
    </div>

    {{-- ── CARDS GRID ───────────────────────── --}}
    <div class="row g-3">
        @forelse($reservations as $res)
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 {{ $res->status == 'cancelled' ? 'opacity-60' : '' }}">
                    <div class="card-body">

                        {{-- Status badge --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="fw-bold text-danger mb-0 flex-grow-1 pe-2 text-truncate">
                                {{ $res->client_name }}
                            </h6>
                            <span class="badge flex-shrink-0 {{ $res->status == 'confirmed' ? 'bg-success' : ($res->status == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ strtoupper($res->status == 'pending' ? 'Pendiente' : ($res->status == 'confirmed' ? 'Confirmada' : 'Cancelada')) }}
                            </span>
                        </div>

                        {{-- Phone --}}
                        @if($res->phone)
                            <div class="text-muted small mb-3">
                                <i class="bi bi-telephone me-1"></i> {{ $res->phone }}
                            </div>
                        @endif

                        {{-- Info chips --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-2 text-center">
                                    <div class="text-muted" style="font-size:.65rem;font-weight:700;letter-spacing:.5px;">FECHA</div>
                                    <div class="fw-bold" style="font-size:.95rem;">
                                        {{ $res->reservation_time->format('d/m') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-2 text-center">
                                    <div class="text-muted" style="font-size:.65rem;font-weight:700;letter-spacing:.5px;">HORA</div>
                                    <div class="fw-bold text-danger" style="font-size:.95rem;">
                                        {{ $res->reservation_time->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-2 text-center">
                                    <div class="text-muted" style="font-size:.65rem;font-weight:700;letter-spacing:.5px;">PERSONAS</div>
                                    <div class="fw-bold" style="font-size:.95rem;">{{ $res->people }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-2 text-center">
                                    <div class="text-muted" style="font-size:.65rem;font-weight:700;letter-spacing:.5px;">MESA</div>
                                    <div class="fw-bold text-danger" style="font-size:.9rem;">
                                        {{ $res->table->name ?? 'Por asignar' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Note --}}
                        @if($res->note)
                            <div class="alert alert-info py-2 px-3 mb-3 small">
                                <i class="bi bi-sticky me-1"></i> {{ $res->note }}
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="d-flex gap-2 mt-auto">
                            @if($res->status == 'pending')
                                <form action="{{ route('reservations.status', $res->id) }}"
                                      method="POST"
                                      class="flex-fill">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button class="btn btn-sm btn-success w-100 fw-bold">
                                        <i class="bi bi-check-lg me-1"></i> Confirmar
                                    </button>
                                </form>
                                <form action="{{ route('reservations.status', $res->id) }}"
                                      method="POST"
                                      class="flex-fill">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button class="btn btn-sm btn-outline-danger w-100 fw-bold">
                                        <i class="bi bi-x-lg me-1"></i> Cancelar
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('reservations.destroy', $res->id) }}"
                                      method="POST"
                                      class="w-100"
                                      onsubmit="return confirm('¿Borrar historial de esta reserva?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-muted w-100">
                                        <i class="bi bi-trash me-1"></i> Eliminar Historial
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-25"></i>
                <p class="mb-0">No hay reservas próximas.</p>
                <button class="btn btn-danger mt-3 fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#createReservationModal">
                    <i class="bi bi-plus-lg me-1"></i> Crear primera reserva
                </button>
            </div>
        @endforelse
    </div>

</div>

{{-- ── CREATE RESERVATION MODAL ────────────── --}}
<div class="modal fade" id="createReservationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('reservations.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Nueva Reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold">Nombre Cliente</label>
                        <input type="text" name="client_name" class="form-control"
                               required placeholder="Ej: Familia Gómez">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="Opcional">
                    </div>
                    <div class="col-12 col-sm-8">
                        <label class="form-label fw-bold">Fecha y Hora</label>
                        <input type="datetime-local" name="reservation_time"
                               class="form-control" required>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label fw-bold">Personas</label>
                        <input type="number" name="people" class="form-control"
                               value="2" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Mesa (Opcional)</label>
                        <select name="table_id" class="form-select">
                            <option value="">-- Asignar al llegar --</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}">
                                    {{ $table->name }}
                                    (Zona: {{ $table->area->name ?? 'Gral' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Notas / Pedidos Especiales</label>
                        <textarea name="note" class="form-control" rows="2"
                                  placeholder="Ej: Necesitan silla de bebé"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Agendar</button>
            </div>
        </form>
    </div>
</div>
@endsection
