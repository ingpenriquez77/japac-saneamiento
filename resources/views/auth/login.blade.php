<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JAPAC | Iniciar Sesión</title>

    <link rel="icon" type="image/png" href="{{ asset('dist/img/logo-japac.jpg') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f6f9 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            width: 420px;
            background: #ffffff;
            border-radius: 8px;
            border-top: 4px solid #0056b3; /* Línea superior con el azul de JAPAC */
            padding: 35px;
        }

        .logo-container {
            background: #ffffff;
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }

        /* Inputs estilizados */
        .form-control-custom {
            border-radius: 6px;
            border: 1px solid #cccccc;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15);
            outline: none;
        }

        /* Botón con el azul JAPAC */
        .btn-japac {
            background-color: #0056b3;
            border-color: #0056b3;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background-color 0.2s;
        }
        .btn-japac:hover {
            background-color: #004085;
            border-color: #004085;
            color: #ffffff;
        }

        .text-link {
            color: #0056b3;
            text-decoration: underline;
            font-size: 13px;
        }
        .text-link:hover {
            color: #004085;
        }
    </style>
</head>
<body class="hold-transition login-page d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh; gap: 15px;">

    <div class="logo-container text-center">
        <img src="{{ asset('dist/img/logo-japac.jpg') }}" alt="JAPAC Logo" style="height: 120px; object-fit: contain;">
    </div>

    <div class="login-card shadow-sm">
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="usuario" class="form-label text-secondary fw-semibold mb-1" style="font-size: 13px;">Usuario</label>
                <input id="usuario" class="form-control form-control-custom" type="text" name="usuario" value="{{ old('usuario') }}" required autofocus placeholder="Ingresa tu usuario">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-secondary fw-semibold mb-1" style="font-size: 13px;">Contraseña</label>
                <input id="password" class="form-control form-control-custom" type="password" name="password" required placeholder="Ingresa tu contraseña">
            </div>

            <div class="form-check mb-4 mt-2">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me" style="cursor: pointer;">
                <label class="form-check-label text-muted" for="remember_me" style="font-size: 13px; cursor: pointer; user-select: none;">
                    Recordarme
                </label>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <a class="text-link" href="#">
                    ¿Olvidaste tu contraseña?
                </a>
                <button type="submit" class="btn btn-japac px-4">
                    Iniciar Sesión
                </button>
            </div>

        </form>
    </div>

    <div class="text-muted text-center mt-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">
        JAPAC CLN | Área de Saneamiento
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 🛑 1. Interceptar cuenta dada de Baja
            @if ($errors->has('cuenta_inactiva'))
                Swal.fire({
                    icon: 'error',
                    title: 'Acceso Restringido',
                    text: 'Tu cuenta corporativa no se encuentra activa en el sistema. Por favor, comunícate con el Administrador del Área de Saneamiento para reactivar tus accesos.',
                    confirmButtonColor: '#0056b3',
                    confirmButtonText: 'Entendido'
                });

            // ❌ 2. Interceptar credenciales incorrectas genéricas de Laravel
            @elseif ($errors->has('usuario') || $errors->has('email'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Credenciales Inválidas',
                    text: 'El usuario o la contraseña introducidos son incorrectos. Por favor, verifícalos e intenta de nuevo.',
                    confirmButtonColor: '#0056b3',
                    confirmButtonText: 'Reintentar'
                });
            @endif
        });
    </script>
</body>
</html>
