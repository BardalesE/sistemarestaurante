@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-start gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-box-seam-fill me-2 text-danger"></i>Inventario de Productos
            </h4>
            <p class="text-muted mb-0 small">Gestión de carta y existencias</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('inventory.logs') }}" class="btn btn-dark fw-bold shadow-sm">
                <i class="bi bi-clock-history me-1"></i>
                <span class="d-none d-sm-inline">Ver </span>Kardex
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-danger fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nuevo
                <span class="d-none d-sm-inline">Producto</span>
            </a>
        </div>
    </div>

    {{-- ── DESKTOP TABLE ────────────────────── --}}
    <div class="card border-0 shadow-sm d-none d-lg-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}"
                                                 class="rounded border flex-shrink-0"
                                                 width="44" height="44"
                                                 style="object-fit:cover;">
                                        @else
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted flex-shrink-0"
                                                 style="width:44px;height:44px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            @if(!$product->is_saleable)
                                                <span class="badge bg-secondary" style="font-size:.62rem;">
                                                    <i class="bi bi-eye-slash-fill me-1"></i>SOLO INSUMO
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="fw-bold text-danger">S/ {{ number_format($product->price, 2) }}</td>
                                <td class="text-center">
                                    @if($product->stock <= 5)
                                        <span class="badge bg-danger">Crítico: {{ $product->stock }}</span>
                                    @elseif($product->stock <= 15)
                                        <span class="badge bg-warning text-dark">Bajo: {{ $product->stock }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm rounded-pill px-3 fw-bold {{ $product->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                                                style="font-size:.72rem;">
                                            {{ $product->is_active ? 'ACTIVO' : 'INACTIVO' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-dark me-1"
                                            onclick="adjustStock({{ $product }})"
                                            title="Ajustar Stock">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar producto?')">
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
        <div class="row g-3">
            @foreach($products as $product)
                <div class="col-12 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex gap-3 mb-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         class="rounded border flex-shrink-0"
                                         width="56" height="56"
                                         style="object-fit:cover;">
                                @else
                                    <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted flex-shrink-0"
                                         style="width:56px;height:56px;">
                                        <i class="bi bi-image fs-4"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate">{{ $product->name }}</div>
                                    <span class="badge bg-light text-dark border" style="font-size:.68rem;">
                                        {{ $product->category->name }}
                                    </span>
                                    @if(!$product->is_saleable)
                                        <span class="badge bg-secondary d-block mt-1" style="font-size:.62rem;">
                                            SOLO INSUMO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="text-muted" style="font-size:.7rem;">PRECIO</div>
                                    <div class="fw-bold text-danger fs-6">S/ {{ number_format($product->price, 2) }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-muted" style="font-size:.7rem;">STOCK</div>
                                    @if($product->stock <= 5)
                                        <span class="badge bg-danger">{{ $product->stock }}</span>
                                    @elseif($product->stock <= 15)
                                        <span class="badge bg-warning text-dark">{{ $product->stock }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                    @endif
                                </div>
                                <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm rounded-pill px-3 fw-bold {{ $product->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                                            style="font-size:.7rem;">
                                        {{ $product->is_active ? 'ACTIVO' : 'INACTIVO' }}
                                    </button>
                                </form>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-dark flex-fill"
                                        onclick="adjustStock({{ $product }})">
                                    <i class="bi bi-arrow-left-right me-1"></i> Stock
                                </button>
                                <a href="{{ route('products.edit', $product->id) }}"
                                   class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar producto?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── ADJUST STOCK MODAL ───────────────────── --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form id="adjustStockForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold">Ajuste de Inventario</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3 small text-muted">
                    Producto: <strong id="adjust_name" class="text-dark"></strong>
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Tipo de Movimiento</label>
                    <select name="type" class="form-select">
                        <option value="add">📥 Entrada (Compra / Reposición)</option>
                        <option value="subtract">🗑️ Salida (Merma / Pérdida / Uso)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Cantidad</label>
                    <input type="number" name="quantity" class="form-control"
                           min="1" required placeholder="Ej: 10">
                </div>
                <div class="mb-1">
                    <label class="form-label fw-bold small">Motivo (Opcional)</label>
                    <input type="text" name="note" class="form-control form-control-sm"
                           placeholder="Ej: Compra semanal">
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="submit" class="btn btn-danger w-100 fw-bold">
                    Guardar Movimiento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function adjustStock(product) {
    document.getElementById('adjust_name').innerText = product.name;
    document.getElementById('adjustStockForm').action =
        "{{ url('/products') }}/" + product.id + "/adjust";
    new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
}
</script>
@endsection
