@extends('layouts.base')

@section('content')
<div class="row">
    <div class="col-12">
        <div>
            <h2>Editar Reserva '{{ $reserva->nombre }}'</h2>
        </div>
    </div>

    {!! Form::open(['route' => ['reservas.update', $reserva->id], 'method' => 'PUT']) !!}
    @csrf
    <div class="row">
        <!-- Campo Nombre -->
        <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
            <div class="form-group">
                {!! Form::label('nombre', 'Nombre:') !!}
                {!! Form::text('nombre', $reserva->nombre, ['id' => 'nombre', 'class' => 'form-control', 'placeholder' => 'Tarea', 'readonly']) !!}
                @error('nombre')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Ubicación -->
        <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
            <div class="form-group">
                {!! Form::label('ubicacion', 'Ubicación:') !!}
                {!! Form::textarea('ubicacion', $reserva->ubicacion, ['id' => 'ubicacion', 'class' => 'form-control', 'placeholder' => 'Ubicación']) !!}
                @error('ubicacion')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Capacidad -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                {!! Form::label('capacidad', 'Capacidad:') !!}
                {!! Form::number('capacidad', $reserva->capacidad, ['id' => 'capacidad', 'class' => 'form-control', 'placeholder' => 'Capacidad']) !!}
                @error('capacidad')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Status -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                {!! Form::label('status', 'Status:') !!}
                {!! Form::select('status', [null => 'Selecciona una opción', 'Habilitada' => 'Habilitada', 'Inhabilitada' => 'Inhabilitada'], $reserva->status, ['id' => 'status', 'class' => 'form-control']) !!}
                @error('status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Horario de Inicio -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                <label for="horario_inicio"><strong>Hora de inicio:</strong></label>
                <input type="time" id="horario_inicio" name="horario_inicio" value="{{ $reserva->horario_inicio }}" class="form-control">
                @error('horario_inicio')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Campo Horario Fin -->
        <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
            <div class="form-group">
                <label for="horario_fin"><strong>Hora fin:</strong></label>
                <input type="time" id="horario_fin" name="horario_fin" value="{{ $reserva->horario_fin }}" class="form-control">
                @error('horario_fin')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Botones -->
        <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
            <button type="submit" class="btn btn-primary text-right">Actualizar</button>
            <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@endsection
