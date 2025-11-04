


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
</head>


<body class="hold-transition">

    {{-- Navbar superior --}}
    <nav class="navbar navbar-dark bg-dark fixed-top shadow-sm">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-2" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                <i class="fas fa-bars"></i>
            </button>
            <span class="navbar-brand mb-0 h1">Panel de Administración</span>
        </div>
    </nav>

    {{-- Sidebar offcanvas --}}
    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Banner System</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body p-0">
            <nav class="nav flex-column mt-2">
                <a href="{{ route('dashboard') }}" class="nav-link text-white px-3 {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
                <a href="{{ route('banners.index') }}" class="nav-link text-white px-3 {{ request()->is('banners*') ? 'active' : '' }}">
                    <i class="fas fa-image me-2"></i>Banners
                </a>
                <a href="{{ route('zones.index') }}" class="nav-link text-white px-3 {{ request()->is('zones*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group me-2"></i>Zonas
                </a>
                <a href="{{ route('assignments.index') }}" class="nav-link text-white px-3 {{ request()->is('assignments*') ? 'active' : '' }}">
                    <i class="fas fa-link me-2"></i>Asignaciones
                </a>
                <a href="{{ route('estadisticas.index') }}" class="nav-link text-white px-3 {{ request()->is('estadisticas*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </a>

                {{-- Separador --}}
                <hr class="my-2 text-secondary opacity-50">

                {{-- Botón de Salir --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="nav-link text-white px-3 border-0 bg-transparent w-100 text-start"
                        style="cursor:pointer;"
                        data-bs-toggle="tooltip" title="Cerrar sesión">
                        <i class="fas fa-sign-out-alt me-2 text-danger"></i> Salir
                    </button>
                </form>
            </nav>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div class="content-wrapper">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('vendor/admin-lte/js/adminlte.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
