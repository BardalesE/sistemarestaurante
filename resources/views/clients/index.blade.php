@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-people-fill me-2 text-danger"></i>Cartera de Clientes
            </h4>
            <p class="text-muted mb-0 small">Gestión de relaciones y fidelización (CRM)</p>
        </div>
        <button class="btn btn-danger fw-bold shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createClientModal">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Cliente
        </button>
    </div>

    {{-- ── DESKTOP TABLE ────────────────────── --}}
    <div class="card border-0 shadow-sm d-none d-lg-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre Cliente</th>
                            <th>Documento / RUC</th>
                            <th>Contacto</th>
                            <th class="text-center">Visitas</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    <a href="{{ route('clients.show', $client->id) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $client->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $client->document_number ?? '---' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    @if($client->phone)
                                        <div><i class="bi bi-telephone me-1"></i>{{ $client->phone }}</div>
                                    @endif
                                    @if($client->email)
                                        <div><i class="bi bi-envelope me-1"></i>{{ $client->email }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($client->orders_count > 0)
                                        <span class="badge bg-success rounded-pill">{{ $client->orders_count }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('clients.show', $client->id) }}"
                                       class="btn btn-sm btn-outline-primary me-1"
                                       title="Ver Perfil 360">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="editClient({{ $client }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('clients.destroy', $client->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar cliente?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── MOBILE / TABLET CARDS ────────────── --}}
    <div class="d-lg-none">
        @forelse($clients as $client)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1 overflow-hidden pe-2">
                            <a href="{{ route('clients.show', $client->id) }}"
                               class="fw-bold text-dark text-decoration-none fs-6 text-truncate d-block">
                                {{ $client->name }}
                            </a>
                            @if($client->document_number)
                                <span class="badge bg-light text-dark border" style="font-size:.68rem;">
                                    {{ $client->document_number }}
                                </span>
                            @endif
                        </div>
                        @if($client->orders_count > 0)
                            <div class="text-center flex-shrink-0">
                                <span class="badge bg-success rounded-pill fs-6">
                                    {{ $client->orders_count }}
                                </span>
                                <div class="text-muted" style="font-size:.6rem;">visitas</div>
                            </div>
                        @endif
                    </div>

                    @if($client->phone || $client->email)
                        <div class="text-muted small mb-3">
                            @if($client->phone)
                                <div><i class="bi bi-telephone me-1"></i>{{ $client->phone }}</div>
                            @endif
                            @if($client->email)
                                <div><i class="bi bi-envelope me-1"></i>{{ $client->email }}</div>
                            @endif
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('clients.show', $client->id) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-eye-fill me-1"></i> Ver Perfil
                        </a>
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="editClient({{ $client }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('clients.destroy', $client->id) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('¿Eliminar cliente?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                No hay clientes registrados.
            </div>
        @endforelse
    </div>

</div>

{{-- ── CREATE CLIENT MODAL ─────────────────── --}}
<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('clients.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Registrar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="fw-bold form-label">Nombre Completo *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="fw-bold form-label">DNI / RUC</label>
                        <input type="text" name="document_number" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="fw-bold form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="fw-bold form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="fw-bold form-label">Dirección</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT CLIENT MODAL ───────────────────── --}}
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editClientForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Documento</label>
                        <input type="text" name="document_number" id="edit_doc" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="address" id="edit_address" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning fw-bold">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editClient(client) {
    document.getElementById('edit_name').value    = client.name;
    document.getElementById('edit_doc').value     = client.document_number;
    document.getElementById('edit_phone').value   = client.phone;
    document.getElementById('edit_email').value   = client.email;
    document.getElementById('edit_address').value = client.address;
    document.getElementById('editClientForm').action = "{{ url('/clients') }}/" + client.id;
    new bootstrap.Modal(document.getElementById('editClientModal')).show();
}
</script>
@endsection
