@extends('layouts.base')

@section('title', 'Inicio')

@section('content')
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


    <div class="container">
        <h2>Calendario de Reservas</h2>

        <div id="calendar" class="col-7 shadow-lg mb-5 bg-white p-3"></div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',  // Vista inicial
                    events: '{{route('listar_reservas_calendario')}}',  // URL para obtener eventos
                    eventClick: function(info) {
                        alert('Evento: ' + info.event.title);
                        // Aquí podrías integrar la lógica de validación o redireccionamiento para hacer una reserva
                    }
                });
                calendar.render();
            });
        </script>
    </div>
@endsection
