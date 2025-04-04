@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-12">
            <div>
                <h2 class="theme-global">Listado de Usuarios</h2>
            </div>
        </div>

        <div class="col-12 mt-4">
            <table class="table table-bordered theme-global">
                <tr class="text-secondary">

                    <th>USUARIO</th>
                    <th>NOMBRES</th>
                    <th>APELLIDOS</th>
                    <th>UNIDAD FUNCIONAL</th>
                    <th>E-MIAL</th>
                    <th>ACCIONES</th>
                </tr>
                @foreach ($users as $user)
                    <tr>

                        <td class="fw-bold">{{ $user->username }}</td>
                        <td>{{ $user->nombre }}</td>
                        <td>{{ $user->apellido }}</td>
                        <td>{{ $user->unidad_funcinal }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            {{--                            <a href="{{ route('users.show', ['user' => $user->id]) }}" class="btn btn-warning">Editar</a>--}}

                            <button class="btn btn-danger" onclick="confirmDelete({{ $user->id }})">Eliminar</button>

                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', ['id' => $user->id]) }}" method="POST" style="display: none;">
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
