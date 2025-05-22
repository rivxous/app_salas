@extends('layouts.base')

@section('title', 'Creación de Salas')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Crear Nueva Sala</h2>
                </div>

                <div class="card-body">
                    {!! Form::open(['route' => 'salas.store', 'method' => 'POST', 'class' => 'needs-validation', 'novalidate' => true]) !!}

                    <!-- Campo Nombre -->
                    <div class="mb-3">
                        {!! Form::label('nombre', 'Nombre de la Sala', ['class' => 'form-label fw-bold']) !!}
                        {!! Form::text('nombre', old('nombre'), [
                            'class' => 'form-control' . ($errors->has('nombre') ? ' is-invalid' : ''),
                            'placeholder' => 'Ej: Sala de Conferencias A',
                            'required' => true,
                            'aria-describedby' => 'nombreHelp nombreError'
                        ]) !!}
                        <div id="nombreHelp" class="form-text">Ingrese un nombre descriptivo para la sala.</div>
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
                        ], old('ubicacion'), [
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
                            {!! Form::number('capacidad', old('capacidad'), [
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
                        ], old('status'), [
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
                            {!! Form::time('horario_inicio', old('horario_inicio'), [
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
                            {!! Form::time('horario_fin', old('horario_fin'), [
                                'class' => 'form-control' . ($errors->has('horario_fin') ? ' is-invalid' : ''),
                                'required' => true,
                                'id' => 'horario_fin',
                                'aria-describedby' => 'horarioFinError horarioValidation',
                                'onchange' => 'validarHorarios()'
                            ]) !!}
                            <div id="horarioValidation" class="text-danger small d-none">
                                <i class="bi bi-exclamation-circle"></i> La hora final debe ser posterior a la hora de
                                inicio.
                            </div>
                            @error('horario_fin')
                            <div id="horarioFinError" class="invalid-feedback">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Campo Atributos - Corregido -->
                    <div class="mb-3">
                        {!! Form::label('atributos', 'Atributos Disponibles', ['class' => 'form-label fw-bold']) !!}
                        <div class="alert alert-warning p-2">
                            <i class="bi bi-info-circle-fill"></i> Debe seleccionar al menos un atributo
                        </div>

                        <!-- Mensaje de error -->
                        @error('atributos')
                        <div class="alert alert-danger mt-2">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </div>
                        @enderror

                        <div class="row g-3" aria-describedby="atributosError">
                            @php
                                $atributosOptions = [
                                    'Proyector' => 'Proyector',
                                    'Monitor' => 'Monitor',
                                    'Aire Acondicionado' => 'Aire Acondicionado',
                                    'Wifi' => 'Wifi',
                                    'Video Conferencia' => 'Video Conferencia',
                                    'Bocinas' => 'Bocinas'
                                ];
                                $oldAtributos = old('atributos', []);
                            @endphp

                            @foreach($atributosOptions as $value => $label)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        {!! Form::checkbox(
                                            'atributos[]',
                                            $value,
                                            in_array($value, $oldAtributos),
                                            [
                                                'class' => 'form-check-input' . ($errors->has('atributos') ? ' is-invalid' : ''),
                                                'id' => 'atributo' . Str::camel($value)
                                            ]
                                        ) !!}
                                        {!! Form::label('atributo' . Str::camel($value), $label, ['class' => 'form-check-label']) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('salas.index') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        {!! Form::button('<i class="bi bi-check-circle"></i> Guardar Sala', [
                            'type' => 'submit',
                            'class' => 'btn btn-primary'
                        ]) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Variables globales para horario de la sala
        let currentHorarioInicio = '07:30';
        let currentHorarioFin = '16:30';

        function validarHorarios() {
            const inicio = document.getElementById('horario_inicio');
            const fin = document.getElementById('horario_fin');
            const mensajeError = document.getElementById('horarioValidation');
            const minTime = '07:30';
            const maxTime = '16:30';
            let isValid = true; // Variable para rastrear la validez

            let errorMessages = [];

            // Resetear estados
            inicio.classList.remove('is-invalid');
            fin.classList.remove('is-invalid');

            // Validar hora mínima
            if (inicio.value && inicio.value < minTime) {
                errorMessages.push('La hora de inicio no puede ser antes de las 7:30 AM');
                inicio.classList.add('is-invalid');
                isValid = false;
            }

            // Validar hora máxima
            if (fin.value && fin.value > maxTime) {
                errorMessages.push('La hora final no puede ser después de las 4:30 PM');
                fin.classList.add('is-invalid');
                isValid = false;
            }

            // Validar que inicio sea antes de fin
            if (inicio.value && fin.value && inicio.value >= fin.value) {
                errorMessages.push('La hora final debe ser posterior a la hora de inicio');
                fin.classList.add('is-invalid');
                isValid = false;
            }

            // Mostrar mensajes
            if (errorMessages.length > 0) {
                mensajeError.innerHTML = errorMessages.map(msg =>
                    `<i class="bi bi-exclamation-circle"></i> ${msg}`
                ).join('<br>');
                mensajeError.classList.remove('d-none');
            } else {
                mensajeError.classList.add('d-none');
            }

            return isValid; // Retornar estado de validación
        }

        // Modificar la validación del formulario
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const isHorarioValid = validarHorarios();
                    const isFormValid = form.checkValidity();

                    if (isHorarioValid && isFormValid) {
                        Swal.fire({
                            title: '¿Confirmar creación?', // Corregido texto
                            text: '¿Estás seguro de crear esta sala?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear', // Corregido texto
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        form.classList.add('was-validated');
                    }
                }, false);
            });
        })();
        // Configurar restricciones al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const timeInputs = document.querySelectorAll('input[type="time"]');
            timeInputs.forEach(input => {
                input.min = '07:30';
                input.max = '16:30';
            });

            // Validar inicialmente
            validarHorarios();
        });

        // Event listeners para validación en tiempo real
        document.getElementById('horario_inicio').addEventListener('change', validarHorarios);
        document.getElementById('horario_fin').addEventListener('change', validarHorarios);

        // Modificar la validación del formulario
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const isHorarioValid = validarHorarios();
                    const isFormValid = form.checkValidity();

                    if (isHorarioValid && isFormValid) {
                        Swal.fire({
                            title: '¿Confirmar creación?', // Corregido texto
                            text: '¿Estás seguro de crear esta sala?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear', // Corregido texto
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        form.classList.add('was-validated');
                    }
                }, false);
            });
        })();
    </script>
@endsection
