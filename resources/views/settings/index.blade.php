@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-7">

            {{-- ── HEADER ──────────────────────── --}}
            <div class="mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-gear-fill me-2 text-danger"></i>Configuración
                </h4>
                <p class="text-muted mb-0 small">Personaliza la identidad y región de tu negocio</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- ── EMPRESA ──────────────────── --}}
                        <h6 class="fw-bold text-danger mb-3 text-uppercase" style="letter-spacing:.5px;">
                            <i class="bi bi-building me-2"></i>Datos de la Empresa
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold">Nombre del Restaurante</label>
                                <input type="text" name="company_name" class="form-control"
                                       value="{{ $settings['company_name'] ?? '' }}"
                                       placeholder="Ej: Restaurante Vito" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-bold">Teléfono / Pedidos</label>
                                <input type="text" name="company_phone" class="form-control"
                                       value="{{ $settings['company_phone'] ?? '' }}"
                                       placeholder="Ej: 999-888-777">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Dirección</label>
                                <input type="text" name="company_address" class="form-control"
                                       value="{{ $settings['company_address'] ?? '' }}"
                                       placeholder="Ej: Av. Principal 123, Ica">
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        {{-- ── REGIÓN ───────────────────── --}}
                        <h6 class="fw-bold text-danger mb-3 text-uppercase" style="letter-spacing:.5px;">
                            <i class="bi bi-globe me-2"></i>Región y Sistema
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-7">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-clock me-1"></i>Zona Horaria
                                </label>
                                <select name="timezone" class="form-select">
                                    @foreach($timezones as $tz => $label)
                                        <option value="{{ $tz }}"
                                                {{ ($settings['timezone'] ?? 'America/Lima') == $tz ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Hora del sistema: <strong>{{ \Carbon\Carbon::now()->format('H:i:s') }}</strong>
                                </small>
                            </div>

                            <div class="col-12 col-sm-5">
                                <label class="form-label fw-bold">Moneda</label>
                                <select name="currency_symbol" class="form-select">
                                    <option value="S/" {{ ($settings['currency_symbol'] ?? '') == 'S/' ? 'selected' : '' }}>
                                        S/ (Soles)
                                    </option>
                                    <option value="$" {{ ($settings['currency_symbol'] ?? '') == '$' ? 'selected' : '' }}>
                                        $ (Dólares)
                                    </option>
                                    <option value="€" {{ ($settings['currency_symbol'] ?? '') == '€' ? 'selected' : '' }}>
                                        € (Euros)
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Mensaje Pie de Ticket</label>
                                <input type="text" name="ticket_footer" class="form-control"
                                       value="{{ $settings['ticket_footer'] ?? '¡Gracias por su visita!' }}">
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        {{-- ── LOGO ─────────────────────── --}}
                        <h6 class="fw-bold text-danger mb-3 text-uppercase" style="letter-spacing:.5px;">
                            <i class="bi bi-image me-2"></i>Logotipo
                        </h6>
                        <div class="row g-3 align-items-center mb-2">
                            <div class="col-12 col-sm-8">
                                <label class="form-label fw-bold">Subir Logo (Ticket y Sistema)</label>
                                <input type="file" name="company_logo" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12 col-sm-4 text-center">
                                @if(isset($settings['company_logo']) && $settings['company_logo'])
                                    <img src="{{ asset('storage/'.$settings['company_logo']) }}"
                                         class="img-thumbnail rounded-3"
                                         style="max-height:80px;max-width:120px;object-fit:cover;">
                                @else
                                    <div class="p-3 border rounded-3 bg-light text-muted">
                                        <i class="bi bi-image fs-2"></i>
                                        <div class="small mt-1">Sin logo</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-danger fw-bold px-5 shadow-sm">
                                <i class="bi bi-save me-2"></i>Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
