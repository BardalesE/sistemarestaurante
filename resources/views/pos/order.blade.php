@extends('layouts.app')

@section('content')
{{-- Wrapper: full-height on desktop, natural scroll on mobile --}}
<div id="posWrapper" class="pos-wrapper">

    {{-- ── TOP BAR ──────────────────────────── --}}
    <div class="pos-topbar d-flex justify-content-between align-items-center bg-white border-bottom px-3 px-md-4 py-2 mb-0 shadow-sm flex-shrink-0">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
                <span class="d-none d-sm-inline ms-1">Volver</span>
            </a>
            <div>
                <h6 class="fw-bold mb-0 text-danger">Mesa: {{ $table->name }}</h6>
                <small class="text-muted">{{ $table->area->name }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($order)
                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#moveTableModal">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="d-none d-sm-inline ms-1">Mover</span>
                </button>
            @endif
            <span class="badge bg-light text-dark border d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-person"></i> {{ auth()->user()->name }}
            </span>
            <span class="badge bg-success">
                <i class="bi bi-clock me-1"></i>{{ now()->format('H:i') }}
            </span>
        </div>
    </div>

    {{-- ── MAIN 3-PANEL LAYOUT ──────────────── --}}
    <div id="posMainRow" class="pos-main-row">

        {{-- ── CATEGORIES ───────────────────── --}}
        <div id="catSidebar" class="pos-cat-sidebar bg-light border-end overflow-auto">
            {{-- Desktop: vertical list --}}
            <div class="desktop-cats">
                <a href="javascript:void(0)"
                   onclick="filterProducts('all')"
                   class="list-group-item list-group-item-action active text-center py-3 category-btn"
                   id="cat-btn-all">
                    <i class="bi bi-grid-fill d-block fs-4 mb-1"></i>
                    <span class="small fw-bold">Todo</span>
                </a>
                @foreach($categories as $category)
                    <a href="javascript:void(0)"
                       onclick="filterProducts('cat-{{ $category->id }}')"
                       class="list-group-item list-group-item-action text-center py-3 category-btn"
                       id="cat-btn-{{ $category->id }}">
                        @if($category->image)
                            <img src="{{ asset('storage/'.$category->image) }}"
                                 class="rounded mb-1"
                                 width="40" height="40"
                                 style="object-fit:cover;">
                        @else
                            <i class="bi bi-tag d-block fs-4 mb-1"></i>
                        @endif
                        <span class="d-block small fw-bold lh-sm">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Mobile: horizontal pills --}}
            <div class="mobile-cats d-flex align-items-center gap-2 px-3 py-2">
                <button onclick="filterProducts('all')"
                        class="btn btn-sm btn-danger fw-bold flex-shrink-0 category-btn"
                        id="mob-btn-all">
                    <i class="bi bi-grid-fill me-1"></i>Todo
                </button>
                @foreach($categories as $category)
                    <button onclick="filterProducts('cat-{{ $category->id }}')"
                            class="btn btn-sm btn-outline-secondary fw-bold flex-shrink-0 category-btn"
                            id="mob-btn-{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── PRODUCTS ──────────────────────── --}}
        <div class="pos-products bg-white overflow-auto" id="products-container">
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2 g-md-3 p-2 p-md-3">
                @foreach($categories as $category)
                    @foreach($category->products as $product)
                        <div class="col product-item cat-{{ $category->id }}">
                            <div class="card h-100 border-0 shadow-sm product-card"
                                 onclick="addToOrder({{ $product->id }})"
                                 style="cursor:pointer;transition:transform .1s;">
                                <div class="position-relative">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}"
                                             class="card-img-top"
                                             style="height:100px;object-fit:cover;">
                                    @else
                                        <div class="bg-light d-flex justify-content-center align-items-center"
                                             style="height:100px;">
                                            <i class="bi bi-cup-straw fs-1 text-muted opacity-25"></i>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 end-0 m-1">
                                        <span class="badge bg-dark opacity-80" style="font-size:.7rem;">
                                            {{ $currency ?? '$' }}{{ number_format($product->price, 0) }}
                                        </span>
                                    </div>
                                    @if(!is_null($product->stock) && $product->stock <= 5)
                                        <div class="position-absolute bottom-0 start-0 m-1">
                                            <span class="badge bg-danger border border-white"
                                                  style="font-size:.6rem;">
                                                Quedan: {{ $product->stock }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body p-2 text-center">
                                    <h6 class="card-title mb-0 text-truncate" style="font-size:.82rem;">
                                        {{ $product->name }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- ── CART ──────────────────────────── --}}
        <div class="pos-cart bg-white border-start d-flex flex-column">
            <div class="p-3 bg-light border-bottom flex-shrink-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-cart me-2"></i>Cuenta Actual
                </h6>
            </div>
            <div id="cart-container" class="flex-grow-1 d-flex flex-column overflow-hidden">
                @include('pos.partials.cart', ['order' => $order])
            </div>
        </div>

    </div>{{-- end pos-main-row --}}
</div>{{-- end posWrapper --}}

{{-- ── MOBILE CART TOGGLE ───────────────────── --}}
<div class="d-lg-none pos-cart-toggle-bar" id="cartToggleBar">
    <button class="btn btn-danger fw-bold w-100 py-3 rounded-0"
            onclick="toggleMobileCart()"
            id="cartToggleBtn">
        <i class="bi bi-cart-fill me-2"></i>
        <span id="cartToggleLabel">Ver Cuenta</span>
    </button>
</div>

{{-- ── NOTE MODAL ───────────────────────────── --}}
<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-warning">
                <h6 class="modal-title fw-bold text-dark">Nota Cocina</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="noteDetailId">
                <textarea id="noteText" class="form-control" rows="3"
                          placeholder="Ej: Sin sal"></textarea>
            </div>
            <div class="modal-footer p-1">
                <button type="button"
                        class="btn btn-warning w-100 btn-sm text-dark fw-bold"
                        onclick="saveNote()">
                    Guardar Nota
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── DISCOUNT MODAL ───────────────────────── --}}
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
                    <input type="number" step="any" id="inputDiscount" class="form-control"
                           placeholder="0.00">
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-success">Propina (S/)</label>
                    <input type="number" step="any" id="inputTip" class="form-control"
                           placeholder="0.00">
                </div>
            </div>
            <div class="modal-footer p-1">
                <button type="button"
                        class="btn btn-primary w-100 btn-sm"
                        onclick="saveDiscount()">
                    Aplicar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── SPLIT MODAL ──────────────────────────── --}}
<div class="modal fade" id="splitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-scissors me-2"></i>Dividir Cuenta
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            @if($order)
                <form action="{{ route('pos.split', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body" id="splitModalBody">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="submit" class="btn btn-dark w-100 fw-bold">
                            <i class="bi bi-wallet2 me-2"></i>Cobrar Selección
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- ── MOVE TABLE MODAL ─────────────────────── --}}
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
                                <option value="{{ $ft->id }}">
                                    {{ $ft->name }} ({{ $ft->area->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="submit"
                                class="btn btn-info w-100 btn-sm text-white fw-bold">
                            Confirmar
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
/* ── POS LAYOUT ─────────────────────────────── */
.pos-wrapper {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 58px);    /* 28px top padding + 30px gap */
    overflow: hidden;
    margin: -28px -28px 0;        /* bleed to main-content edges */
}

.pos-topbar { flex-shrink: 0; }

.pos-main-row {
    display: flex;
    flex-grow: 1;
    overflow: hidden;
}

/* Categories sidebar */
.pos-cat-sidebar { width: 100px; flex-shrink: 0; }
.desktop-cats    { display: block; }
.mobile-cats     { display: none; }

/* Products area */
.pos-products { flex: 1; min-width: 0; }

/* Cart panel */
.pos-cart { width: 280px; flex-shrink: 0; }

/* Cart toggle bar (hidden on desktop) */
.pos-cart-toggle-bar { display: none; }

/* ── PRODUCT CARD ───────────────────────────── */
.product-card:active { transform: scale(0.94); background: #f8f9fa; }

/* ── CATEGORY BTN (desktop vertical) ────────── */
.list-group-item.active {
    background-color: #c0392b;
    border-color: #c0392b;
    color: #fff;
}

/* ── MOBILE & TABLET (< lg) ─────────────────── */
@media (max-width: 991.98px) {
    .pos-wrapper {
        height: auto;
        overflow: visible;
        margin: -16px -14px 0;
    }

    .pos-main-row {
        flex-direction: column;
        overflow: visible;
        height: auto;
    }

    /* Categories → horizontal scroll bar */
    .pos-cat-sidebar {
        width: 100%;
        border-bottom: 1px solid #dee2e6 !important;
        border-right: none !important;
        background: #fff !important;
    }
    .desktop-cats { display: none; }
    .mobile-cats  { display: flex; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .mobile-cats::-webkit-scrollbar { height: 0; }

    /* Products: full width, auto height */
    .pos-products {
        height: auto;
        overflow: visible;
        padding-bottom: 140px; /* space for cart toggle + bottom nav */
    }

    /* Cart: hidden by default on mobile, shown as overlay */
    .pos-cart {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 75vh;
        z-index: 1035;
        border-top: 3px solid #c0392b !important;
        border-left: none !important;
        transform: translateY(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        border-radius: 18px 18px 0 0;
        box-shadow: 0 -8px 30px rgba(0,0,0,.2);
    }
    .pos-cart.cart-open { transform: translateY(0); }

    /* Toggle bar */
    .pos-cart-toggle-bar {
        display: block;
        position: fixed;
        bottom: 65px; /* above bottom nav */
        left: 0; right: 0;
        z-index: 1034;
    }

    /* Product cards larger for touch */
    .product-card .card-img-top,
    .product-card .bg-light { height: 90px !important; }
}

@media (max-width: 575.98px) {
    .pos-wrapper { margin: -14px -12px 0; }
    .pos-cart { height: 80vh; }
}
</style>

<script>
var tableId   = {{ $table->id }};
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/* ── MODAL SETUP ─────────────────────────────── */
var noteModalEl = document.getElementById('noteModal');
if (noteModalEl) {
    noteModalEl.addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        document.getElementById('noteDetailId').value = btn.getAttribute('data-detail-id');
        document.getElementById('noteText').value     = btn.getAttribute('data-note-content') || '';
        setTimeout(() => document.getElementById('noteText').focus(), 500);
    });
}

var discountModalEl = document.getElementById('discountModal');
if (discountModalEl) {
    discountModalEl.addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        document.getElementById('discountUrl').value   = btn.getAttribute('data-url');
        document.getElementById('inputDiscount').value = btn.getAttribute('data-discount');
        document.getElementById('inputTip').value      = btn.getAttribute('data-tip');
        setTimeout(() => document.getElementById('inputDiscount').focus(), 500);
    });
}

var splitModalEl = document.getElementById('splitModal');
if (splitModalEl) {
    splitModalEl.addEventListener('show.bs.modal', function (e) {
        var url = e.relatedTarget.getAttribute('data-url');
        fetch(url)
            .then(r => r.text())
            .then(html => { document.getElementById('splitModalBody').innerHTML = html; });
    });
}

function forceCloseModal(modalId) {
    var el = document.getElementById(modalId);
    if (typeof bootstrap !== 'undefined') {
        var inst = bootstrap.Modal.getInstance(el);
        if (inst) inst.hide();
    }
    el.classList.remove('show');
    el.style.display = 'none';
    document.body.classList.remove('modal-open');
    document.body.style = '';
    var bd = document.getElementsByClassName('modal-backdrop');
    if (bd[0]) bd[0].remove();
}

/* ── AJAX ACTIONS ────────────────────────────── */
window.saveNote = function () {
    var detailId = document.getElementById('noteDetailId').value;
    var note     = document.getElementById('noteText').value;
    forceCloseModal('noteModal');
    fetch(`{{ url('/pos/detail') }}/${detailId}/note`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ note: note })
    }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
};

window.saveDiscount = function () {
    var url      = document.getElementById('discountUrl').value;
    var discount = document.getElementById('inputDiscount').value;
    var tip      = document.getElementById('inputTip').value;
    forceCloseModal('discountModal');
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ discount: discount, tip: tip })
    }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
};

window.addToOrder = function (productId) {
    fetch(`{{ url('/pos/order') }}/${tableId}/add`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ product_id: productId })
    }).then(r => r.text()).then(function (html) {
        document.getElementById('cart-container').innerHTML = html;
        // On mobile, auto-open cart when item added
        if (window.innerWidth < 992) openMobileCart();
    });
};

window.updateQuantity = function (detailId, newQty) {
    fetch(`{{ url('/pos/detail') }}/${detailId}/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ quantity: newQty })
    }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
};

window.removeItem = function (detailId) {
    if (!confirm('¿Eliminar producto?')) return;
    fetch(`{{ url('/pos/detail') }}/${detailId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
};

/* ── PRODUCT FILTER ──────────────────────────── */
window.filterProducts = function (cat) {
    document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
    // Activate all matching buttons (desktop + mobile)
    if (cat === 'all') {
        document.querySelectorAll('[id="cat-btn-all"], [id="mob-btn-all"]').forEach(b => b.classList.add('active'));
    } else {
        var id = cat.replace('cat-', '');
        var d  = document.getElementById('cat-btn-' + id);
        var m  = document.getElementById('mob-btn-' + id);
        if (d) d.classList.add('active');
        if (m) m.classList.add('active');
    }
    document.querySelectorAll('.product-item').forEach(item => {
        item.style.display = (cat === 'all' || item.classList.contains(cat)) ? 'block' : 'none';
    });
};

/* ── PAYMENT ─────────────────────────────────── */
window.togglePaymentInputs = function () {
    var isCash = document.getElementById('pay_cash').checked;
    document.getElementById('cash-inputs').style.display = isCash ? 'block' : 'none';
    document.getElementById('received_amount').required  = isCash;
    if (!isCash) document.getElementById('received_amount').value = document.getElementById('hiddenTotal').value;
};

window.calculateChange = function () {
    var total    = parseFloat(document.getElementById('hiddenTotal').value) || 0;
    var received = parseFloat(document.getElementById('received_amount').value) || 0;
    document.getElementById('change_display').innerText = '{{ $currency ?? "$" }}' + (received - total).toFixed(2);
};

document.addEventListener('shown.bs.modal', function (e) {
    if (e.target.id === 'checkoutModal') document.getElementById('received_amount').focus();
});

/* ── SPLIT BILL ──────────────────────────────── */
window.calcSplitTotal = function () {
    var total = 0;
    document.querySelectorAll('.split-item-check:checked').forEach(chk => {
        total += parseFloat(chk.getAttribute('data-price'));
    });
    document.getElementById('splitTotalDisplay').innerText = total.toFixed(2);
};

window.toggleAllSplit = function (source) {
    document.querySelectorAll('.split-item-check').forEach(chk => { chk.checked = source.checked; });
    calcSplitTotal();
};

/* ── MOBILE CART TOGGLE ──────────────────────── */
function openMobileCart() {
    document.querySelector('.pos-cart').classList.add('cart-open');
    document.getElementById('cartToggleLabel').textContent = 'Cerrar Cuenta';
}

window.toggleMobileCart = function () {
    var cart = document.querySelector('.pos-cart');
    var isOpen = cart.classList.toggle('cart-open');
    document.getElementById('cartToggleLabel').textContent = isOpen ? 'Cerrar Cuenta' : 'Ver Cuenta';
};
</script>
@endsection
