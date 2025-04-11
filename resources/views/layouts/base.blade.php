<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title','Sistema de Reserva de Salas')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('js/select2/select2.min.css') }}">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css" rel="stylesheet">

    <!-- Estilos personalizados con temas -->
    <style>
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #495057;
            --bg-card: #ffffff;
            --border-color: #dee2e6;
        }

        .dark-theme {
            --bg-primary: #212529;
            --bg-secondary: #343a40;
            --text-primary: #f8f9fa;
            --text-secondary: #e9ecef;
            --bg-card: #2c3034;
            --border-color: #495057;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .card, .form-control, .form-select, .input-group-text {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .btn-outline-secondary:hover {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }

        .card-header {
            border-bottom-color: var(--border-color);
        }
    </style>

    <!-- Librerías JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>

    <!-- Bootstrap JS Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Full calendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.2/main.min.js"></script>
</head>
<body class="{{ session('theme', 'light-theme') }}">

<!-- Barra superior con botones -->
<div class="container-fluid">
    <div class="d-flex justify-content-between mt-3 me-3">
        <!-- Botón para cambiar tema -->
        <button id="toggle-theme" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-moon-fill"></i> Tema Oscuro
        </button>
        <!-- Botón inicio -->
        <a class="btn btn-sm btn-outline-secondary" href="{{route("/")}}">
            <i class="bi bi-house-check"></i> Home
        </a>

        <!-- Botón de Cerrar Sesión -->
        @auth()
            <div>{{Auth::user()->full_name}}</div>
            <button id="logout-button" class="btn btn-sm btn-danger">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </button>
        @endauth
    </div>
</div>

<!-- Contenido principal -->
<div class="container py-4">
    @yield('content')
</div>




<!-- Scripts globales -->
<script>
    // Cambio de tema
    document.getElementById('toggle-theme').addEventListener('click', function() {
        const body = document.body;
        const isDark = body.classList.contains('dark-theme');
        const icon = this.querySelector('i');

        if(isDark) {
            body.classList.remove('dark-theme');
            localStorage.setItem('theme', 'light-theme');
            icon.className = 'bi bi-moon-fill';
            this.innerHTML = '<i class="bi bi-moon-fill"></i> Tema Oscuro';
        } else {
            body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark-theme');
            icon.className = 'bi bi-sun-fill';
            this.innerHTML = '<i class="bi bi-sun-fill"></i> Tema Claro';
        }
    });

    // Cargar tema guardado al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light-theme';
        document.body.className = savedTheme;

        // Actualizar texto del botón
        const themeButton = document.getElementById('toggle-theme');
        if(savedTheme === 'dark-theme') {
            themeButton.innerHTML = '<i class="bi bi-sun-fill"></i> Tema Claro';
        }
    });

    // Cierre de sesión con confirmación (SweetAlert2)
    document.getElementById('logout-button')?.addEventListener('click', function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se cerrará tu sesión actual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('logout') }}";
            }
        });
    });

</script>

@yield('scripts')

</body>
</html>
