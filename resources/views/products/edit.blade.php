@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('products.index') }}" class="btn btn-light border shadow-sm me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h2 class="fw-bold text-dark mb-0">Editar Producto</h2>
                    <p class="text-muted mb-0">Gestiona detalles y receta</p>
                </div>
            </div>

            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white fw-bold py-3">Información General</div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre del Producto</label>
                                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Categoría</label>
                                        <select name="category_id" class="form-select" required>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Precio (S/)</label>
                                        <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Stock Actual (Lectura)</label>
                                    <input type="text" class="form-control bg-light" value="{{ $product->stock }}" readonly>
                                    <small class="text-muted">Si tiene receta, el stock dependerá de los insumos.</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch p-3 border rounded bg-light">
                                        <input type="hidden" name="is_saleable" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_saleable" value="1" id="saleableCheck" {{ $product->is_saleable ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold ms-2" for="saleableCheck">
                                            Disponible para Venta en POS
                                        </label>
                                        <div class="small text-muted ms-2 mt-1">
                                            Si desmarcas esto, el producto servirá como <strong>Insumo</strong> (para recetas) pero no aparecerá en el menú de ventas.
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold">Imagen</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm h-100 bg-light">
                            <div class="card-header bg-primary text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-basket me-2"></i>Receta / Insumos</span>
                                <button type="button" class="btn btn-sm btn-light text-primary fw-bold" onclick="addIngredient()">
                                    <i class="bi bi-plus-lg"></i> Agregar
                                </button>
                            </div>
                            <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                                <small class="d-block text-muted mb-3">Selecciona los insumos que componen este plato. Al venderlo, se descontarán automáticamente.</small>
                                
                                <div id="ingredients-container">
                                    @foreach($product->ingredients as $index => $ing)
                                        <div class="card mb-2 border-0 shadow-sm ingredient-row">
                                            <div class="card-body p-2 d-flex gap-2 align-items-center">
                                                <select name="ingredients[{{ $index }}][id]" class="form-select form-select-sm" required>
                                                    <option value="">-- Insumo --</option>
                                                    @foreach($allProducts as $p)
                                                        <option value="{{ $p->id }}" {{ $ing->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" step="0.01" name="ingredients[{{ $index }}][quantity]" class="form-control form-control-sm" style="width: 80px;" placeholder="Cant." value="{{ $ing->pivot->quantity }}" required>
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('.ingredient-row').remove()"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div id="no-ingredients-msg" class="text-center text-muted py-4 {{ $product->ingredients->count() > 0 ? 'd-none' : '' }}">
                                    <i class="bi bi-box-seam fs-1 opacity-25"></i>
                                    <p class="small mb-0">Sin receta configurada.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-5 fw-bold shadow-lg">
                        <i class="bi bi-check-lg me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    let ingIndex = {{ $product->ingredients->count() }};

    function addIngredient() {
        document.getElementById('no-ingredients-msg').classList.add('d-none');
        
        const container = document.getElementById('ingredients-container');
        const row = document.createElement('div');
        row.className = 'card mb-2 border-0 shadow-sm ingredient-row';
        row.innerHTML = `
            <div class="card-body p-2 d-flex gap-2 align-items-center">
                <select name="ingredients[${ingIndex}][id]" class="form-select form-select-sm" required>
                    <option value="">-- Seleccionar Insumo --</option>
                    @foreach($allProducts as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" name="ingredients[${ingIndex}][quantity]" class="form-control form-control-sm" style="width: 80px;" placeholder="Cant." required>
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('.ingredient-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        ingIndex++;
    }
</script>
@endsection