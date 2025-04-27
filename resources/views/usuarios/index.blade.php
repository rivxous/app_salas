@extends('layouts.base')

@section('title', 'Listado de Usuarios')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Listado de Usuarios</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        @if (Auth::user()->rol == 'admin')
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" onclick="confirmSync()">Sincronizar Usuarios</button>
            </div>
        @endif


        <form id="sync-form" action="{{ route('users.sync') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <div class="table-responsive">
            <table class="table table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Unidad Funcional</th>
                        <th>Email</th>
                        @if (Auth::user()->rol == 'admin')
                            <th>Acciones</th>
                        @endif

                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="fw-bold text-uppercase">{{ $user->username }}</td>
                            <td>{{ $user->nombre }}</td>
                            <td>{{ $user->apellido }}</td>
                            <td>{{ $user->unidad_funcinal }}</td>
                            <td>{{ $user->email }}</td>
                            @if (Auth::user()->rol == 'admin')
                                <td>
                                    @if ($user->id)
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $user->id }})">Eliminar</button>

                                        <form id="delete-form-{{ $user->id }}"
                                            action="{{ route('users.destroy', ['id' => $user->id]) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        <span class="text-muted">LDAP</span>
                                    @endif
                                </td>
                            @endif

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

        function confirmSync() {
            Swal.fire({
                title: '¿Sincronizar usuarios?',
                text: "Esta acción actualizará la lista de usuarios desde el servidor LDAP.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, sincronizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('sync-form').submit();
                }
            });
        }
    </script>
@endsection
