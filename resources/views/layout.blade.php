<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Agrega enlaces a tus estilos CSS o scripts aquí -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>let urlApi = "/app_salas/public/api";</script>
    <script>let url = "{{url('/')}}";</script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
</head>
<body>
    <header>
        @include('partials/header') <!-- Archivo separado para el header -->
    </header>

    <div class="container">
        <aside>
            @include('partials/sidebar') <!-- Archivo para el sidebar -->
        </aside>
        <main>
            @yield('content') <!-- Aquí va el contenido principal -->
        </main>
    </div>

    <footer>
        @include('partials/footer') <!-- Archivo para el footer -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('js')
</body>
</html>

