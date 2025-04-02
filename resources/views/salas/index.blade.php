@extends('layouts.base')

@section('content')
<div class="row">
    <div class="col-12">
        <div>
            <h2 class="theme-global">Listado de Salas QQGAS</h2>
        </div>
        <div>
            <a href="{{ route('salas.create') }}" class="btn btn-primary">Crear Sala</a>
        </div>
    </div>

    @if(Session::get('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ Session::get('success') }}',
            timer: 3000, // Duración
            timerProgressBar: true,
            showConfirmButton: false
        });
    </script>
    @endif

    <div class="col-12 mt-4">
        <table class="table table-bordered theme-global">
            <tr class="text-secondary">
                <th>NOMBRE</th>
                <th>UBICACIÓN</th>
                <th>CAPACIDAD</th>
                <th>STATUS</th>
                <th>HORARIO INICIO</th>
                <th>HORARIO FIN</th>
                <th>ACCIONES</th>
            </tr>
            @foreach ($salas as $sala)
            <tr>
                <td class="fw-bold">{{ $sala->nombre }}</td>
                <td>{{ $sala->ubicacion }}</td>
                <td>{{ $sala->capacidad }}</td>
                <td><span class="badge bg-warning fs-6">{{ $sala->status }}</span></td>
                <td>{{ $sala->horario_inicio }}</td>
                <td>{{ $sala->horario_fin }}</td>
                <td>
                    <a href="{{ route('salas.show', ['sala' => $sala->id]) }}" class="btn btn-warning">Editar</a>

                    <button class="btn btn-danger" onclick="confirmDelete({{ $sala->id }})">Eliminar</button>

                    <form id="delete-form-{{ $sala->id }}" action="{{ route('salas.destroy', ['sala' => $sala->id]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás deshacer esta acción!",
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
