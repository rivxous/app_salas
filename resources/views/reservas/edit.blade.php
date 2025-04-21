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
                        <div class="mb-3" id="salaContainer" style="display: block;">
                            {!! Form::label('fk_idSala', 'Sala', ['class' => 'form-label fw-bold']) !!}
                            <select id="fk_idSala" name="fk_idSala" class="form-select" required>
                                <option value="">-- Seleccione una sala --</option>
                                @foreach($salas as $sala)
                                    <option value="{{ $sala->id }}"
                                            {{ $reserva->fk_idSala == $sala->id ? 'selected' : '' }}
                                            data-horario-inicio="{{ $sala->horario_inicio }}"
                                            data-horario-fin="{{ $sala->horario_fin }}"
                                            data-status="{{ $sala->status }}">
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
                            <!-- Horario permitido -->
                            <div id="horarioInfo" class="alert alert-info mb-3">
                                Horario permitido:
                                <span id="horarioPermitido">
                                    {{ $reserva->sala->horario_inicio }} - {{ $reserva->sala->horario_fin }}
                                </span>
                                <span class="badge bg-success ms-2">{{ $reserva->sala->status }}</span>
                            </div>

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
                                    <button type="button" id="agregarFecha" class="btn btn-sm btn-success" style="display: none;">
                                        <i class="bi bi-plus-circle"></i> Agregar fecha
                                    </button>
                                </div>

                                <div id="horarioValidation" class="d-none mt-2"></div>

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
                                                    'required' => true,
                                                    'min' => $reserva->sala->horario_inicio,
                                                    'max' => $reserva->sala->horario_fin
                                                ]) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! Form::label('horas_fin[]', 'Hora de Fin', ['class' => 'form-label']) !!}
                                                {!! Form::time('horas_fin[]', \Carbon\Carbon::parse($horario->hora_fin)->format('H:i'), [
                                                    'class' => 'form-control',
                                                    'required' => true,
                                                    'min' => $reserva->sala->horario_inicio,
                                                    'max' => $reserva->sala->horario_fin
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
            // 1. Configuración inicial
            const salas = @json($salas);
            let currentHorario = {
                inicio: '{{ $reserva->sala->horario_inicio }}',
                fin: '{{ $reserva->sala->horario_fin }}'
            };
            let salaSeleccionada = null;

            // 2. Elementos del DOM
            const $form = $('#reservaForm');
            const $ubicacion = $('#ubicacion');
            const $sala = $('#fk_idSala');
            const $camposReserva = $('#camposReserva');
            const $horarioInfo = $('#horarioInfo');
            const $fechasContainer = $('#fechasContainer');
            const $agregarFecha = $('#agregarFecha');
            const $participantesSelect = $('#fk_participantes');

            // Inicializar Select2 para participantes
            $participantesSelect.select2({
                placeholder: "Seleccione participantes",
                allowClear: true
            });

            // 3. Evento cambio de ubicación
            $ubicacion.on('change', function() {
                const ubicacion = $(this).val();

                // Resetear sala y formulario
                $sala.val('').trigger('change');
                $sala.empty().append('<option value="">-- Seleccione una sala --</option>');

                if (ubicacion) {
                    // Filtrar y poblar salas
                    const salasFiltradas = salas.filter(s => s.ubicacion === ubicacion);
                    salasFiltradas.forEach(s => {
                        $sala.append(new Option(s.nombre, s.id, false, false));
                    });

                    $('#salaContainer').show(300);
                } else {
                    $('#salaContainer').hide(300);
                    $camposReserva.hide(300);
                }
            });

            // 4. Evento cambio de sala
            $sala.on('change', function() {
                const salaId = $(this).val();
                const $submitBtn = $('button[type="submit"]');

                if (salaId) {
                    salaSeleccionada = salas.find(s => s.id == salaId);

                    // Validar status de la sala
                    if (salaSeleccionada.status === 'Mantenimiento') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sala no disponible',
                            html: `<div class="text-center">
                            <i class="bi bi-tools fs-1"></i>
                            <p class="mt-3">Esta sala está en mantenimiento y no puede ser reservada</p>
                           </div>`,
                            confirmButtonText: 'Entendido',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            }
                        });

                        // Deshabilitar y ocultar elementos
                        $camposReserva.hide(300);
                        $horarioInfo.hide();
                        $submitBtn.prop('disabled', true);
                        $sala.addClass('is-invalid');
                        return;
                    }

                    // Resto de lógica si la sala está habilitada
                    $submitBtn.prop('disabled', false);
                    $sala.removeClass('is-invalid');

                    currentHorario = {
                        inicio: salaSeleccionada.horario_inicio,
                        fin: salaSeleccionada.horario_fin
                    };

                    $('input[type="time"]').each(function() {
                        $(this).attr({
                            'min': currentHorario.inicio,
                            'max': currentHorario.fin
                        });
                    });

                    $horarioInfo.html(`
                <i class="bi bi-clock"></i>
                Horario permitido: ${currentHorario.inicio} - ${currentHorario.fin}
                <span class="badge bg-success ms-2">${salaSeleccionada.status}</span>
            `).show();
                    $camposReserva.show(300);
                } else {
                    $camposReserva.hide(300);
                    $horarioInfo.hide();
                    $submitBtn.prop('disabled', false);
                }
            });

            // 5. Validación de horarios
            const validarHorarios = () => {
                let errores = [];
                const horarios = [];

                $('.fecha-grupo').each((index, grupo) => {
                    const $grupo = $(grupo);
                    const fecha = $grupo.find('input[name="fechas[]"]').val();
                    const inicio = $grupo.find('input[name="horas_inicio[]"]').val();
                    const fin = $grupo.find('input[name="horas_fin[]"]').val();

                    $grupo.find('.is-invalid').removeClass('is-invalid');

                    if (!fecha) errores.push(`Grupo ${index + 1}: Fecha requerida`);
                    if (!inicio) errores.push(`Grupo ${index + 1}: Hora de inicio requerida`);
                    if (!fin) errores.push(`Grupo ${index + 1}: Hora final requerida`);

                    if (inicio && fin && inicio >= fin) {
                        errores.push(`Grupo ${index + 1}: Hora final debe ser posterior a la de inicio`);
                        $grupo.find('input[name="horas_fin[]"]').addClass('is-invalid');
                    }

                    if (inicio < currentHorario.inicio) {
                        errores.push(`Grupo ${index + 1}: Hora inicio no puede ser antes de ${currentHorario.inicio}`);
                        $grupo.find('input[name="horas_inicio[]"]').addClass('is-invalid');
                    }

                    if (fin > currentHorario.fin) {
                        errores.push(`Grupo ${index + 1}: Hora final no puede ser después de ${currentHorario.fin}`);
                        $grupo.find('input[name="horas_fin[]"]').addClass('is-invalid');
                    }

                    if (fecha && inicio && fin) {
                        horarios.push({
                            grupo: index + 1,
                            fecha: fecha,
                            inicio: inicio,
                            fin: fin,
                            element: $grupo
                        });
                    }
                });

                // Validar superposición
                horarios.forEach((horario, i) => {
                    horarios.slice(i + 1).forEach(other => {
                        if (horario.fecha === other.fecha) {
                            const inicio1 = horario.inicio;
                            const fin1 = horario.fin;
                            const inicio2 = other.inicio;
                            const fin2 = other.fin;

                            if ((inicio1 >= inicio2 && inicio1 < fin2) ||
                                (fin1 > inicio2 && fin1 <= fin2) ||
                                (inicio1 <= inicio2 && fin1 >= fin2)) {

                                errores.push(`Conflicto entre Grupo ${horario.grupo} y Grupo ${other.grupo}`);
                                horario.element.find('input').addClass('is-invalid');
                                other.element.find('input').addClass('is-invalid');
                            }
                        }
                    });
                });

                const $valError = $('#horarioValidation');
                if (errores.length) {
                    $valError.html(errores.map(e => `<div class="text-danger small">${e}</div>`).join(''))
                        .removeClass('d-none');
                    return false;
                }
                $valError.addClass('d-none');
                return true;
            };

            // 6. Manejo de fechas/horarios
            $agregarFecha.on('click', () => {
                const $clone = $('.fecha-grupo:first').clone();
                $clone.find('input').val('');
                $clone.find('.eliminarFecha').prop('disabled', false);
                $fechasContainer.append($clone);
            });

            $(document).on('click', '.eliminarFecha', function() {
                if ($('.fecha-grupo').length > 1) {
                    $(this).closest('.fecha-grupo').remove();
                    validarHorarios();
                }
            });

            // 7. Evento submit
            $form.on('submit', function(e) {
                e.preventDefault();

                // Validar status de sala
                if (salaSeleccionada && salaSeleccionada.status === 'Mantenimiento') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acción no permitida',
                        text: 'No se puede reservar una sala en mantenimiento',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                // Validaciones combinadas
                const valido = this.checkValidity() && validarHorarios();

                if (!valido) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                // Confirmación final
                Swal.fire({
                    title: '¿Confirmar cambios?',
                    text: '¿Está seguro de actualizar la reserva?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            // 8. Eventos de validación en tiempo real
            $form.on('input', 'input, select', validarHorarios);
            $('#tipoEvento').on('change', function() {
                $agregarFecha.toggle($(this).val() === 'Curso');
                if ($(this).val() !== 'Curso') {
                    $('.fecha-grupo:not(:first)').remove();
                    $('.fecha-grupo:first .eliminarFecha').prop('disabled', true);
                }
            });
        });
    </script>
@endsection
