@if($order)
    {{-- ── ORDER ITEMS ──────────────────────── --}}
    <div class="flex-grow-1 overflow-auto" style="max-height:calc(100vh - 400px);">
        @foreach($order->details as $detail)
            <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                {{-- Remove --}}
                <form action="{{ route('pos.remove', $detail->id) }}" method="POST" class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm text-danger border-0 p-0 d-flex align-items-center justify-content-center"
                            title="Eliminar"
                            style="width:28px;height:28px;">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </button>
                </form>

                {{-- Name + note --}}
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold text-dark text-truncate" style="font-size:.88rem;">
                        {{ $detail->product->name }}
                    </div>
                    @if($detail->note)
                        <div class="small text-muted fst-italic text-truncate">
                            <i class="bi bi-sticky me-1"></i>{{ $detail->note }}
                        </div>
                    @endif
                </div>

                {{-- Qty + price --}}
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <form action="{{ route('pos.update', $detail->id) }}" method="POST">
                        @csrf
                        <input type="number"
                               name="quantity"
                               value="{{ $detail->quantity }}"
                               class="form-control form-control-sm text-center fw-bold border bg-light"
                               style="width:52px;height:36px;"
                               min="1"
                               onchange="this.form.submit()">
                    </form>
                    <div class="fw-bold text-end text-danger"
                         style="width:62px;font-size:.88rem;">
                        {{ number_format($detail->price * $detail->quantity, 2) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── TOTALS ───────────────────────────── --}}
    <div class="bg-light px-3 py-2 border-top border-bottom">
        <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Subtotal:</span>
            <span>{{ number_format($order->details->sum(fn($d) => $d->price * $d->quantity), 2) }}</span>
        </div>
        @if($order->discount > 0)
            <div class="d-flex justify-content-between small text-success mb-1">
                <span>Descuento:</span>
                <span>-{{ number_format($order->discount, 2) }}</span>
            </div>
        @endif
        @if($order->tip > 0)
            <div class="d-flex justify-content-between small text-info mb-1">
                <span>Propina:</span>
                <span>+{{ number_format($order->tip, 2) }}</span>
            </div>
        @endif
        <div class="d-flex justify-content-between mt-1 pt-1 border-top">
            <span class="fs-5 fw-bold text-dark">Total:</span>
            <span class="fs-5 fw-bold text-danger">{{ $currency ?? 'S/' }}{{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    {{-- ── ACTION BUTTONS ───────────────────── --}}
    <div class="p-3">
        <div class="row g-2 mb-2">
            <div class="col-6">
                <button class="btn btn-outline-secondary w-100 btn-sm py-2 fw-bold"
                        data-bs-toggle="modal"
                        data-bs-target="#optionsModal">
                    <i class="bi bi-sliders me-1"></i>Ajustes
                </button>
            </div>
            <div class="col-6">
                <a href="{{ route('pos.split.content', $order->id ?? 0) }}"
                   class="btn btn-outline-primary w-100 btn-sm py-2 fw-bold">
                    <i class="bi bi-scissors me-1"></i>Dividir
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('pos.kitchen', $order->id) }}"
                   target="_blank"
                   class="btn btn-outline-danger w-100 btn-sm py-2 fw-bold">
                    <i class="bi bi-fire me-1"></i>Comanda
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('pos.precheck', $order->id) }}"
                   target="_blank"
                   class="btn btn-outline-dark w-100 btn-sm py-2 fw-bold">
                    <i class="bi bi-receipt me-1"></i>Pre-Cuenta
                </a>
            </div>
        </div>

        <button class="btn btn-success w-100 fw-bold shadow-sm"
                style="height:52px;font-size:1.05rem;"
                data-bs-toggle="modal"
                data-bs-target="#checkoutModal">
            <i class="bi bi-wallet2 me-2"></i>COBRAR
        </button>
    </div>

    {{-- ── CHECKOUT MODAL ───────────────────── --}}
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('pos.checkout', $order->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-wallet2 me-2"></i>Finalizar Venta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Cliente --}}
                    <div class="mb-3 bg-light p-3 rounded border">
                        <label class="form-label fw-bold small text-uppercase text-muted mb-2">
                            <i class="bi bi-person-badge me-1"></i>Cliente
                        </label>
                        <div class="input-group mb-2">
                            <input type="text"
                                   list="clientsList"
                                   id="clientSearch"
                                   name="client_name"
                                   class="form-control"
                                   placeholder="Buscar cliente..."
                                   autocomplete="off">
                            <input type="hidden" name="client_id" id="clientId">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="document.getElementById('clientSearch').value='';document.getElementById('clientId').value='';">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <datalist id="clientsList">
                            @foreach($clients as $client)
                                <option data-id="{{ $client->id }}" value="{{ $client->name }}">
                                    {{ $client->document_number }}
                                </option>
                            @endforeach
                        </datalist>
                        <div class="d-flex gap-2">
                            <input type="text"
                                   name="client_document"
                                   id="clientDoc"
                                   class="form-control form-control-sm"
                                   placeholder="RUC / DNI / Doc">
                            <select name="document_type"
                                    class="form-select form-select-sm"
                                    style="max-width:110px;">
                                <option>Ticket</option>
                                <option>Boleta</option>
                                <option>Factura</option>
                            </select>
                        </div>
                    </div>

                    {{-- Payment method --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Método de Pago</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="payment_method"
                                   id="payCash" value="cash" checked
                                   onchange="toggleCashInput(true)">
                            <label class="btn btn-outline-success fw-bold" for="payCash">
                                <i class="bi bi-cash me-1"></i>Efectivo
                            </label>
                            <input type="radio" class="btn-check" name="payment_method"
                                   id="payCard" value="card"
                                   onchange="toggleCashInput(false)">
                            <label class="btn btn-outline-primary fw-bold" for="payCard">
                                <i class="bi bi-credit-card me-1"></i>Tarjeta
                            </label>
                        </div>
                    </div>

                    {{-- Cash received --}}
                    <div class="mb-3" id="cashInputGroup">
                        <label class="form-label fw-bold small">Monto Recibido</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $currency ?? '$' }}</span>
                            <input type="number"
                                   step="0.01"
                                   name="received_amount"
                                   id="receivedAmount"
                                   class="form-control fs-4 fw-bold text-success"
                                   value="{{ $order->total }}"
                                   oninput="calculateChange()">
                        </div>
                        <div class="mt-1 text-end">
                            <small class="text-muted">Cambio: </small>
                            <span class="fw-bold text-dark" id="changeAmount">0.00</span>
                        </div>
                    </div>

                    <div class="text-center border-top pt-3">
                        <div class="text-muted small mb-1">TOTAL A COBRAR</div>
                        <div class="fw-bold text-success" style="font-size:1.8rem;">
                            {{ $currency ?? 'S/' }}{{ number_format($order->total, 2) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" class="btn btn-success w-100 fw-bold py-3 fs-6">
                        <i class="bi bi-check-circle-fill me-2"></i>CONFIRMAR PAGO
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function toggleCashInput(show) {
        document.getElementById('cashInputGroup').style.display = show ? 'block' : 'none';
    }
    function calculateChange() {
        var total    = {{ $order->total }};
        var received = parseFloat(document.getElementById('receivedAmount').value) || 0;
        var change   = received - total;
        var el = document.getElementById('changeAmount');
        el.innerText    = change >= 0 ? change.toFixed(2) : '0.00';
        el.style.color  = change >= 0 ? 'green' : 'red';
    }
    document.getElementById('clientSearch').addEventListener('input', function (e) {
        var options = document.getElementById('clientsList').options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value === e.target.value) {
                document.getElementById('clientId').value = options[i].getAttribute('data-id');
                break;
            } else {
                document.getElementById('clientId').value = '';
            }
        }
    });
    </script>

@else
    {{-- Empty state --}}
    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted py-5">
        <i class="bi bi-cart-x display-1 mb-3 opacity-25"></i>
        <h5 class="fw-bold">Mesa Vacía</h5>
        <small>Agrega productos para comenzar</small>
    </div>
@endif

{{-- ── OPTIONS MODAL ────────────────────────── --}}
<div class="modal fade" id="optionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h6 class="modal-title fw-bold">Ajustes de Cuenta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pos.discount', $order->id ?? 0) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descuento Global</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">{{ $currency ?? 'S/' }}</span>
                            <input type="number" step="0.01" name="discount"
                                   class="form-control text-center fw-bold"
                                   value="{{ number_format($order->discount ?? 0, 2, '.', '') }}"
                                   placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Propina</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted">{{ $currency ?? 'S/' }}</span>
                            <input type="number" step="0.01" name="tip"
                                   class="form-control text-center fw-bold"
                                   value="{{ number_format($order->tip ?? 0, 2, '.', '') }}"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Aplicar Todo</button>
                </div>
            </form>
        </div>
    </div>
</div>
