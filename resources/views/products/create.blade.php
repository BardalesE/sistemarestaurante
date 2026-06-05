@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-6">

            {{-- ── HEADER ──────────────────────── --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('products.index') }}"
                   class="btn btn-light border shadow-sm flex-shrink-0">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Nuevo Producto</h4>
                    <p class="text-muted mb-0 small">Registra un nuevo ítem en el menú</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-sm-8">
                                <label class="form-label fw-bold">
                                    Nombre del Producto <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="Ej: Lomo Saltado" required
                                       value="{{ old('name') }}">
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-bold">
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                                {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold">
                                    Precio de Venta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" step="0.01" name="price"
                                           class="form-control"
                                           placeholder="0.00" required
                                           value="{{ old('price') }}">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold">Stock Inicial</label>
                                <input type="number" name="stock"
                                       class="form-control"
                                       value="{{ old('stock', 0) }}" min="0">
                                <small class="text-muted">
                                    Se registrará en el Kardex.
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Imagen (Opcional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-danger fw-bold px-4 shadow-sm">
                                <i class="bi bi-save me-2"></i>Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
