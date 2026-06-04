@extends('layouts.app')

@section('content')
<div class="container-fluid p-0 d-flex flex-column" style="height: calc(100vh - 20px); overflow: hidden;">
    
    <div class="d-flex justify-content-between align-items-center bg-white border-bottom px-4 py-2 mb-3 shadow-sm flex-shrink-0">
        <div class="d-flex align-items-center">
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <div>
                <h5 class="fw-bold mb-0 text-primary">Mesa: {{ $table->name }}</h5>
                <small class="text-muted">Zona: {{ $table->area->name }}</small>
            </div>
        </div>
        <div>
            @if($order)
                <button type="button" class="btn btn-outline-primary border me-2" data-bs-toggle="modal" data-bs-target="#moveTableModal">
                    <i class="bi bi-arrow-left-right"></i> Mover Mesa
                </button>
            @endif

            <span class="badge bg-light text-dark border me-2">
                <i class="bi bi-person"></i> {{ auth()->user()->name }}
            </span>
            <span class="badge bg-success">
                <i class="bi bi-clock"></i> {{ now()->format('H:i') }}
            </span>
        </div>
    </div>

    <div class="row g-0 flex-grow-1 overflow-hidden">
        <div class="col-md-2 bg-light border-end overflow-auto h-100 pb-5">
            <div class="list-group list-group-flush">
                <a href="javascript:void(0)" onclick="filterProducts('all')" class="list-group-item list-group-item-action active text-center py-3 category-btn" id="cat-btn-all">
                    <i class="bi bi-grid-fill d-block fs-4 mb-1"></i> Todo
                </a>
                @foreach($categories as $category)
                    <a href="javascript:void(0)" onclick="filterProducts('cat-{{ $category->id }}')" class="list-group-item list-group-item-action text-center py-3 category-btn" id="cat-btn-{{ $category->id }}">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}" class="rounded mb-1" width="40" height="40" style="object-fit: cover;">
                        @else
                            <i class="bi bi-tag d-block fs-4 mb-1"></i>
                        @endif
                        <span class="d-block small fw-bold lh-sm">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="col-md-7 bg-white overflow-auto h-100 px-3 pb-5" id="products-container">
            <div class="row row-cols-2 row-cols-lg-3 row-cols-xl-4 g-3 py-3">
                @foreach($categories as $category)
                    @foreach($category->products as $product)
                        <div class="col product-item cat-{{ $category->id }}">
                            <div class="card h-100 border-0 shadow-sm product-card" onclick="addToOrder({{ $product->id }})" style="cursor: pointer; transition: transform 0.1s;">
                                <div class="position-relative">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex justify-content-center align-items-center" style="height: 120px;">
                                            <i class="bi bi-cup-straw fs-1 text-muted opacity-25"></i>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-dark opacity-75">
                                            {{ $currency ?? '$' }}{{ number_format($product->price, 0) }}
                                        </span>
                                    </div>
                                    @if(!is_null($product->stock) && $product->stock <= 5)
                                        <div class="position-absolute bottom-0 start-0 m-2">
                                            <span class="badge bg-danger border border-white">Quedan: {{ $product->stock }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body p-2 text-center">
                                    <h6 class="card-title fs-6 mb-0 text-truncate">{{ $product->name }}</h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="col-md-3 bg-white border-start h-100 d-flex flex-column">
            <div class="p-3 bg-light border-bottom flex-shrink-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart"></i> Cuenta Actual</h6>
            </div>
            <div id="cart-container" class="flex-grow-1 d-flex flex-column overflow-hidden">
                @include('pos.partials.cart', ['order' => $order])
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-warning">
                <h6 class="modal-title fw-bold text-dark">Nota Cocina</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="noteDetailId">
                <textarea id="noteText" class="form-control" rows="3" placeholder="Ej: Sin sal"></textarea>
            </div>
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-warning w-100 btn-sm text-dark fw-bold" onclick="saveNote()">Guardar Nota</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title small">Ajustes de Cuenta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="discountUrl">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-danger">Descuento (S/)</label>
                    <input type="number" step="any" id="inputDiscount" class="form-control" placeholder="0.00">
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-success">Propina (S/)</label>
                    <input type="number" step="any" id="inputTip" class="form-control" placeholder="0.00">
                </div>
            </div>
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-primary w-100 btn-sm" onclick="saveDiscount()">Aplicar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="splitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-scissors me-2"></i> Dividir Cuenta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            @if($order)
                <form action="{{ route('pos.split', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body" id="splitModalBody">
                        <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="submit" class="btn btn-dark w-100 fw-bold">
                            <i class="bi bi-wallet2 me-2"></i> Cobrar Selección
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="moveTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-info text-white">
                <h6 class="modal-title fw-bold">Mover a otra mesa</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            @if($order)
                <form action="{{ route('pos.move', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label small text-muted">Selecciona destino:</label>
                        <select name="target_table_id" class="form-select" required>
                            <option value="" selected disabled>-- Elegir Mesa --</option>
                            @foreach($freeTables as $ft)
                                <option value="{{ $ft->id }}">{{ $ft->name }} ({{ $ft->area->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="submit" class="btn btn-info w-100 btn-sm text-white fw-bold">Confirmar</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    const tableId = {{ $table->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- CONFIGURACIÓN MODALES ---
    var noteModalEl = document.getElementById('noteModal');
    if(noteModalEl){
        noteModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('noteDetailId').value = button.getAttribute('data-detail-id');
            document.getElementById('noteText').value = button.getAttribute('data-note-content') || '';
            setTimeout(() => document.getElementById('noteText').focus(), 500);
        });
    }

    var discountModalEl = document.getElementById('discountModal');
    if(discountModalEl){
        discountModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('discountUrl').value = button.getAttribute('data-url');
            document.getElementById('inputDiscount').value = button.getAttribute('data-discount');
            document.getElementById('inputTip').value = button.getAttribute('data-tip');
            setTimeout(() => document.getElementById('inputDiscount').focus(), 500);
        });
    }

    // --- CONFIGURACIÓN DIVIDIR CUENTA ---
    var splitModalEl = document.getElementById('splitModal');
    if(splitModalEl){
        splitModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var url = button.getAttribute('data-url');
            
            // Cargar contenido via AJAX
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('splitModalBody').innerHTML = html;
                });
        });
    }

    function forceCloseModal(modalId) {
        var el = document.getElementById(modalId);
        if (typeof bootstrap !== 'undefined') {
            var instance = bootstrap.Modal.getInstance(el);
            if (instance) instance.hide();
        }
        el.classList.remove('show');
        el.style.display = 'none';
        document.body.classList.remove('modal-open');
        document.body.style = '';
        var backdrops = document.getElementsByClassName('modal-backdrop');
        if(backdrops[0]) backdrops[0].remove();
    }

    // --- FUNCIONES AJAX DE GUARDADO ---
    window.saveNote = function() {
        var detailId = document.getElementById('noteDetailId').value;
        var note = document.getElementById('noteText').value;
        forceCloseModal('noteModal'); 
        fetch(`{{ url('/pos/detail') }}/${detailId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note: note })
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    window.saveDiscount = function() {
        var url = document.getElementById('discountUrl').value;
        var discount = document.getElementById('inputDiscount').value;
        var tip = document.getElementById('inputTip').value;
        forceCloseModal('discountModal'); 
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ discount: discount, tip: tip })
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    // --- POS: PRODUCTOS Y CANTIDADES ---
    window.addToOrder = function(productId) {
        fetch(`{{ url('/pos/order') }}/${tableId}/add`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ product_id: productId })
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    window.updateQuantity = function(detailId, newQty) {
        fetch(`{{ url('/pos/detail') }}/${detailId}/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ quantity: newQty })
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    window.removeItem = function(detailId) {
        if(!confirm('¿Eliminar producto?')) return;
        fetch(`{{ url('/pos/detail') }}/${detailId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    // --- LOGICA DE COBRO Y FILTROS ---
    window.filterProducts = function(cat) {
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(cat === 'all' ? 'cat-btn-all' : 'cat-btn-' + cat.replace('cat-', '')).classList.add('active');
        document.querySelectorAll('.product-item').forEach(item => {
            item.style.display = (cat === 'all' || item.classList.contains(cat)) ? 'block' : 'none';
        });
    };

    window.togglePaymentInputs = function() {
        var isCash = document.getElementById('pay_cash').checked;
        document.getElementById('cash-inputs').style.display = isCash ? 'block' : 'none';
        document.getElementById('received_amount').required = isCash;
        if(!isCash) document.getElementById('received_amount').value = document.getElementById('hiddenTotal').value;
    };

    window.calculateChange = function() {
        var total = parseFloat(document.getElementById('hiddenTotal').value) || 0;
        var received = parseFloat(document.getElementById('received_amount').value) || 0;
        document.getElementById('change_display').innerText = '{{ $currency ?? "$" }}' + (received - total).toFixed(2);
    };

    document.addEventListener('shown.bs.modal', function (event) {
        if (event.target.id === 'checkoutModal') document.getElementById('received_amount').focus();
    });

    // --- 6. LÓGICA DE DIVIDIR CUENTA (GLOBAL) ---
    // Estas funciones ahora viven aquí para que el navegador siempre las encuentre
    
    window.calcSplitTotal = function() {
        let total = 0;
        // Buscamos los checkbox marcados
        document.querySelectorAll('.split-item-check:checked').forEach(chk => {
            // Convertimos el precio crudo a flotante para sumar
            total += parseFloat(chk.getAttribute('data-price'));
        });
        // Mostramos el total con 2 decimales
        document.getElementById('splitTotalDisplay').innerText = total.toFixed(2);
    };

    window.toggleAllSplit = function(source) {
        document.querySelectorAll('.split-item-check').forEach(chk => {
            chk.checked = source.checked;
        });
        calcSplitTotal();
    };

</script>

<style>
    .product-card:active { transform: scale(0.95); background-color: #f8f9fa; }
</style>
@endsection