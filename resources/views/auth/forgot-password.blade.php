<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Recuperar Contraseña – Restaurante POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --brand: #c0392b; }
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1c1c2e 0%, #2d1b1b 50%, #1c1c2e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .login-logo {
            width: 64px; height: 64px;
            background: var(--brand);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 800; color: white;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(192,57,43,.4);
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 24px 60px rgba(0,0,0,.3);
            overflow: hidden;
        }

        .login-card .card-body { padding: 2rem; }

        .form-control {
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: .95rem;
            border: 1.5px solid #e5e7eb;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(192,57,43,.15);
        }

        .btn-brand {
            background: var(--brand);
            border: none;
            color: white;
            border-radius: 10px;
            padding: .85rem;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: .3px;
            transition: all .2s;
        }
        .btn-brand:hover {
            background: #a93226;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(192,57,43,.35);
        }
        .btn-brand:active { transform: translateY(0); }

        .back-link {
            color: var(--brand);
            text-decoration: none;
            font-weight: 600;
            font-size: .875rem;
            transition: opacity .2s;
        }
        .back-link:hover { opacity: .75; color: var(--brand); }

        .info-box {
            background: #fef9f9;
            border: 1.5px solid #f5c6cb;
            border-radius: 10px;
            padding: .85rem 1rem;
            font-size: .85rem;
            color: #6b7280;
        }

        @media (max-width: 400px) {
            .login-card .card-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="text-center mb-4">
        <div class="login-logo"><i class="bi bi-shop"></i></div>
        <h4 class="fw-bold text-white mb-1">Restaurante POS</h4>
        <p class="text-white opacity-50 mb-0 small">Sistema de Gestión Profesional</p>
    </div>

    <div class="login-card">
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <span style="font-size:2.5rem;">🔐</span>
                </div>
                <h5 class="fw-bold text-dark mb-1">¿Olvidaste tu contraseña?</h5>
                <p class="text-muted small mb-0">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>
            </div>

            @if(session('status'))
                <div class="alert border-0 rounded-3 small mb-4"
                     style="background:#d1fae5; color:#065f46;">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 small mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label fw-bold text-dark small">
                        <i class="bi bi-envelope me-1"></i>Correo Electrónico
                    </label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="admin@restaurante.com"
                           autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-brand">
                        <i class="bi bi-send me-2"></i>ENVIAR ENLACE DE RECUPERACIÓN
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
                    </a>
                </div>
            </form>
        </div>

        <div class="border-top text-center py-3">
            <small class="text-muted">Desarrollado con Laravel & Bootstrap</small>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
