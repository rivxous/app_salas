@extends('layouts.base')

@section('title', 'Edición de Salas')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">Editar Sala: {{ $sala->nombre }}</h2>
                    </div>

                    <div class="card-body">
                        {!! Form::model($sala, [
                            'route' => ['salas.update', $sala->id],
                            'method' => 'PUT',
                            'class' => 'needs-validation',
                            'novalidate' => true
                        ]) !!}

                        <!-- Campo Nombre -->
                        <div class="mb-3">
                            {!! Form::label('nombre', 'Nombre de la Sala', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::text('nombre', null, [
                                'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
                                'placeholder' => 'Ej: Sala de Conferencias A',
                                'readonly' => true,
                                'aria-describedby' => 'nombreHelp nombreError'
                            ]) !!}
                            <div id="nombreHelp" class="form-text">El nombre de la sala no puede modificarse.</div>
                            @error('nombre')
                            <div id="nombreError" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campo Ubicación -->
                        <div class="mb-3">
                            {!! Form::label('ubicacion', 'Ubicación', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select('ubicacion', [
                                '' => '-- Seleccione una ubicación --',
                                'CCP' => '(CCP) Centro Comercial Petro Petroriente',
                                'QE2' => '(QE2) Quiriquire',
                                'ALMACEN' => '(Almacén)'
                            ], null, [
                                'class' => 'form-select' . ($errors->has('ubicacion') ? ' is-invalid' : ''),
                                'required' => true,
                                'aria-describedby' => 'ubicacionError'
                            ]) !!}
                            @error('ubicacion')
                            <div id="ubicacionError" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campo Capacidad -->
                        <div class="mb-3">
                            {!! Form::label('capacidad', 'Capacidad Máxima', ['class' => 'form-label fw-bold']) !!}
                            <div class="input-group">
                                {!! Form::number('capacidad', null, [
                                    'class' => 'form-control' . ($errors->has('capacidad') ? ' is-invalid' : ''),
                                    'placeholder' => 'Ej: 20',
                                    'min' => 1,
                                    'required' => true,
                                    'aria-describedby' => 'capacidadHelp capacidadError'
                                ]) !!}
                                <span class="input-group-text">personas</span>
                            </div>
                            <div id="capacidadHelp" class="form-text">Número máximo de ocupantes permitidos.</div>
                            @error('capacidad')
                            <div id="capacidadError" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campo Status -->
                        <div class="mb-3">
                            {!! Form::label('status', 'Estado', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select('status', [
                                '' => '-- Seleccione un estado --',
                                'Habilitada' => 'Habilitada',
                                'Inhabilitada' => 'Inhabilitada',
                                'Mantenimiento' => 'En Mantenimiento'
                            ], null, [
                                'class' => 'form-select' . ($errors->has('status') ? ' is-invalid' : ''),
                                'required' => true,
                                'aria-describedby' => 'statusError'
                            ]) !!}
                            @error('status')
                            <div id="statusError" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campos de Horario -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                {!! Form::label('horario_inicio', 'Hora de Inicio', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::time('horario_inicio', $sala->horario_inicio ? \Carbon\Carbon::parse($sala->horario_inicio)->format('H:i') : null, [
                                    'class' => 'form-control' . ($errors->has('horario_inicio') ? ' is-invalid' : ''),
                                    'required' => true,
                                    'id' => 'horario_inicio',
                                    'aria-describedby' => 'horarioInicioError'
                                ]) !!}
                                @error('horario_inicio')
                                <div id="horarioInicioError" class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                {!! Form::label('horario_fin', 'Hora de Finalización', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::time('horario_fin', $sala->horario_fin ? \Carbon\Carbon::parse($sala->horario_fin)->format('H:i') : null, [
                                    'class' => 'form-control' . ($errors->has('horario_fin') ? ' is-invalid' : ''),
                                    'required' => true,
                                    'id' => 'horario_fin',
                                    'aria-describedby' => 'horarioFinError horarioValidation',
                                    'onchange' => 'validarHorario()'
                                ]) !!}
                                <div id="horarioValidation" class="text-danger small d-none">
                                    <i class="bi bi-exclamation-circle"></i> La hora final debe ser posterior a la hora de inicio.
                                </div>
                                @error('horario_fin')
                                <div id="horarioFinError" class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('salas.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            {!! Form::button('<i class="bi bi-save"></i> Actualizar Sala', [
                                'type' => 'submit',
                                'class' => 'btn btn-primary'
                            ]) !!}
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function validarHorario() {
            const inicio = document.getElementById('horario_inicio').value;
            const fin = document.getElementById('horario_fin').value;
            const mensajeError = document.getElementById('horarioValidation');

            if (inicio && fin && inicio >= fin) {
                mensajeError.classList.remove('d-none');
                document.getElementById('horario_fin').classList.add('is-invalid');
            } else {
                mensajeError.classList.add('d-none');
                document.getElementById('horario_fin').classList.remove('is-invalid');
            }
        }

        // Validación para ambos campos de horario
        document.getElementById('horario_inicio').addEventListener('change', validarHorario);
        document.getElementById('horario_fin').addEventListener('change', validarHorario);

        // Validación de Bootstrap
        (function() {
            'use strict'

            const forms = document.querySelectorAll('.needs-validation')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection
