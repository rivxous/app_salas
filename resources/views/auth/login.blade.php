@extends('layouts.base')

@section('content')

    <div class="card p-4 bg-light">
        <form class="" method="POST" action="{{ route('login.post') }}">
             @csrf
            <div>
                <h1 class=" p-2 text-center">Login</h1>

            </div>
            <div class="form-group text-left">
                <label for="usuario">Usuario:</label>
                <input type="email" name="email" class="form-control" placeholder="Ingrese su usuario" required>
            </div>

            <div class="form-group text-left">
                <label for="clave">Contraseña:</label>
                <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña"
                       required>
            </div>

            <div class="form-group text-center p-2">
                <button class="btn btn-danger" type="submit">Iniciar sesión</button>

            </div>
        </form>

    </div>

@endsection
