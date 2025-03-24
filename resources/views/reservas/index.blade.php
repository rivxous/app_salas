@extends('layouts.base')

@section('content')
<div class="row">
    <div class="col-12">
        <div>
            <h2 class="text-white">Listado de reservas QQGAS</h2>
        </div>
        <div>
            <a href="{{ route('reservas.create') }}" class="btn btn-primary">Crear reserva</a>
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
        <table class="table table-bordered text-white">
            <tr class="text-secondary">
                <th>TITULO</th>
                <th>DESCRIPCIÓN</th>
                <th>TIPO DE EVENTO </th>
                <th>HORARIO</th>
                <th>SALA</th>
                <th>ACCIONES</th>
            </tr>
            @foreach ($reservas as $reserva)
            <tr>
                <td class="fw-bold">{{ $reserva->nombre }}</td>
                <td>{{ $reserva->ubicacion }}</td>
                <td>{{ $reserva->capacidad }}</td>
                <td><span class="badge bg-warning fs-6">{{ $reserva->status }}</span></td>
                <td>{{ $reserva->horario_inicio }}</td>
                <td>{{ $reserva->horario_fin }}</td>
                <td>
                    <a href="{{ route('reservas.show', ['reserva' => $reserva->id]) }}" class="btn btn-warning">Editar</a>

                    <button class="btn btn-danger" onclick="confirmDelete({{ $reserva->id }})">Eliminar</button>

                    <form id="delete-form-{{ $reserva->id }}" action="{{ route('reservas.destroy', ['reserva' => $reserva->id]) }}" method="POST" style="display: none;">
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
