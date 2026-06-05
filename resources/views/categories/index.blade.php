@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-tags-fill me-2 text-danger"></i>Gestión de Categorías
            </h4>
            <p class="text-muted mb-0 small">Organiza los productos por tipo</p>
        </div>
        <button type="button"
                class="btn btn-danger fw-bold shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createCategoryModal">
            <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
        </button>
    </div>

    {{-- ── DESKTOP TABLE ────────────────────── --}}
    <div class="card border-0 shadow-sm d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="80">Imagen</th>
                            <th>Nombre</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="ps-4">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             class="rounded border"
                                             width="50" height="50"
                                             style="object-fit:cover;">
                                    @else
                                        <div class="bg-light text-muted d-flex justify-content-center align-items-center rounded border"
                                             style="width:50px;height:50px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td class="text-center">
                                    @if($category->is_active)
                                        <span class="badge bg-success px-3 py-2">Activo</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('categories.destroy', $category) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="if(confirm('¿Estás seguro de eliminar esta categoría?')) this.closest('form').submit();">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-tags fs-1 d-block mb-2 opacity-25"></i>
                                    No hay categorías registradas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── MOBILE CARDS ─────────────────────── --}}
    <div class="d-md-none">
        @forelse($categories as $category)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 class="rounded border flex-shrink-0"
                                 width="56" height="56"
                                 style="object-fit:cover;">
                        @else
                            <div class="bg-light text-muted d-flex justify-content-center align-items-center rounded border flex-shrink-0"
                                 style="width:56px;height:56px;">
                                <i class="bi bi-image fs-4"></i>
                            </div>
                        @endif

                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-bold fs-6 text-truncate">{{ $category->name }}</div>
                            @if($category->is_active)
                                <span class="badge bg-success mt-1">Activo</span>
                            @else
                                <span class="badge bg-secondary mt-1">Inactivo</span>
                            @endif
                        </div>

                        <form action="{{ route('categories.destroy', $category) }}"
                              method="POST"
                              class="flex-shrink-0">
                            @csrf @method('DELETE')
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="if(confirm('¿Eliminar esta categoría?')) this.closest('form').submit();">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-tags fs-1 d-block mb-2 opacity-25"></i>
                No hay categorías registradas aún.
            </div>
        @endforelse
    </div>

</div>

{{-- ── CREATE CATEGORY MODAL ───────────────── --}}
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Nueva Categoría</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="name" name="name"
                               required placeholder="Ej: Bebidas Calientes">
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Imagen (Opcional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
