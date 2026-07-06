<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JAPAC | @yield('title', 'Saneamiento')</title>

    <link class="rounded-circle" rel="icon" type="image/png" href="{{ asset('dist/img/logo-japac.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css">

    <style>
        .sidebar-menu .nav-link.active { background-color: #007bff !important; color: #fff !important; }
        .app-sidebar { width: 260px; }
        .text-japac { color: #0056b3 !important; }
        .bg-japac { background-color: #0056b3 !important; color: white; }
    </style>
    @yield('css')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        <nav class="app-header navbar navbar-expand bg-body shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-3 d-flex align-items-center">
                        <span class="nav-link">
                            <img src="{{ asset('dist/img/logo-japac.png') }}" alt="User" class="rounded-circle me-2 border bg-white" style="width: 25px; height: 25px; object-fit: contain;">
                            <strong>{{ auth()->user()->nombre }} {{ auth()->user()->paterno }}</strong>
                            <span class="badge bg-dark ms-1 text-uppercase">{{ auth()->user()->puesto->nombre ?? 'Operador' }}</span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn nav-link text-danger fw-bold">
                                <i class="fa-solid fa-power-off"></i> Salir
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand text-center py-3">
                <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
                    <img src="{{ asset('dist/img/logo-japac.png') }}" alt="JAPAC Logo" class="brand-image img-circle elevation-3" style="height: 40px;">
                    <span class="brand-text fw-bold ms-2">JAPAC <span class="text-info">Saneamiento</span></span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-gauge-high"></i>
                                <p>Dashboard Principal</p>
                            </a>
                        </li>

                        <li class="nav-header text-uppercase text-xs fw-bold opacity-75">Control de Campo</li>

                        <li class="nav-item">
                            <a href="{{ route('establecimientos.index') }}" class="nav-link {{ request()->routeIs('establecimientos.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-building"></i>
                                <p>Establecimientos</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('visitas-inspeccion.*', 'inicios-procedimiento.*', 'resolutivos-administrativos.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('visitas-inspeccion.*', 'inicios-procedimiento.*', 'resolutivos-administrativos.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-file-shield"></i>
                                <p>
                                    Inspecciones Formales
                                    <i class="nav-arrow fa-solid fa-angle-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview ps-3">
                                <li class="nav-item">
                                    <a href="{{ route ('visitas_inspeccion.index') }}" class="nav-link">
                                        <i class="nav-icon fa-solid fa-file-contract"></i>
                                        <p>Visitas de Inspección</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inicio_procedimientos.index') }}" class="nav-link">
                                        <i class="nav-icon fa-solid fa-gavel"></i>
                                        <p>Inicio de Procedimiento</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('resolutivo_administrativos.index') }}" class="nav-link">
                                        <i class="nav-icon fa-solid fa-file-signature"></i>
                                        <p>Resolutivo Administrativo</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('inspecciones_informales.index') }}" class="nav-link {{ request()->routeIs('inspecciones_informales.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-clipboard-list text-danger"></i>
                                <p>Inspecciones Informales</p>
                            </a>
                        </li>

                        <li class="nav-header text-uppercase text-xs fw-bold opacity-75">Laboratorio y Finanzas</li>

                        <li class="nav-item {{ request()->routeIs('resultados-laboratorio.*', 'procedimientos-laboratorio.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('resultados-laboratorio.*', 'procedimientos-laboratorio.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-flask-vial"></i>
                                <p>
                                    Laboratorio Externo
                                    <i class="nav-arrow fa-solid fa-angle-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview ps-3">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fa-solid fa-flask"></i>
                                        <p>Resultados de Lab.</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fa-solid fa-scale-balanced"></i>
                                        <p>Inicio de Procedimiento</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('calculo_incumplimientos.index') }}" class="nav-link text-warning">
                                <i class="nav-icon fa-solid fa-calculator"></i>
                                <p class="fw-bold">Calculo de Índice de Incumplimiento</p>
                            </a>
                        </li>

                        <li class="nav-header text-uppercase text-xs fw-bold opacity-75">Configuración</li>

                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-users-gear"></i>
                                <p>Control de Usuarios</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('departamentos.index') }}" class="nav-link {{ request()->routeIs('departamentos.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-network-wired"></i>
                                <p>Departamentos / Áreas</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('puestos.index') }}" class="nav-link {{ request()->routeIs('puestos.*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-briefcase"></i>
                                <p>Puestos / Roles</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main">
            <div class="app-content-header py-3">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold">@yield('content_header')</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content text-sm">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>

        <footer class="app-footer p-3 bg-white border-top d-flex justify-content-between align-items-center">
            <div>
                <strong>Copyright &copy; {{ date('Y') }} <a href="#" class="text-japac text-decoration-none">JAPAC</a>.</strong> Todos los derechos reservados.
            </div>
            <div class="d-none d-sm-inline"><b>Versión AdminLTE</b> 4.0</div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('js')
</body>
</html>
