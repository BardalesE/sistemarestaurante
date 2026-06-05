<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Nueva Contraseña – Restaurante POS</title>
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
            max-width: 420px;
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
        .form-control:disabled {
            background: #f9fafb;
            color: #9ca3af;
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

        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            transition: color .2s;
        }
        .toggle-password:hover { color: var(--brand); }

        .strength-bar {
            height: 4px;
            border-radius: 4px;
            background: #e5e7eb;
            margin-top: 6px;
            overflow: hidden;
        }
        .strength-fill {
            height: 100%;
            border-radius: 4px;
            transition: width .3s, background .3s;
            width: 0%;
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
                    <span style="font-size:2.5rem;">🔑</span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Crear nueva contraseña</h5>
                <p class="text-muted small mb-0">Elige una contraseña segura de al menos 8 caracteres.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 small mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold text-dark small">
                        <i class="bi bi-envelope me-1"></i>Correo Electrónico
                    </label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ $email ?? old('email') }}"
                           required
                           readonly
                           autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold text-dark small">
                        <i class="bi bi-lock me-1"></i>Nueva Contraseña
                    </label>
                    <div class="password-wrapper">
                        <input type="password"
                               class="form-control pe-5 @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               required
                               minlength="8"
                               placeholder="Mínimo 8 caracteres"
                               autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-password" onclick="toggleVisibility('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="strength-bar mt-1">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted" id="strengthText" style="font-size:.75rem;"></small>
                        <small class="text-muted" style="font-size:.75rem;">Mín. 8 caracteres</small>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-bold text-dark small">
                        <i class="bi bi-lock-fill me-1"></i>Confirmar Contraseña
                    </label>
                    <div class="password-wrapper">
                        <input type="password"
                               class="form-control pe-5"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               placeholder="Repite la contraseña"
                               autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="toggleVisibility('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-brand">
                        <i class="bi bi-shield-check me-2"></i>RESTABLECER CONTRASEÑA
                    </button>
                </div>
            </form>
        </div>

        <div class="border-top text-center py-3">
            <small class="text-muted">Desarrollado con Laravel & Bootstrap</small>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function checkStrength(val) {
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');
    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '0%',   color: '#e5e7eb', label: '' },
        { pct: '25%',  color: '#ef4444', label: 'Muy débil' },
        { pct: '50%',  color: '#f97316', label: 'Débil' },
        { pct: '75%',  color: '#eab308', label: 'Aceptable' },
        { pct: '90%',  color: '#22c55e', label: 'Fuerte' },
        { pct: '100%', color: '#16a34a', label: 'Muy fuerte' },
    ];
    const l = levels[score] ?? levels[5];
    fill.style.width    = l.pct;
    fill.style.background = l.color;
    text.textContent    = l.label;
    text.style.color    = l.color;
}
</script>
</body>
</html>
