@extends('layouts.base')

@section('content')
<div class="row">
    <div class="col-12">
        <div>
            <h2>Crear Sala</h2>
        </div>
    </div>

    {!! Form::open(['route' => 'salas.store', 'method' => 'POST']) !!}
    <div class="row">
        <!-- Campo Nombre -->
        <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
            <div class="form-group">
                {!! Form::label('nombre', 'Nombre:', ['for' => 'nombre']) !!}
                {!! Form::text('nombre', old('nombre'), ['id' => 'nombre', 'class' => 'form-control', 'placeholder' => 'Nombre de la sala']) !!}
                @error('nombre')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Ubicación -->
        <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
            <div class="form-group">
                {!! Form::label('ubicacion', 'Ubicación:', ['for' => 'ubicacion']) !!}
                {!! Form::select('ubicacion', ['' => '-- Elige una ubicación --', 'CCP' => 'CCP', 'QE2' => 'QE2', 'ALMACEN' => 'ALMACEN'], old('ubicacion'), ['id' => 'ubicacion', 'class' => 'form-select']) !!} @error('ubicacion')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Capacidad -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                {!! Form::label('capacidad', 'Capacidad:', ['for' => 'capacidad']) !!}
                {!! Form::number('capacidad', old('capacidad'), ['id' => 'capacidad', 'class' => 'form-control', 'min' => '1', 'placeholder' => 'Capacidad de personas']) !!}
                @error('capacidad')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Status -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                {!! Form::label('status', 'Status:', ['for' => 'status']) !!}
                {!! Form::select('status', ['' => '-- Elige el status --', 'Habilitada' => 'Habilitada', 'Inhabilitada' => 'Inhabilitada'], old('status'), ['id' => 'status', 'class' => 'form-select']) !!}
                @error('status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Horario Inicio -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                {!! Form::label('horario_inicio', 'Hora de inicio:', ['for' => 'horario_inicio']) !!}
                {!! Form::time('horario_inicio', old('horario_inicio'), ['id' => 'horario_inicio', 'class' => 'form-control']) !!}
                @error('horario_inicio')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Horario Fin -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                {!! Form::label('horario_fin', 'Hora fin:', ['for' => 'horario_fin']) !!}
                {!! Form::time('horario_fin', old('horario_fin'), ['id' => 'horario_fin', 'class' => 'form-control']) !!}
                @error('horario_fin')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Botones -->
        <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
            {!! Form::submit('Crear', ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('salas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@endsection
