@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ──────────────────────────── --}}
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-start gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-grid-3x3-gap-fill me-2 text-danger"></i>Diseño de Salón
            </h4>
            <p class="text-muted mb-0 small">
                <span class="d-none d-md-inline">Arrastra las mesas y guarda la distribución</span>
                <span class="d-md-none">Gestiona zonas y mesas</span>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#areaModal">
                <i class="bi bi-plus-circle me-1"></i>
                <span class="d-none d-sm-inline">Nueva </span>Zona
            </button>
            <button class="btn btn-outline-secondary"
                    data-bs-toggle="modal"
                    data-bs-target="#tableModal">
                <i class="bi bi-plus-lg me-1"></i>
                <span class="d-none d-sm-inline">Nueva </span>Mesa
            </button>
            <button class="btn btn-success fw-bold shadow-sm d-none d-md-inline-flex align-items-center gap-2"
                    onclick="savePositions()"
                    id="btnSave">
                <i class="bi bi-save"></i> Guardar Diseño
            </button>
        </div>
    </div>

    {{-- ── AREA TABS ────────────────────────── --}}
    <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" id="areaTabs" role="tablist"
        style="-webkit-overflow-scrolling:touch;">
        @foreach($areas as $index => $area)
            <li class="nav-item flex-shrink-0" role="presentation">
                <button class="nav-link {{ $index == 0 ? 'active' : '' }} fw-bold"
                        id="tab-{{ $area->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#area-{{ $area->id }}"
                        type="button" role="tab">
                    {{ $area->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="areaTabsContent">
        @foreach($areas as $index => $area)
            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                 id="area-{{ $area->id }}"
                 role="tabpanel">

                {{-- Area toolbar --}}
                <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 border rounded-3 gap-2">
                    <small class="text-muted d-none d-md-block">
                        <i class="bi bi-info-circle me-1"></i>
                        Arrastra las mesas y luego presiona <b>Guardar Diseño</b>.
                    </small>
                    <small class="text-muted d-md-none">
                        <i class="bi bi-info-circle me-1"></i> Vista de mesas
                    </small>
                    <form action="{{ route('tables.destroyArea', $area->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Eliminar esta zona y sus mesas?')"
                          class="flex-shrink-0">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger border-0">
                            <i class="bi bi-trash me-1"></i> Eliminar Zona
                        </button>
                    </form>
                </div>

                {{-- ── DESKTOP DRAG CANVAS ──────────── --}}
                <div class="salon-canvas d-none d-md-block bg-white border rounded-3 shadow-sm position-relative"
                     style="height:600px;
                            background-image:radial-gradient(#dee2e6 1px,transparent 1px);
                            background-size:20px 20px;
                            overflow:hidden;">

                    @foreach($area->tables as $table)
                        <div class="draggable-table position-absolute d-flex flex-column align-items-center justify-content-center bg-white border shadow-sm rounded-3"
                             id="table-{{ $table->id }}"
                             data-id="{{ $table->id }}"
                             style="width:100px;height:100px;
                                    left:{{ $table->x_pos }}px;
                                    top:{{ $table->y_pos }}px;
                                    cursor:grab;z-index:10;
                                    transition:box-shadow .2s;">

                            <i class="bi bi-display fs-3 mb-1 {{ $table->status == 'available' ? 'text-success' : 'text-danger' }}"></i>
                            <span class="fw-bold small text-center text-truncate w-100 px-1">
                                {{ $table->name }}
                            </span>

                            <form action="{{ route('tables.destroyTable', $table->id) }}"
                                  method="POST"
                                  class="position-absolute top-0 end-0 m-1">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm p-0 text-danger"
                                        style="opacity:.3;"
                                        onclick="return confirm('¿Borrar mesa?')">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                {{-- Save button visible only on desktop canvas --}}
                <div class="d-none d-md-flex justify-content-end mt-3">
                    <button class="btn btn-success fw-bold shadow-sm px-4"
                            onclick="savePositions()" id="btnSave2">
                        <i class="bi bi-save me-2"></i> Guardar Diseño
                    </button>
                </div>

                {{-- ── MOBILE LIST VIEW ─────────────── --}}
                <div class="d-md-none">
                    @if($area->tables->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-grid fs-1 d-block mb-2 opacity-25"></i>
                            No hay mesas en esta zona.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($area->tables as $table)
                                <div class="col-6 col-sm-4">
                                    <div class="card border shadow-sm h-100 text-center">
                                        <div class="card-body py-3 px-2">
                                            <i class="bi bi-display fs-2 mb-2 {{ $table->status == 'available' ? 'text-success' : 'text-danger' }}"></i>
                                            <div class="fw-bold small text-truncate">{{ $table->name }}</div>
                                            <span class="badge mt-1 {{ $table->status == 'available' ? 'bg-success' : 'bg-danger' }}"
                                                  style="font-size:.65rem;">
                                                {{ $table->status == 'available' ? 'Libre' : 'Ocupada' }}
                                            </span>
                                        </div>
                                        <div class="card-footer p-2 bg-transparent border-top">
                                            <form action="{{ route('tables.destroyTable', $table->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Borrar mesa?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @endforeach
    </div>

</div>

{{-- ── NEW AREA MODAL ───────────────────────── --}}
<div class="modal fade" id="areaModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form action="{{ route('tables.storeArea') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Nueva Zona</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="name" class="form-control" required
                       placeholder="Ej: Terraza">
            </div>
            <div class="modal-footer p-2">
                <button class="btn btn-danger w-100 fw-bold">Crear Zona</button>
            </div>
        </form>
    </div>
</div>

{{-- ── NEW TABLE MODAL ──────────────────────── --}}
<div class="modal fade" id="tableModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form action="{{ route('tables.storeTable') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Nueva Mesa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Mesa 1" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Zona</label>
                    <select name="area_id" class="form-select">
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button class="btn btn-danger w-100 fw-bold">Crear Mesa</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const draggables = document.querySelectorAll('.draggable-table');
    let activeDrag = null, initialX, initialY;

    draggables.forEach(el => el.addEventListener('mousedown', dragStart));
    document.addEventListener('mouseup', dragEnd);
    document.addEventListener('mousemove', drag);

    function dragStart(e) {
        if (e.target.closest('form')) return;
        activeDrag = e.currentTarget;
        initialX = e.clientX - activeDrag.offsetLeft;
        initialY = e.clientY - activeDrag.offsetTop;
        activeDrag.style.cursor = 'grabbing';
        activeDrag.style.zIndex = 100;
        activeDrag.classList.add('shadow-lg');
    }

    function dragEnd() {
        if (!activeDrag) return;
        activeDrag.style.cursor = 'grab';
        activeDrag.style.zIndex = 10;
        activeDrag.classList.remove('shadow-lg');
        activeDrag = null;
    }

    function drag(e) {
        if (!activeDrag) return;
        e.preventDefault();
        let x = e.clientX - initialX;
        let y = e.clientY - initialY;
        if (x < 0) x = 0;
        if (y < 0) y = 0;
        activeDrag.style.left = x + 'px';
        activeDrag.style.top  = y + 'px';
    }
});

function savePositions() {
    const btn = document.getElementById('btnSave') || document.getElementById('btnSave2');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
    btn.disabled = true;

    const positions = [];
    document.querySelectorAll('.draggable-table').forEach(el => {
        positions.push({
            id: el.getAttribute('data-id'),
            x: parseInt(el.style.left) || 0,
            y: parseInt(el.style.top)  || 0
        });
    });

    fetch("{{ route('tables.updatePositions') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ positions })
    })
    .then(r => { if (!r.ok) throw new Error(r.statusText); return r.json(); })
    .then(data => {
        if (data.status === 'success') alert('¡Diseño guardado con éxito! ✅');
        else alert('Error: ' + data.message);
    })
    .catch(err => alert('Error al guardar:\n' + err.message))
    .finally(() => { btn.innerHTML = orig; btn.disabled = false; });
}
</script>
@endsection
