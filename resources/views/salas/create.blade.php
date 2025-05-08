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
                                'onchange' => 'validarHorario()'
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
        let currentHorarioInicio = '08:00';
        let currentHorarioFin = '20:00';

        function validarHorarios() {
            const mensajeError = document.getElementById('horarioValidation');
            const gruposFecha = document.querySelectorAll('.fecha-grupo');
            let errorMessages = [];

            // Restablecer estados
            gruposFecha.forEach(grupo => {
                grupo.querySelectorAll('input').forEach(input => {
                    input.classList.remove('is-invalid');
                });
            });

            // Validar cada grupo de fecha/hora
            gruposFecha.forEach((grupo, index) => {
                const inicio = grupo.querySelector('input[name="horas_inicio[]"]');
                const fin = grupo.querySelector('input[name="horas_fin[]"]');
                const fecha = grupo.querySelector('input[name="fechas[]"]');

                // Validar horario sala
                if (inicio.value && inicio.value < currentHorarioInicio) {
                    errorMessages.push(`Grupo ${index + 1}: Hora de inicio no puede ser antes de ${currentHorarioInicio}`);
                    inicio.classList.add('is-invalid');
                }

                if (fin.value && fin.value > currentHorarioFin) {
                    errorMessages.push(`Grupo ${index + 1}: Hora final no puede ser después de ${currentHorarioFin}`);
                    fin.classList.add('is-invalid');
                }

                // Validar orden
                if (inicio.value && fin.value && inicio.value >= fin.value) {
                    errorMessages.push(`Grupo ${index + 1}: Hora final debe ser posterior a la de inicio`);
                    fin.classList.add('is-invalid');
                }

                // Validar fecha requerida
                if (!fecha.value) {
                    errorMessages.push(`Grupo ${index + 1}: Fecha es requerida`);
                    fecha.classList.add('is-invalid');
                }
            });

            // Mostrar errores
            if (errorMessages.length > 0) {
                mensajeError.innerHTML = errorMessages.map(msg =>
                    `<div class="text-danger small"><i class="bi bi-x-circle"></i> ${msg}</div>`
                ).join('');
                mensajeError.classList.remove('d-none');
                return false;
            }

            mensajeError.classList.add('d-none');
            return true;
        }

        // Actualizar horarios al seleccionar sala
        function actualizarHorarioSala(horarioInicio, horarioFin) {
            currentHorarioInicio = horarioInicio;
            currentHorarioFin = horarioFin;

            // Actualizar restricciones en inputs
            document.querySelectorAll('input[type="time"]').forEach(input => {
                input.min = horarioInicio;
                input.max = horarioFin;
            });
        }

        // Configurar validación en tiempo real
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name="horas_inicio[]"], input[name="horas_fin[]"]')) {
                validarHorarios();
            }
        });

        // Validación de formulario
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const esValido = validarHorarios() && form.checkValidity();

                    if (esValido) {
                        // Mostrar confirmación con SweetAlert
                        Swal.fire({
                            title: '¿Confirmar reserva?',
                            text: '¿Estás seguro de crear esta reserva?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, reservar',
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
