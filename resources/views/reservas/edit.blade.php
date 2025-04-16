@extends('layouts.base')

@section('title', 'Editar Reserva')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">Editar Reserva: {{ $reserva->titulo }}</h2>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {!! Form::model($reserva, ['route' => ['reservas.update', $reserva->id], 'method' => 'PUT', 'id' => 'reservaForm', 'class' => 'needs-validation', 'novalidate' => true]) !!}

                        <!-- Ubicación -->
                        <div class="mb-3">
                            {!! Form::label('ubicacion', 'Ubicación', ['class' => 'form-label fw-bold']) !!}
                            <select id="ubicacion" name="ubicacion" class="form-select" required>
                                <option value="">-- Seleccione una ubicación --</option>
                                @foreach($salas->pluck('ubicacion')->unique() as $ubicacion)
                                    <option value="{{ $ubicacion }}" {{ $reserva->sala->ubicacion == $ubicacion ? 'selected' : '' }}>
                                        {{ $ubicacion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ubicacion')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Sala -->
                        <div class="mb-3" id="salaContainer">
                            {!! Form::label('fk_idSala', 'Sala', ['class' => 'form-label fw-bold']) !!}
                            <select id="fk_idSala" name="fk_idSala" class="form-select" required>
                                <option value="">-- Seleccione una sala --</option>
                                @foreach($salas as $sala)
                                    <option value="{{ $sala->id }}" {{ $reserva->fk_idSala == $sala->id ? 'selected' : '' }}>
                                        {{ $sala->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fk_idSala')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campos de reserva -->
                        <div id="camposReserva">

                            <!-- Tipo de evento -->
                            <div class="mb-3">
                                {!! Form::label('tipoEvento', 'Tipo de Evento', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::select('tipoEvento', [
                                    '' => '-- Seleccione un tipo --',
                                    'Reunión' => 'Reunión',
                                    'Charla' => 'Charla',
                                    'Curso' => 'Curso'
                                ], $reserva->tipoEvento, [
                                    'class' => 'form-select',
                                    'id' => 'tipoEvento',
                                    'required' => true
                                ]) !!}
                                @error('tipoEvento')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Fechas y horarios -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-bold">Fechas y Horarios</label>
                                    <button type="button" id="agregarFecha" class="btn btn-sm btn-success" style="{{ $reserva->tipoEvento === 'Curso' ? '' : 'display: none;' }}">
                                        <i class="bi bi-plus-circle"></i> Agregar fecha
                                    </button>
                                </div>

                                <div id="fechasContainer">
                                    @foreach($reserva->reserva_horarios as $index => $horario)
                                        <div class="row g-3 mb-3 fecha-grupo">
                                            <div class="col-md-4">
                                                {!! Form::label('fechas[]', 'Fecha', ['class' => 'form-label']) !!}
                                                {!! Form::date('fechas[]', \Carbon\Carbon::parse($horario->fecha), [
                                                    'class' => 'form-control',
                                                    'required' => true
                                                ]) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! Form::label('horas_inicio[]', 'Hora de Inicio', ['class' => 'form-label']) !!}
                                                {!! Form::time('horas_inicio[]', \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i'), [
                                                    'class' => 'form-control',
                                                    'required' => true
                                                ]) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! Form::label('horas_fin[]', 'Hora de Fin', ['class' => 'form-label']) !!}
                                                {!! Form::time('horas_fin[]', \Carbon\Carbon::parse($horario->hora_fin)->format('H:i'), [
                                                    'class' => 'form-control',
                                                    'required' => true
                                                ]) !!}
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-danger eliminarFecha" {{ $loop->first && count($reserva->reserva_horarios) === 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Título -->
                            <div class="mb-3">
                                {!! Form::label('titulo', 'Título', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::text('titulo', old('titulo', $reserva->titulo), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Título de la reserva',
                                    'required' => true
                                ]) !!}
                                @error('titulo')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                {!! Form::label('descripcion', 'Descripción', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::textarea('descripcion', old('descripcion', $reserva->descripcion), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Descripción de la reserva',
                                    'rows' => 3
                                ]) !!}
                                @error('descripcion')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Participantes -->
                            <div class="mb-3">
                                {!! Form::label('participantes', 'Participantes', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::select('participantes[]', $usuarios, $reserva->participantes_reservas->pluck('fk_idUsuario')->toArray(), [
                                    'id' => 'fk_participantes',
                                    'class' => 'form-select',
                                    'multiple' => 'multiple',
                                    'style' => 'width: 100%'
                                ]) !!}
                                @error('participantes')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                            {!! Form::button('<i class="bi bi-save"></i> Actualizar Reserva', [
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
        $(document).ready(function() {
            // Datos de las salas
            const salas = @json($salas);
            const salasPorUbicacion = {};

            salas.forEach(sala => {
                if (!salasPorUbicacion[sala.ubicacion]) {
                    salasPorUbicacion[sala.ubicacion] = [];
                }
                salasPorUbicacion[sala.ubicacion].push(sala);
            });

            // Elementos del DOM
            const $ubicacionSelect = $('#ubicacion');
            const $salaSelect = $('#fk_idSala');
            const $participantesSelect = $('#fk_participantes');
            const $tipoEvento = $('#tipoEvento');
            const $fechasContainer = $('#fechasContainer');
            const $agregarFechaBtn = $('#agregarFecha');

            // Inicializar Select2 para participantes
            $participantesSelect.select2({
                placeholder: "Seleccione participantes",
                allowClear: true
            });

            // Cambio de tipo de evento
            $tipoEvento.on('change', function() {
                if ($(this).val() === 'Curso') {
                    $agregarFechaBtn.show();
                } else {
                    $agregarFechaBtn.hide();
                    $fechasContainer.find('.fecha-grupo').not(':first').remove();
                    $fechasContainer.find('.fecha-grupo:first .eliminarFecha').prop('disabled', true);
                }
            });

            // Agregar nueva fecha
            $agregarFechaBtn.on('click', function() {
                const nuevoGrupo = $(`
                    <div class="row g-3 mb-3 fecha-grupo">
                        <div class="col-md-4">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fechas[]" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hora de Inicio</label>
                            <input type="time" name="horas_inicio[]" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" name="horas_fin[]" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger eliminarFecha">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                `);
                $fechasContainer.append(nuevoGrupo);

                if ($fechasContainer.find('.fecha-grupo').length > 1) {
                    $fechasContainer.find('.fecha-grupo:first .eliminarFecha').prop('disabled', false);
                }
            });

            // Eliminar fecha
            $(document).on('click', '.eliminarFecha', function() {
                $(this).closest('.fecha-grupo').remove();
                if ($fechasContainer.find('.fecha-grupo').length === 1) {
                    $fechasContainer.find('.fecha-grupo:first .eliminarFecha').prop('disabled', true);
                }
            });

            // Validación de horas
            $(document).on('change', 'input[name="horas_fin[]"]', function() {
                const $grupo = $(this).closest('.fecha-grupo');
                const inicio = $grupo.find('input[name="horas_inicio[]"]').val();
                const fin = $(this).val();

                if (inicio && fin && fin <= inicio) {
                    Swal.fire({
                        title: 'Error',
                        text: 'La hora de fin debe ser posterior a la hora de inicio',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    $(this).val('');
                }
            });

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
        });
    </script>
@endsection
