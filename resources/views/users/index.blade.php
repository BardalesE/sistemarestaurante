@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-people-fill me-2 text-danger"></i>Personal
            </h4>
            <p class="text-muted mb-0 small">Gestiona los accesos y roles de tu equipo</p>
        </div>
        <button class="btn btn-danger fw-bold shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </button>
    </div>

    {{-- ── DESKTOP TABLE ────────────────────── --}}
    <div class="card border-0 shadow-sm d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Usuario</th>
                            <th>Rol / Cargo</th>
                            <th>Email de Acceso</th>
                            <th class="d-none d-lg-table-cell">Fecha Registro</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'cashier' ? 'bg-primary' : 'bg-success') }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            @if($user->id === Auth::id())
                                                <span class="badge bg-light text-dark border" style="font-size:.6rem;">TÚ</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">Administrador</span>
                                    @elseif($user->role == 'cashier')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2">Cajero</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">Mozo / Staff</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $user->email }}</td>
                                <td class="text-muted small d-none d-lg-table-cell">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="editUser({{ $user }})"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar a {{ $user->name }}?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── MOBILE CARDS ─────────────────────── --}}
    <div class="d-md-none">
        <div class="row g-3">
            @foreach($users as $user)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar-circle flex-shrink-0 {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'cashier' ? 'bg-primary' : 'bg-success') }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate">{{ $user->name }}</div>
                                    <div class="text-muted small text-truncate">{{ $user->email }}</div>
                                </div>
                                @if($user->id === Auth::id())
                                    <span class="badge bg-light text-dark border" style="font-size:.6rem;">TÚ</span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">Administrador</span>
                                    @elseif($user->role == 'cashier')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">Cajero</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Mozo / Staff</span>
                                    @endif
                                    <div class="text-muted mt-1" style="font-size:.7rem;">
                                        Desde {{ $user->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            onclick="editUser({{ $user }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar a {{ $user->name }}?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── CREATE USER MODAL ────────────────────── --}}
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Registrar Personal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre Completo</label>
                    <input type="text" name="name" class="form-control" required
                           placeholder="Ej: Juan Pérez">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico (Login)</label>
                    <input type="email" name="email" class="form-control" required
                           placeholder="juan@restaurante.com">
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold">Rol / Permisos</label>
                        <select name="role" class="form-select">
                            <option value="waiter">Mozo (Solo Pedidos)</option>
                            <option value="cashier">Cajero (Cobros y Gastos)</option>
                            <option value="admin">Administrador (Total)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT USER MODAL ──────────────────────── --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editUserForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Dejar vacío para no cambiar">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold">Rol</label>
                        <select name="role" id="edit_role" class="form-select">
                            <option value="waiter">Mozo</option>
                            <option value="cashier">Cajero</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning fw-bold">Actualizar Datos</button>
            </div>
        </form>
    </div>
</div>

<style>
.avatar-circle {
    width: 42px; height: 42px;
    border-radius: 50%;
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 17px;
    text-transform: uppercase;
}
</style>

<script>
function editUser(user) {
    document.getElementById('edit_name').value  = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value  = user.role;
    document.getElementById('editUserForm').action = "{{ url('/users') }}/" + user.id;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
</script>
@endsection
