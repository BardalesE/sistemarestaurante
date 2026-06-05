@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            {{-- ── HEADER ──────────────────────── --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('products.index') }}"
                   class="btn btn-light border shadow-sm flex-shrink-0">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold text-dark mb-0">Editar Producto</h4>
                    <p class="text-muted mb-0 small">Gestiona detalles y receta</p>
                </div>
            </div>

            <form action="{{ route('products.update', $product->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row g-4">

                    {{-- ── INFO GENERAL ─────────────── --}}
                    <div class="col-12 col-md-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white fw-bold py-3 border-bottom">
                                <i class="bi bi-info-circle me-2 text-danger"></i>Información General
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre del Producto</label>
                                    <input type="text" name="name"
                                           class="form-control"
                                           value="{{ $product->name }}"
                                           required>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-bold">Categoría</label>
                                        <select name="category_id" class="form-select" required>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}"
                                                        {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-bold">Precio (S/)</label>
                                        <input type="number" step="0.01" name="price"
                                               class="form-control"
                                               value="{{ $product->price }}"
                                               required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Stock Actual (Lectura)</label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           value="{{ $product->stock }}"
                                           readonly>
                                    <small class="text-muted">
                                        Si tiene receta, el stock dependerá de los insumos.
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch p-3 border rounded bg-light">
                                        <input type="hidden" name="is_saleable" value="0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="is_saleable"
                                               value="1"
                                               id="saleableCheck"
                                               {{ $product->is_saleable ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-2" for="saleableCheck">
                                            Disponible para Venta en POS
                                        </label>
                                        <div class="small text-muted ms-2 mt-1">
                                            Si desmarcas esto, el producto servirá como
                                            <strong>Insumo</strong> pero no aparecerá en el menú.
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold">Imagen</label>
                                    @if($product->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/'.$product->image) }}"
                                                 class="rounded border"
                                                 width="80" height="80"
                                                 style="object-fit:cover;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── RECETA / INSUMOS ─────────── --}}
                    <div class="col-12 col-md-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-danger text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-basket me-2"></i>Receta / Insumos</span>
                                <button type="button"
                                        class="btn btn-sm btn-light text-danger fw-bold"
                                        onclick="addIngredient()">
                                    <i class="bi bi-plus-lg"></i> Agregar
                                </button>
                            </div>
                            <div class="card-body p-3" style="max-height:380px;overflow-y:auto;">
                                <small class="d-block text-muted mb-3">
                                    Al vender, se descontarán los insumos automáticamente.
                                </small>

                                <div id="ingredients-container">
                                    @foreach($product->ingredients as $index => $ing)
                                        <div class="card mb-2 border-0 shadow-sm ingredient-row">
                                            <div class="card-body p-2">
                                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                                    <select name="ingredients[{{ $index }}][id]"
                                                            class="form-select form-select-sm flex-grow-1"
                                                            required>
                                                        <option value="">-- Insumo --</option>
                                                        @foreach($allProducts as $p)
                                                            <option value="{{ $p->id }}"
                                                                    {{ $ing->id == $p->id ? 'selected' : '' }}>
                                                                {{ $p->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number"
                                                           step="0.01"
                                                           name="ingredients[{{ $index }}][quantity]"
                                                           class="form-control form-control-sm flex-shrink-0"
                                                           style="width:80px;"
                                                           placeholder="Cant."
                                                           value="{{ $ing->pivot->quantity }}"
                                                           required>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger border-0"
                                                            onclick="this.closest('.ingredient-row').remove()">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div id="no-ingredients-msg"
                                     class="text-center text-muted py-4 {{ $product->ingredients->count() > 0 ? 'd-none' : '' }}">
                                    <i class="bi bi-box-seam fs-1 opacity-25 d-block mb-2"></i>
                                    <p class="small mb-0">Sin receta configurada.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
var ingIndex = {{ $product->ingredients->count() }};

function addIngredient() {
    document.getElementById('no-ingredients-msg').classList.add('d-none');
    var container = document.getElementById('ingredients-container');
    var row = document.createElement('div');
    row.className = 'card mb-2 border-0 shadow-sm ingredient-row';
    row.innerHTML = `
        <div class="card-body p-2">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <select name="ingredients[${ingIndex}][id]" class="form-select form-select-sm flex-grow-1" required>
                    <option value="">-- Seleccionar Insumo --</option>
                    @foreach($allProducts as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" name="ingredients[${ingIndex}][quantity]"
                       class="form-control form-control-sm flex-shrink-0"
                       style="width:80px;" placeholder="Cant." required>
                <button type="button" class="btn btn-sm btn-outline-danger border-0"
                        onclick="this.closest('.ingredient-row').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    container.appendChild(row);
    ingIndex++;
}
</script>
@endsection
