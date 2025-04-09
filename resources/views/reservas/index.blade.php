@extends('layouts.base')

@section('title', 'Lista de Reservas')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Listado de reservas QQGAS</h2>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('/') }}" class="btn btn-success shadow-sm">Regresar al inicio</a>
            <a href="{{ route('reservas.create') }}" class="btn btn-success shadow-sm">Crear reserva</a>
        </div>

        @if(Session::get('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ Session::get('success') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            </script>
        @endif

        <div class="table-responsive">
            <table class="table table-hover shadow-sm">
                <thead class="table-dark">
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Tipo de Evento</th>
                    <th>Sala</th>
                    <th>Horarios Reservados</th>
                    <th>Creador</th>
                    <th>Participantes</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($reservas as $reserva)
                    <tr>
                        <td class="fw-bold text-uppercase">{{ $reserva->titulo }}</td>
                        <td>{{ $reserva->descripcion }}</td>
                        <td>{{ $reserva->tipoEvento }}</td>
                        <td>{{ $reserva->sala->nombre }}</td>
                        <td>{{ $reserva->horarios_new }}</td>
                        <td>{{ $reserva->usuario_creador_reserva->nombre }}</td>
                        <td>{{ $reserva->participantes }}</td>
                        <td>
                            <a href="{{ route('reservas.show', ['reserva' => $reserva->id]) }}" class="btn btn-warning btn-sm">Editar</a>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $reserva->id }})">Eliminar</button>

                            <form id="delete-form-{{ $reserva->id }}" action="{{ route('reservas.destroy', ['reserva' => $reserva->id]) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
{{--    <div class="container">--}}
{{--        <h2>Calendario de Reservas</h2>--}}

{{--        <div id="calendar"></div>--}}

{{--        <script>--}}
{{--            document.addEventListener('DOMContentLoaded', function () {--}}
{{--                var calendarEl = document.getElementById('calendar');--}}
{{--                var calendar = new FullCalendar.Calendar(calendarEl, {--}}
{{--                    initialView: 'dayGridMonth',  // Vista inicial--}}
{{--                    events: '{{route('listar_reservas_calendario')}}',  // URL para obtener eventos--}}
{{--                    eventClick: function(info) {--}}
{{--                        alert('Evento: ' + info.event.title);--}}
{{--                        // Aquí podrías integrar la lógica de validación o redireccionamiento para hacer una reserva--}}
{{--                    }--}}
{{--                });--}}
{{--                calendar.render();--}}
{{--            });--}}
{{--        </script>--}}
{{--    </div>--}}

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>
@endsection
