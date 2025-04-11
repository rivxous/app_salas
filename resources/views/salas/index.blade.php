@extends('layouts.base')

@section('title', 'Listado de Salas')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Listado de Salas QQGAS</h2>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('/') }}" class="btn btn-success shadow-sm">Regresar al inicio</a>
            <a href="{{ route('salas.create') }}" class="btn btn-success shadow-sm">Crear Sala</a>
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
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Capacidad</th>
                    <th>Status</th>
                    <th>Horario Inicio</th>
                    <th>Horario Fin</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($salas as $sala)
{{--                    {{dd($sala->horario_inicio )}}--}}
                    <tr>
                        <td class="fw-bold text-uppercase">{{ $sala->nombre }}</td>
                        <td>{{ $sala->ubicacion }}</td>
                        <td>{{ $sala->capacidad }}</td>
                        <td><span class="badge bg-warning fs-6">{{ $sala->status }}</span></td>
                        <td>{{ $sala->horario_inicio->format('h:i A') }}</td>
                        <td>{{ $sala->horario_fin->format('h:i A') }}</td>
                        <td>
                            <a href="{{ route('salas.show', ['sala' => $sala->id]) }}" class="btn btn-warning btn-sm">Editar</a>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $sala->id }})">Eliminar</button>

                            <form id="delete-form-{{ $sala->id }}" action="{{ route('salas.destroy', ['sala' => $sala->id]) }}" method="POST" style="display: none;">
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
