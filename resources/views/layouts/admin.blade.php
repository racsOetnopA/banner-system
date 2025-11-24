


<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel de Administración')</title>

    {{-- CSS AdminLTE + Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer"/>

    {{-- 💡 Importante: esta línea inyecta el cliente de Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <style>
        /* Modern hover for top navigation links */
        .navbar {
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
        }
        .navbar .nav-link.nav-hover {
            position: relative;
            transition: color .2s ease, transform .15s ease;
            color: #f8f8fa; /* texto claro para fondo oscuro */
            padding-bottom: .6rem; /* espacio para la línea sin superposición */
        }
        .navbar .nav-link.nav-hover::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -6px; /* colocar la línea por debajo del texto */
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg,#06b6d4,#6366f1);
            border-radius: 2px;
            transition: transform .25s ease;
        }
        .navbar .nav-link.nav-hover:hover {
            color: #ffffff; /* hover claro sobre fondo oscuro */
            transform: translateY(-2px);
        }
        .navbar .nav-link.nav-hover:hover::after {
            transform: translateX(-50%) scaleX(1);
        }
        /* Reduce underline space on small screens */
        @media (max-width: 576px) {
            .navbar .nav-link.nav-hover { padding-bottom: .45rem; }
            .navbar .nav-link.nav-hover::after { bottom: -4px; width: 70%; height: 2px; }
        }
    </style>
</head>

<body class="hold-transition" style="background-color: #f8f8fa;">


    <nav class="navbar navbar-dark fixed-top shadow-sm " style="position: fixed; width:100%; z-index:1030; background-color:#566cac; color: white; font-size: 18px">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="nav-link nav-hover px-3 {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
                <a href="{{ route('webs.index') }}" class="nav-link nav-hover px-3 {{ request()->is('webs*') ? 'active' : '' }}">
                    <i class="fas fa-globe me-2"></i>Webs
                </a>
                <a href="{{ route('zones.index') }}" class="nav-link nav-hover px-3 {{ request()->is('zones*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group me-2"></i>Zonas
                </a>
                <a href="{{ route('banners.index') }}" class="nav-link nav-hover px-3 {{ request()->is('banners*') ? 'active' : '' }}">
                    <i class="fas fa-image me-2"></i>Banners
                </a>
                <a href="{{ route('assignments.index') }}" class="nav-link nav-hover px-3 {{ request()->is('assignments*') ? 'active' : '' }}">
                    <i class="fas fa-link me-2"></i>Asignaciones
                </a>
                <a href="{{ route('estadisticas.index') }}" class="nav-link nav-hover px-3 {{ request()->is('estadisticas*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </a>
                <a href="{{ route('webs.index') }}" class="nav-link nav-hover px-3 {{ request()->is('webs*') ? 'active' : '' }}">
                    <i class="fas fa-globe me-2"></i>Web
                </a>
            </div>

            <div class="d-flex align-items-center ms-auto">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link nav-hover px-3 text-white" style="text-decoration:none;">
                        <i class="fas fa-sign-out-alt me-2"></i><b>Salir</b>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <div class="content-wrapper" style="min-height: 85vh; margin-top: 120px;">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="mt-5 p-3">
            @yield('content')
        </div>
    </div>

    {{-- Footer --}}
    <footer class="main-footer text-center py-3border-top shadow-sm mt-5" style="background-color: whitesmoke;">
        <small>
            <span class="text-muted"> Copyright © <span id="year"></span>
                    by <a href="https://adclichosting.com/" target="_blank" data-bs-toggle="tooltip" title="{{ Route::currentRouteName() }}" data-bs-placement="top" data-bs-custom-class="tooltip-primary">
                    <span class="fw-medium text-primary">Adclic Hosting</span>
                </a> All rights reserved
            </span>
        </small>
    </footer>

    {{-- JS --}}
    <!-- jQuery (required by some plugins like bootstrap-multiselect) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('vendor/admin-lte/js/adminlte.min.js') }}"></script>

    <script>
    // Central tooltip initialization: enables HTML tooltips and zero show delay.
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(el => {
            try { if (el._tooltip) el._tooltip.dispose(); } catch(e) {}
            el._tooltip = new bootstrap.Tooltip(el, { html: true, delay: { show: 0, hide: 100 } });
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
