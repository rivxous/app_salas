<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title','Sistema de Reserva de Salas')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <!-- Estilos adicionales -->
    <link rel="stylesheet" href="{{ asset('js/select2/select2.min.css') }}">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <!-- Librerías JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>

    {{-- Full calendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.js"></script>


    <!-- Estilos personalizados -->
    <style>
        .light-theme {
            background-color: #f8f9fa; /* Fondo claro */
            color: #000; /* Texto negro */
        }

        .dark-theme {
            background-color: #000; /* Fondo oscuro */
            color: #fff; /* Texto blanco */
        }

        .theme-global {
            background-color: #f8f9fa;
            color: #000;
        }

    </style>
</head>
<body class="theme-global">

<!-- Botón de Cerrar Sesión en la esquina superior derecha -->
<div class="container-fluid">
    <div class="d-flex justify-content-end mt-3 me-3">
        @auth()
            <button id="logout-button" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        @endauth
    </div>
</div>

<!-- Contenido principal -->
<div class="container">
    @yield('content')
</div>

<!-- Script de cierre de sesión con confirmación -->
<script>
    document.getElementById('logout-button').addEventListener('click', function () {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se cerrará tu sesión.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cerrar sesión'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('logout') }}";
            }
        });
    });
</script>

@yield('js')

</body>
</html>
