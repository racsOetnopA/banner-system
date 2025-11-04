<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap & AdminLTE -->
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
        <link rel="stylesheet" href="/node_modules/admin-lte/dist/css/adminlte.min.css">


        <!-- Scripts -->
        <script src="/node_modules/admin-lte/dist/js/adminlte.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- zona banner --}}
            <div class="text-center">
            <span>antes</span>
            <div id="zone-header"></div>
            <script src="https://banner-system.test:8080/js/banner.js?zone=header&site=blog.com&interval=12000"></script>
            <span>despues</span>
            </div>
            {{-- zona banner --}}



            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
