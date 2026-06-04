@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam-fill me-2"></i>Inventario de Productos</h2>
            <p class="text-muted mb-0">Gestión de carta y existencias</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.logs') }}" class="btn btn-dark fw-bold shadow-sm">
                <i class="bi bi-clock-history me-2"></i> Ver Kardex (Movimientos)
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
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
                                    <div class="d-flex align-items-center">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}" class="rounded me-3 border" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-3 border d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;"><i class="bi bi-image"></i></div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $product->name }}</div>
                                            @if(!$product->is_saleable)
                                                <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                                    <i class="bi bi-eye-slash-fill me-1"></i> SOLO INSUMO
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $product->category->name }}</span></td>
                                <td class="fw-bold text-primary">S/ {{ number_format($product->price, 2) }}</td>
                                <td class="text-center">
                                    @if($product->stock <= 5)
                                        <span class="badge bg-danger">Critico: {{ $product->stock }}</span>
                                    @elseif($product->stock <= 15)
                                        <span class="badge bg-warning text-dark">Bajo: {{ $product->stock }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm {{ $product->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">
                                            {{ $product->is_active ? 'ACTIVO' : 'INACTIVO' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-dark me-1" onclick="adjustStock({{ $product }})" title="Ajustar Stock">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form id="adjustStockForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold">Ajuste de Inventario</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 small text-muted">Producto: <strong id="adjust_name" class="text-dark"></strong></p>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipo de Movimiento</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="add">📥 Entrada (Compra/Reposición)</option>
                        <option value="subtract">🗑️ Salida (Merma/Pérdida/Uso)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Cantidad</label>
                    <input type="number" name="quantity" class="form-control" min="1" required placeholder="Ej: 10">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Motivo (Opcional)</label>
                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Ej: Compra semanal">
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Guardar Movimiento</button>
            </div>
        </form>
    </div>
</div>

<script>
    function adjustStock(product) {
        document.getElementById('adjust_name').innerText = product.name;
        document.getElementById('adjustStockForm').action = "{{ url('/products') }}/" + product.id + "/adjust";
        new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
    }
</script>
@endsection