@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-12">
            <div>
                <h2>Crear Reserva</h2>
            </div>
        </div>

        {!! Form::open(['route' => 'reservas.store', 'method' => 'POST']) !!}
        <div class="row">
            <!-- Campo Titulo -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('titulo', 'Titulo:') !!}
                    {!! Form::text('titulo', old('titulo'), ['id' => 'titulo', 'class' => 'form-control', 'placeholder' => 'Titulo de la reserva']) !!}
                    @error('titulo')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Descripción -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('ubicacion', 'Descripción:') !!}
                    {!! Form::text('descripcion', old('descripcion'), ['id' => 'descripcion', 'class' => 'form-control', 'placeholder' => 'Descripción de la reserva']) !!}
                    @error('descripcion')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Tipo de evento -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('tipoEvento', 'Tipo de evento:') !!}
                    {!! Form::select('tipoEvento', ['' => '-- Elige un evento --', 'Reunión' => 'Reunión', 'Charla' => 'Charla', 'Curso' => 'Curso'], old('tipoEvento'), ['id' => 'tipoEvento', 'class' => 'form-select']) !!}
                    @error('tipoEvento')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Horario -->
            <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
                <div class="form-group">
                    {!! Form::label('horario', 'Horario:') !!}
                    {!! Form::date('horario', old('horario'), ['id' => 'horario', 'class' => 'form-control']) !!}

                    @error('horario')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 mt-2">
                <div class="form-group">
                    {!! Form::label('horario', 'Horario:') !!}
                    {!! Form::time('horario', old('horario'), ['id' => 'horario', 'class' => 'form-control']) !!}

                    @error('horario')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Selección de sala -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('fk_idSala', 'Elija la Sala:') !!}
                    {!! Form::select('fk_idSala', $salas, old('fk_idSala'), ['id' => 'fk_idSala', 'class' => 'form-select','placeholder'=>'--Seleccione--']) !!}
                    @error('fk_idSala')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Selección de Participantes -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('fk_participantes', 'Elija los participantes:') !!}
                    {!! Form::select('fk_participantes', $usuarios, old('fk_participantes'), ['id' => 'fk_participantes', 'class' => 'form-select','name'=>'participantes[]', 'multiple'=>'multiple']) !!}
                    @error('fk_participantes')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                {!! Form::submit('Crear', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @section('js')
        <script>
            $(document).ready(function() {
                $('#fk_participantes').select2({
                    placeholder: "--Seleccione--",
                    allowClear: true
                });
            });

        </script>
    @endsection
@endsection
