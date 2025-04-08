@extends('layouts.base')

@section('title', 'Inicio')

@section('content')
    <div class="container mt-4">
        <div class="text-center">
            <h2 class="text-primary">Bienvenido al Sistema</h2>
            <p class="lead">Seleccione el módulo al que desea acceder</p>
        </div>

        <div class="row mt-4">
            <!-- Módulo de Usuarios -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Usuarios</h5>
                        <p class="card-text">Visualizacion de usuarios</p>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-primary">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Salas -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Salas</h5>
                        <p class="card-text">Visualizacion de salas</p>
                        <a href="{{ route('salas.index') }}" class="btn btn-warning">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Reservas -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Reservas</h5>
                        <p class="card-text">Visualizacion de reservas acutuales.</p>
                        <a href="{{ route('reservas.index') }}" class="btn btn-success">Acceder</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
