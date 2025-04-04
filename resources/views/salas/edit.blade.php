@extends('layouts.base')
@section('title','Edición de Salas')
@section('content')
    <div class="row">
        <div class="col-12">
            <div>
                <h2>Editar Sala '{{ $sala->nombre }}'</h2>
            </div>
        </div>

        {!! Form::model($sala, ['route' => ['salas.update', $sala->id], 'method' => 'PUT']) !!}
        @csrf
        <div class="row">
            <!-- Campo Nombre -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('nombre', 'Nombre:') !!}
                    {!! Form::text('nombre', null, ['id' => 'nombre', 'class' => 'form-control', 'placeholder' => 'Nombre de la sala', 'readonly']) !!}
                    @error('nombre')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Ubicación -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('ubicacion', 'Ubicación:') !!}
                    {!! Form::select('ubicacion', ['CCP' => 'CCP', 'QE2' => 'QE2', 'ALMACEN' => 'ALMACEN'], null, ['id' => 'ubicacion', 'class' => 'form-select', 'placeholder' => '-- Elige una ubicación --']) !!}
                    @error('ubicacion')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Capacidad -->
            <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
                <div class="form-group">
                    {!! Form::label('capacidad', 'Capacidad:') !!}
                    {!! Form::number('capacidad', null, ['id' => 'capacidad', 'class' => 'form-control', 'placeholder' => 'Capacidad']) !!}
                    @error('capacidad')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Status -->
            <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
                <div class="form-group">
                    {!! Form::label('status', 'Status:') !!}
                    {!! Form::select('status', ['Habilitada' => 'Habilitada', 'Inhabilitada' => 'Inhabilitada'], null, ['id' => 'status', 'class' => 'form-select', 'placeholder' => 'Selecciona una opción']) !!}
                    @error('status')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Horario de Inicio -->
            <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
                <div class="form-group">
                    {!! Form::label('horario_inicio', 'Hora de inicio:') !!}
                    {!! Form::time('horario_inicio', null, ['id' => 'horario_inicio', 'class' => 'form-control']) !!}
                    @error('horario_inicio')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Horario Fin -->
            <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
                <div class="form-group">
                    {!! Form::label('horario_fin', 'Hora fin:') !!}
                    {!! Form::time('horario_fin', null, ['id' => 'horario_fin', 'class' => 'form-control']) !!}
                    @error('horario_fin')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <small id="error-horario" class="text-danger" style="display: none;">La hora de fin debe ser posterior a la hora de inicio.</small>
                </div>
            </div>

            <!-- Botones -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                {!! Form::submit('Actualizar', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('salas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    @section('js')
        <script>
            document.getElementById('horario_inicio').addEventListener('change', validarHorario);
            document.getElementById('horario_fin').addEventListener('change', validarHorario);

            function validarHorario() {
                let inicio = document.getElementById('horario_inicio').value;
                let fin = document.getElementById('horario_fin').value;
                let errorMensaje = document.getElementById('error-horario');

                if (inicio && fin && inicio >= fin) {
                    errorMensaje.style.display = 'block';
                } else {
                    errorMensaje.style.display = 'none';
                }
            }
        </script>
    @endsection
    @endsection
