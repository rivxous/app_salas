@extends('layouts.base')

@section('title', 'Sistema de Reservas')

@section('content')
    {{ Auth::user()->rol }}
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="container mt-4">
                <div class="text-center">
                    <h2 class="text-primary">Bienvenido al Sistema</h2>
                    <p class="lead">Seleccione el módulo al que desea acceder</p>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <!-- Módulo de Usuarios -->
                    <div class="nav-item">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h5 class="card-title">Usuarios</h5>
                                <p class="card-text">Visualización de usuarios</p>
                                <a href="{{ route('usuarios.index') }}" class="btn btn-primary">Acceder</a>
                            </div>
                        </div>
                    </div>

                    <!-- Módulo de Reservas -->
                    <div class="nav-item">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h5 class="card-title">Reservas</h5>
                                <p class="card-text">Visualización de reservas actuales.</p>
                                <a href="{{ route('reservas.index') }}" class="btn btn-success">Acceder</a>
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->rol == 'admin')
                        <!-- Módulo de Salas -->
                        <div class="nav-item mx-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Salas</h5>
                                    <p class="card-text">Visualización de salas</p>
                                    <a href="{{ route('salas.index') }}" class="btn btn-warning">Acceder</a>
                                </div>
                            </div>
                        </div>
                    @endif


                    <!-- Módulo de Reportes Estadísticos -->
                    <div class="nav-item mx-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h5 class="card-title">Reportes Estadísticos</h5>
                                <p class="card-text">Acceda a los reportes de uso y estadísticas</p>
                                <a href="{{ route('reportes.index') }}" class="btn btn-info">Acceder</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Contenido Principal -->
            <main class=" container-fluid">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-calendar-day me-2"></i>Calendario de Reservas
                    </h1>

                </div>

                <!-- Calendario -->
                <div class="container-fluid row">
                    <div class="container-fluid col-md-8">
                        <div id="calendar" class="shadow-lg bg-white p-3 rounded-3"></div>
                    </div>
                    <div class="container-fluid col-md-4 citas p-0"> <!-- p-0 para eliminar padding -->
                        <!-- Título con nuevo estilo -->
                        <div class="card-header bg-white text-#2c3e50 rounded-2"> <!-- rounded-0 para quitar bordes redondeados -->
                            <p class="h5 mb-0 p-2">Mis Reservas</p>
                        </div>

                        <div class="eventos-container p-3"> <!-- Agregamos padding al contenedor -->
                            @foreach ($eventos as $evento)
                                <div class="container-fluid evento-item">
                                    <p class="evento-titulo">{{ $evento['title'] }}</p>
                                    <p class="evento-hora">Hora inicio: {{ $evento['start'] }}</p>
                                    <p class="evento-hora">Hora fin: {{ $evento['end'] }}</p>
                                </div>
                                <div class="divisor-evento"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Estilos Personalizados -->
    <style>
        .citas {
            border: 1px solid #dee2e6; /* Borde similar al card */
            max-height: 70vh;
            overflow-y: auto;
        }

        .card-header {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        /* Mantener otros estilos necesarios */
        .eventos-container {
            margin-top: 0 !important;
        }

        .evento-titulo {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .evento-titulo {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .evento-hora {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 0.3rem;
        }

        .divisor-evento {
            height: 1px;
            background-color: #e0e0e0;
            margin: 1rem 0;
        }

        .evento-item {
            padding: 1rem 0;
        }

        .citas {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            max-height: 75vh;
            overflow-y: auto;
            margin: 20px auto;
        }


        /* En tu archivo CSS */
        .fc-event-custom {
            border-radius: 4px;
            border: none;
            font-size: 0.9em;
            padding: 2px 5px;
        }

        .fc-timegrid-slots td {
            vertical-align: top;
        }

        .popover-content-custom {
            max-width: 300px;
            font-size: 0.9em;
        }

        #sidebar {
            min-height: 100vh;
            background: #2c3e50;
            transition: all 0.3s;
        }

        .nav-link {
            transition: all 0.3s;
            border-left: 4px solid transparent;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            background: #34495e;
            border-left-color: #e67e22;
        }

        .nav-link.active {
            background: #34495e;
            border-left-color: #e67e22;
        }

        .btn-orange {
            background-color: #e67e22;
            color: white;
            border: none;
        }

        .btn-orange:hover {
            background-color: #d35400;
            color: white;
        }

        .fc-event-custom {
            background-color: #e67e22;
            border: none;
            border-radius: 3px;
            font-size: 0.85em;
            padding: 2px 5px;
        }

        .fc-toolbar-title {
            color: #2c3e50;
            font-weight: 600;
        }

        .fc-button-primary {
            background-color: #2c3e50 !important;
            border-color: #2c3e50 !important;
        }
    </style>

    <!-- Script del Calendario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                slotMinTime: '07:30:00',
                slotMaxTime: '18:00:00',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: '{{ route('listar_reservas_calendario') }}',
                    method: 'GET',
                    failure: function() {
                        alert('Error al cargar eventos');
                    }
                },
                locale: 'es',
                timeZone: 'America/Caracas',
                firstDay: 1,
                navLinks: true,
                nowIndicator: true,
                eventTimeFormat: { // Formato de hora en la vista
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                    meridiem: 'short'
                },
                slotLabelFormat: { // Formato de las horas en la columna
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventDidMount: function(info) {
                    console.log(info.event)
                    const content = `
                        <div class="popover-content-custom">
                            <p class="mb-1"><strong> Hora: ${info.timeText}</strong></p>
                            <p class="mb-1"><strong>Titulo: ${info.event.title}</strong></p>
                            <p class="mb-1">Descripción:${info.event.extendedProps.description}</p>
                            <hr class="my-1">
                            <small>Sala: ${info.event.extendedProps.sala}</small><br>
                            <small>Organizador: ${info.event.extendedProps.organizador}</small>
                        </div>
        `;

                    new bootstrap.Popover(info.el, {
                        title: `<i class="fas fa-calendar me-2"></i>Detalles de reserva`,
                        content: content,
                        trigger: 'hover',
                        placement: 'auto',
                        container: 'body',
                        html: true,
                        sanitize: false
                    });
                },
                eventClassNames: 'fc-event-custom',
                slotLabelInterval: '01:00:00', // Intervalo de horas
                slotDuration: '00:30:00', // Duración de cada slot
                allDaySlot: false,
                height: 'auto',
                views: {
                    timeGridWeek: {
                        dayHeaderFormat: {
                            weekday: 'short',
                            day: 'numeric'
                        }
                    }
                }
            });

            calendar.render();
        });
    </script>
@endsection
