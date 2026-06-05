@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7">

            <div class="card border-danger shadow-lg overflow-hidden">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>ZONA DE PELIGRO: Reinicio del Sistema
                    </h5>
                </div>
                <div class="card-body text-center p-4 p-md-5">

                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mb-3"
                             style="width:80px;height:80px;">
                            <i class="bi bi-trash3-fill text-danger" style="font-size:2.2rem;"></i>
                        </div>
                        <h4 class="fw-bold text-danger mb-2">¿Estás listo para inaugurar?</h4>
                        <p class="text-muted">
                            Esta acción eliminará todos los datos de prueba para dejar el sistema
                            listo para producción.
                        </p>
                    </div>

                    <div class="alert alert-warning text-start d-inline-block w-100 rounded-3">
                        <div class="fw-bold mb-2">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Se eliminarán permanentemente:
                        </div>
                        <ul class="mb-0">
                            <li>{{ $counts['orders'] }} Ventas y pedidos registrados.</li>
                            <li>{{ $counts['reservations'] }} Reservas de mesa.</li>
                            <li>{{ $counts['logs'] }} Movimientos de Kardex.</li>
                            <li>El <strong>Stock</strong> de todos los productos volverá a <strong>0</strong>.</li>
                        </ul>
                    </div>

                    <p class="mt-3 small text-muted">
                        * Usuarios, Productos, Mesas, Clientes y Configuración
                        <strong>NO</strong> se borrarán.
                    </p>

                    <hr class="my-4">

                    <form action="{{ route('system.reset') }}"
                          method="POST"
                          onsubmit="return confirm('¿ESTÁS 100% SEGURO? NO HAY VUELTA ATRÁS.');">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Ingresa tu contraseña para confirmar:
                            </label>
                            <input type="password"
                                   name="password"
                                   class="form-control text-center fw-bold"
                                   style="max-width:280px;margin:0 auto;"
                                   required
                                   placeholder="Tu contraseña actual">
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg fw-bold w-100">
                            <i class="bi bi-trash3-fill me-2"></i>BORRAR TODO E INICIAR DE CERO
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
