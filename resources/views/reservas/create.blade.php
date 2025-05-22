@extends('layouts.base')

@section('title', 'Creación de Reservas')
@section('styles')
    <style>
        .disponibilidad-msg {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .disponibilidad-msg i {
            margin-right: 0.25rem;
        }

        .fc-event-custom {
            cursor: pointer;
        }

        .popover-content-custom {
            max-width: 300px;
        }

        #calendario {
            position: sticky;
            top: 20px;
        }

        #conflictos-participantes {
            transition: all 0.3s ease;
        }

        #conflictos-participantes .alert {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #conflictos-participantes ul {
            list-style-type: none;
            padding-left: 1rem;
        }

        #conflictos-participantes li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
        }

        #conflictos-participantes li:before {
            content: '⚠️';
            position: absolute;
            left: 0;
            top: 2px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">Crear Nueva Reserva</h2>
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

                        {!! Form::open([
                            'route' => 'reservas.store',
                            'method' => 'POST',
                            'id' => 'reservaForm',
                            'class' => 'needs-validation',
                            'novalidate' => true,
                        ]) !!}
                        @csrf

                        <!-- Ubicación -->
                        <div class="mb-3">
                            {!! Form::label('ubicacion', 'Ubicación', ['class' => 'form-label fw-bold']) !!}
                            <select id="ubicacion" name="ubicacion" class="form-select" required>
                                <option value="">-- Seleccione una ubicación --</option>
                                @foreach ($salas->pluck('ubicacion')->unique() as $ubicacion)
                                    <option value="{{ $ubicacion }}" {{ old('ubicacion') == $ubicacion ? 'selected' : '' }}>
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
                        <div class="mb-3" id="salaContainer" style="display: {{ old('fk_idSala') ? 'block' : 'none' }};">
                            {!! Form::label('fk_idSala', 'Sala', ['class' => 'form-label fw-bold']) !!}
                            <select id="fk_idSala" name="fk_idSala" class="form-select" required>
                                <option value="">-- Seleccione una sala --</option>
                                @if(old('ubicacion'))
                                    @foreach ($salas->where('ubicacion', old('ubicacion')) as $sala)
                                        <option value="{{ $sala->id }}" {{ old('fk_idSala') == $sala->id ? 'selected' : '' }}>
                                            {{ $sala->nombre }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('fk_idSala')
                            <div class="invalid-feedback d-block">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campos de reserva -->
                        <div id="camposReserva" style="display: {{ old('fk_idSala') ? 'block' : 'none' }};">
                            <!-- Tipo de evento -->
                            <div class="mb-3">
                                {!! Form::label('tipoEvento', 'Tipo de Evento', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::select(
                                    'tipoEvento',
                                    [
                                        '' => '-- Seleccione un tipo --',
                                        'Reunión' => 'Reunión',
                                        'Charla' => 'Charla',
                                        'Curso' => 'Curso',
                                    ],
                                    old('tipoEvento'),
                                    [
                                        'class' => 'form-select',
                                        'id' => 'tipoEvento',
                                        'required' => true,
                                    ],
                                ) !!}
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

                                <div id="fechasContainer">
                                    @if(old('fechas'))
                                        @foreach(old('fechas') as $index => $fecha)
                                            <div class="row g-3 mb-3 fecha-grupo">
                                                <div class="col-md-4">
                                                    {!! Form::label('fechas[]', 'Fecha', ['class' => 'form-label']) !!}
                                                    {!! Form::date('fechas[]', $fecha, ['class' => 'form-control', 'required' => true]) !!}
                                                </div>
                                                <div class="col-md-3">
                                                    {!! Form::label('horas_inicio[]', 'Hora de Inicio', ['class' => 'form-label']) !!}
                                                    {!! Form::time('horas_inicio[]', old('horas_inicio.'.$index), ['class' => 'form-control', 'required' => true]) !!}
                                                </div>
                                                <div class="col-md-3">
                                                    {!! Form::label('horas_fin[]', 'Hora de Fin', ['class' => 'form-label']) !!}
                                                    {!! Form::time('horas_fin[]', old('horas_fin.'.$index), ['class' => 'form-control', 'required' => true]) !!}
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-sm btn-danger eliminarFecha" {{ $loop->first ? 'disabled' : '' }}>
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row g-3 mb-3 fecha-grupo">
                                            <div class="col-md-4">
                                                {!! Form::label('fechas[]', 'Fecha', ['class' => 'form-label']) !!}
                                                {!! Form::date('fechas[]', null, ['class' => 'form-control', 'required' => true]) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! Form::label('horas_inicio[]', 'Hora de Inicio', ['class' => 'form-label']) !!}
                                                {!! Form::time('horas_inicio[]', null, ['class' => 'form-control', 'required' => true]) !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!! Form::label('horas_fin[]', 'Hora de Fin', ['class' => 'form-label']) !!}
                                                {!! Form::time('horas_fin[]', null, ['class' => 'form-control', 'required' => true]) !!}
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-danger eliminarFecha" disabled>
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Título -->
                            <div class="mb-3">
                                {!! Form::label('titulo', 'Título', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::text('titulo', old('titulo'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Título de la reserva',
                                    'required' => true,
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
                                {!! Form::textarea('descripcion', old('descripcion'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Descripción de la reserva',
                                    'rows' => 3,
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
                                {!! Form::select('participantes[]', $usuarios, old('participantes'), [
                                    'id' => 'fk_participantes',
                                    'class' => 'form-select',
                                    'multiple' => 'multiple',
                                    'style' => 'width: 100%',
                                ]) !!}
                                @error('participantes')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <!-- Contenedor para conflictos de participantes -->
                            <div id="conflictos-participantes" class="mt-3"></div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="verificarDisponibilidad" class="btn btn-info me-2">
                                <i class="bi bi-check-circle"></i> Verificar Disponibilidad
                            </button>
                            <div>
                                <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                {!! Form::button('<i class="bi bi-save"></i> Crear Reserva', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-primary',
                                ]) !!}
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="container-fluid shadow-lg bg-white p-3 rounded-3" id="calendario">
                    <h2>Calendario de Reservas</h2>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const salas = @json($salas);
            const salasPorUbicacion = {};

            salas.forEach(sala => {
                if (!salasPorUbicacion[sala.ubicacion]) {
                    salasPorUbicacion[sala.ubicacion] = [];
                }
                salasPorUbicacion[sala.ubicacion].push(sala);
            });

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                slotMinTime: '07:30:00',
                slotMaxTime: '18:00:00',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'es',
                timeZone: 'America/Caracas',
                firstDay: 1,
                navLinks: true,
                nowIndicator: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                dateClick: function (info) {
                    const fechaCompleta = info.dateStr;
                    const horaInicio = fechaCompleta.split("T")[1];
                    const fecha = fechaCompleta.split("T")[0];

                    let grupoVacio = $('.fecha-grupo').filter(function () {
                        return $(this).find('input[name="fechas[]"]').val() === '' &&
                            $(this).find('input[name="horas_inicio[]"]').val() === '' &&
                            $(this).find('input[name="horas_fin[]"]').val() === '';
                    }).first();

                    if (grupoVacio.length === 0) {
                        $('#agregarFecha').click();
                        grupoVacio = $('.fecha-grupo').last();
                    }

                    grupoVacio.find('input[name="fechas[]"]').val(fecha);
                    grupoVacio.find('input[name="horas_inicio[]"]').val(horaInicio);

                    const [hora, minutos] = horaInicio.split(':');
                    const horaFin = `${parseInt(hora) + 1}:${minutos}`;
                    grupoVacio.find('input[name="horas_fin[]"]').val(horaFin);

                    const salaId = $('#fk_idSala').val();
                    if (salaId) {
                        verificarDisponibilidadGrupo(grupoVacio);
                    }
                },
                eventClassNames: 'fc-event-custom',
                slotLabelInterval: '01:00:00',
                slotDuration: '00:30:00',
                allDaySlot: false,
                height: 'auto',
                views: {
                    timeGridWeek: {
                        dayHeaderFormat: {
                            weekday: 'short',
                            day: 'numeric'
                        }
                    }
                },
                eventDidMount: function (info) {
                    const formatoFechaHora = {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        timeZone: 'America/Caracas'
                    };

                    const horaInicio = info.event.start.toLocaleTimeString('es-VE', formatoFechaHora);
                    const horaFin = info.event.end.toLocaleTimeString('es-VE', formatoFechaHora);

                    const content = `
                        <div class="popover-content-custom">
                            <p class="mb-1"><strong>${info.event.title}</strong></p>
                            <p class="mb-1 text-primary small">⏰ ${horaInicio} - ${horaFin}</p>
                            <p class="mb-1">${info.event.extendedProps.description}</p>
                            <hr class="my-1">
                            <small>Sala: ${info.event.extendedProps.sala}</small><br>
                            <small>Organizador: ${info.event.extendedProps.organizador}</small><br>
                            <small>Participantes:<br>${info.event.extendedProps.participantes.replace(/\n/g, '<br>')}</small>
                        </div>`;

                    new bootstrap.Popover(info.el, {
                        title: `<i class="fas fa-calendar me-2"></i>Detalles de reserva`,
                        content: content,
                        trigger: 'hover',
                        placement: 'auto',
                        container: 'body',
                        html: true,
                        sanitize: false
                    });

                    const $fechaInputs = $('input[name="fechas[]"]');
                    const $horaInicioInputs = $('input[name="horas_inicio[]"]');
                    const $horaFinInputs = $('input[name="horas_fin[]"]');

                    for (let i = 0; i < $fechaInputs.length; i++) {
                        const fecha = $($fechaInputs[i]).val();
                        const horaInicio = $($horaInicioInputs[i]).val();
                        const horaFin = $($horaFinInputs[i]).val();

                        if (fecha && horaInicio && horaFin) {
                            const fechaInicio = new Date(`${fecha}T${horaInicio}`);
                            const fechaFin = new Date(`${fecha}T${horaFin}`);

                            if (info.event.start <= fechaFin && info.event.end >= fechaInicio) {
                                info.el.style.backgroundColor = '#ffc107';
                                info.el.style.borderColor = '#ffc107';
                            }
                        }
                    }
                }
            });

            calendar.render();

            const $ubicacionSelect = $('#ubicacion');
            const $salaSelect = $('#fk_idSala');
            const $salaContainer = $('#salaContainer');
            const $camposReserva = $('#camposReserva');
            const $participantesSelect = $('#fk_participantes');
            const $tipoEvento = $('#tipoEvento');
            const $fechasContainer = $('#fechasContainer');
            const $agregarFechaBtn = $('#agregarFecha');
            const $verificarDisponibilidadBtn = $('#verificarDisponibilidad');

            // Inicializar Select2 para participantes
            $participantesSelect.select2({
                placeholder: "Seleccione participantes",
                allowClear: true
            });

            function inicializarFormulario() {
                if ($('#ubicacion').val()) {
                    $('#salaContainer').show();

                    const ubicacion = $('#ubicacion').val();
                    if (ubicacion && salasPorUbicacion[ubicacion]) {
                        $('#fk_idSala').empty().append('<option value="">-- Seleccione una sala --</option>');
                        salasPorUbicacion[ubicacion].forEach(sala => {
                            const selected = sala.id == '{{ old('fk_idSala') }}' ? 'selected' : '';
                            $('#fk_idSala').append(`<option value="${sala.id}" ${selected}>${sala.nombre}</option>`);
                        });
                    }

                    if ($('#fk_idSala').val()) {
                        $('#camposReserva').show();
                        cargarReservasEnCalendario($('#fk_idSala').val());
                    }
                }

                if ($('#tipoEvento').val() === 'Curso') {
                    $('#agregarFecha').show();
                }

                if ($('#fk_participantes').val()?.length > 0) {
                    $('#fk_participantes').trigger('change');
                }

                @if(old('fechas') && count(old('fechas')) > 1)
                $('.eliminarFecha').prop('disabled', false);
                @endif
            }

            $ubicacionSelect.on('change', function () {
                const ubicacion = $(this).val();
                $salaSelect.empty().append('<option value="">-- Seleccione una sala --</option>');
                $salaContainer.hide();
                $camposReserva.hide();

                if (ubicacion && salasPorUbicacion[ubicacion]) {
                    salasPorUbicacion[ubicacion].forEach(sala => {
                        const selected = sala.id == '{{ old('fk_idSala') }}' ? 'selected' : '';
                        $salaSelect.append(`<option value="${sala.id}" ${selected}>${sala.nombre}</option>`);
                    });
                    $salaContainer.show();

                    if ($('#fk_idSala').val()) {
                        $camposReserva.show();
                        cargarReservasEnCalendario($('#fk_idSala').val());
                    }
                }
            });

            $salaSelect.on('change', function () {
                if ($(this).val()) {
                    $camposReserva.show();
                    cargarReservasEnCalendario($(this).val());
                } else {
                    $camposReserva.hide();
                    calendar.removeAllEventSources();
                }
            });

            $tipoEvento.on('change', function () {
                if ($(this).val() === 'Curso') {
                    $agregarFechaBtn.show();
                } else {
                    $agregarFechaBtn.hide();
                    $fechasContainer.find('.fecha-grupo').not(':first').remove();
                    $fechasContainer.find('.fecha-grupo:first .eliminarFecha').prop('disabled', true);
                }
            });

            $agregarFechaBtn.on('click', function () {
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

            $(document).on('click', '.eliminarFecha', function () {
                $(this).closest('.fecha-grupo').remove();

                if ($fechasContainer.find('.fecha-grupo').length === 1) {
                    $fechasContainer.find('.fecha-grupo:first .eliminarFecha').prop('disabled', true);
                }
            });

            $(document).on('change', 'input[name="horas_fin[]"]', function () {
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

                const salaId = $('#fk_idSala').val();
                const fecha = $grupo.find('input[name="fechas[]"]').val();

                if (salaId && fecha && inicio && fin && fin > inicio) {
                    verificarDisponibilidadGrupo($grupo);
                }
            });

            $(document).on('change', 'input[name="fechas[]"], input[name="horas_inicio[]"]', function () {
                const $grupo = $(this).closest('.fecha-grupo');
                const salaId = $('#fk_idSala').val();
                const fecha = $grupo.find('input[name="fechas[]"]').val();
                const horaInicio = $grupo.find('input[name="horas_inicio[]"]').val();
                const horaFin = $grupo.find('input[name="horas_fin[]"]').val();

                if (salaId && fecha && horaInicio && horaFin && horaFin > horaInicio) {
                    verificarDisponibilidadGrupo($grupo);
                }
            });

            async function verificarDisponibilidadGrupo($grupo) {
                const salaId = $('#fk_idSala').val();
                const fecha = $grupo.find('input[name="fechas[]"]').val();
                const horaInicio = $grupo.find('input[name="horas_inicio[]"]').val();
                const horaFin = $grupo.find('input[name="horas_fin[]"]').val();

                if (salaId && fecha && horaInicio && horaFin) {
                    $grupo.find('.disponibilidad-msg').remove();
                    const loadingMsg = $('<div class="disponibilidad-msg mt-1 small text-muted"><i class="bi bi-hourglass"></i> Verificando disponibilidad...</div>');
                    $grupo.append(loadingMsg);

                    const disponible = await verificarDisponibilidad(salaId, fecha, horaInicio, horaFin);
                    mostrarEstadoDisponibilidad($grupo, disponible);
                }
            }

            async function verificarDisponibilidad(salaId, fecha, horaInicio, horaFin) {
                try {
                    const response = await fetch("{{ route('buscar_salas_horarios_disponibles') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_sala: salaId,
                            fechas: fecha,
                            horas_inicio: horaInicio,
                            horas_fin: horaFin
                        })
                    });

                    if (response.status === 200) {
                        const data = await response.json();
                        return data.length === 0;
                    }
                    return true;
                } catch (error) {
                    console.error('Error al verificar disponibilidad:', error);
                    return false;
                }
            }

            function mostrarEstadoDisponibilidad($elemento, disponible) {
                $elemento.find('.disponibilidad-msg').remove();

                const mensaje = $('<div class="disponibilidad-msg mt-1 small"></div>');

                if (disponible) {
                    mensaje.html('<i class="bi bi-check-circle-fill text-success"></i> Horario disponible');
                    mensaje.addClass('text-success');
                } else {
                    mensaje.html('<i class="bi bi-exclamation-circle-fill text-danger"></i> Horario no disponible');
                    mensaje.addClass('text-danger');
                    $elemento.find('input').addClass('is-invalid');
                }

                $elemento.append(mensaje);
            }

            async function cargarReservasEnCalendario(salaId) {
                try {
                    const response = await fetch("{{ route('buscar_salas_horarios_disponibles') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({id_sala: salaId})
                    });

                    if (!response.ok) throw new Error('Error al cargar reservas');
                    const reservas = await response.json();

                    calendar.removeAllEventSources();

                    const events = reservas.flatMap(reserva => {
                        return reserva.reserva_horarios.map(horario => {
                            const fechaParts = horario.fecha.split('-');
                            const year = parseInt(fechaParts[0]);
                            const month = parseInt(fechaParts[1]) - 1;
                            const day = parseInt(fechaParts[2]);

                            const [startH, startM] = horario.hora_inicio.split(':').map(Number);
                            const startDate = new Date(year, month, day, startH, startM);

                            const [endH, endM] = horario.hora_fin.split(':').map(Number);
                            const endDate = new Date(year, month, day, endH, endM);

                            return {
                                title: reserva.titulo,
                                start: startDate,
                                end: endDate,
                                color: '#dc3545',
                                extendedProps: {
                                    description: reserva.descripcion,
                                    sala: reserva.sala.nombre,
                                    organizador: `${reserva.usuario_creador_reserva.nombre} ${reserva.usuario_creador_reserva.apellido}`,
                                    participantes: reserva.participantes_reservas
                                        .map(p => `${p.usuario.nombre} ${p.usuario.apellido}`)
                                        .join('\n') || 'Ninguno'
                                }
                            };
                        });
                    });

                    calendar.addEventSource(events);
                    calendar.refetchEvents();
                } catch (error) {
                    console.error('Error al cargar reservas:', error);
                }
            }

            $verificarDisponibilidadBtn.on('click', async function () {
                const gruposFecha = $('.fecha-grupo');
                let todosDisponibles = true;
                let algunCampoIncompleto = false;

                for (const grupo of gruposFecha) {
                    const $grupo = $(grupo);
                    const salaId = $('#fk_idSala').val();
                    const fecha = $grupo.find('input[name="fechas[]"]').val();
                    const horaInicio = $grupo.find('input[name="horas_inicio[]"]').val();
                    const horaFin = $grupo.find('input[name="horas_fin[]"]').val();

                    if (!salaId || !fecha || !horaInicio || !horaFin) {
                        algunCampoIncompleto = true;
                        continue;
                    }

                    if (horaFin <= horaInicio) {
                        $grupo.find('input[name="horas_fin[]"]').addClass('is-invalid');
                        mostrarEstadoDisponibilidad($grupo, false);
                        todosDisponibles = false;
                        continue;
                    }

                    const disponible = await verificarDisponibilidad(salaId, fecha, horaInicio, horaFin);
                    mostrarEstadoDisponibilidad($grupo, disponible);

                    if (!disponible) {
                        todosDisponibles = false;
                    }
                }

                if (algunCampoIncompleto) {
                    Swal.fire({
                        title: 'Campos incompletos',
                        text: 'Por favor complete todos los campos de fecha y hora para verificar disponibilidad',
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                if (todosDisponibles && gruposFecha.length > 0) {
                    Swal.fire({
                        title: '¡Disponible!',
                        text: 'Todos los horarios seleccionados están disponibles.',
                        icon: 'success',
                        confirmButtonText: 'Entendido'
                    });
                } else if (!todosDisponibles) {
                    Swal.fire({
                        title: 'Horarios no disponibles',
                        text: 'Uno o más horarios seleccionados no están disponibles. Por favor, elija otros horarios.',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                }
            });

            (function () {
                'use strict'
                const form = document.getElementById('reservaForm');

                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const gruposFecha = $('.fecha-grupo');
                    let todosDisponibles = true;
                    let algunCampoIncompleto = false;

                    for (const grupo of gruposFecha) {
                        const $grupo = $(grupo);
                        const salaId = $('#fk_idSala').val();
                        const fecha = $grupo.find('input[name="fechas[]"]').val();
                        const horaInicio = $grupo.find('input[name="horas_inicio[]"]').val();
                        const horaFin = $grupo.find('input[name="horas_fin[]"]').val();

                        if (!salaId || !fecha || !horaInicio || !horaFin) {
                            algunCampoIncompleto = true;
                            $grupo.find('input').addClass('is-invalid');
                            continue;
                        }

                        if (horaFin <= horaInicio) {
                            $grupo.find('input[name="horas_fin[]"]').addClass('is-invalid');
                            mostrarEstadoDisponibilidad($grupo, false);
                            todosDisponibles = false;
                            continue;
                        }

                        const disponible = await verificarDisponibilidad(salaId, fecha, horaInicio, horaFin);
                        if (!disponible) {
                            $grupo.find('input').addClass('is-invalid');
                            mostrarEstadoDisponibilidad($grupo, false);
                            todosDisponibles = false;
                        }
                    }

                    if (!form.checkValidity() || algunCampoIncompleto || !todosDisponibles) {
                        if (algunCampoIncompleto) {
                            Swal.fire({
                                title: 'Campos incompletos',
                                text: 'Por favor complete todos los campos requeridos',
                                icon: 'error',
                                confirmButtonText: 'Entendido'
                            });
                        } else if (!todosDisponibles) {
                            Swal.fire({
                                title: 'Horarios no disponibles',
                                text: 'Uno o más horarios seleccionados no están disponibles. Por favor, elija otros horarios.',
                                icon: 'error',
                                confirmButtonText: 'Entendido'
                            });
                        }

                        form.classList.add('was-validated');
                        return;
                    }

                    form.submit();
                }, false);
            })();

            function configurarValidacionParticipantes() {
                const $participantes = $('#fk_participantes');
                const $contenedorFechas = $('#fechasContainer');

                const validarParticipantes = async function() {
                    const participantes = $participantes.val();
                    const grupos = $contenedorFechas.find('.fecha-grupo');
                    const conflictosAcumulados = [];

                    for (const grupo of grupos) {
                        const $grupo = $(grupo);
                        const fecha = $grupo.find('input[name="fechas[]"]').val();
                        const horaInicio = $grupo.find('input[name="horas_inicio[]"]').val();
                        const horaFin = $grupo.find('input[name="horas_fin[]"]').val();

                        if (fecha && horaInicio && horaFin && participantes && participantes.length > 0) {
                            try {
                                const response = await fetch("{{ route('verificar.participantes') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        fecha: fecha,
                                        hora_inicio: horaInicio,
                                        hora_fin: horaFin,
                                        participantes: participantes
                                    })
                                });

                                const data = await response.json();
                                if (data.conflictos.length > 0) {
                                    conflictosAcumulados.push({
                                        fecha: fecha,
                                        horario: `${horaInicio} - ${horaFin}`,
                                        conflictos: data.conflictos
                                    });
                                }
                            } catch (error) {
                                console.error('Error:', error);
                            }
                        }
                    }

                    mostrarConflictosParticipantes(conflictosAcumulados);
                };

                $participantes.on('change', validarParticipantes);
                $contenedorFechas.on('change', 'input[name="fechas[]"], input[name="horas_inicio[]"], input[name="horas_fin[]"]', validarParticipantes);
            }

            function mostrarConflictosParticipantes(conflictosAgrupados) {
                const $container = $('#conflictos-participantes');
                $container.empty();

                if (conflictosAgrupados.length > 0) {
                    let html = '<div class="alert alert-warning mt-3">';
                    html += '<h5 class="alert-heading">🚨 Conflictos de horario detectados:</h5>';

                    conflictosAgrupados.forEach(grupo => {
                        const fechaFormateada = formatearFecha(grupo.fecha);

                        html += `<div class="mb-2">
                                <strong>${fechaFormateada}</strong>
                                <ul class="mb-1">`;

                        grupo.conflictos.forEach(conflicto => {
                            const inicio = formatearHora(conflicto.hora_inicio_iso);
                            const fin = formatearHora(conflicto.hora_fin_iso);

                            html += `<li>
                                        <span class="text-danger">${conflicto.usuario}</span>
                                        en ${conflicto.sala}: ${conflicto.titulo}
                                        (${conflicto.hora_inicio} - ${conflicto.hora_fin})
                                    </li>`;
                        });

                        html += '</ul></div>';
                    });

                    html += '</div>';
                    $container.html(html);
                }
            }

            function formatearFecha(fechaISO) {
                const fecha = new Date(fechaISO + 'T12:00:00Z');
                const opciones = {
                    timeZone: 'America/Caracas',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                };

                return fecha.toLocaleDateString('es-VE', opciones);
            }

            function formatearHora(fechaISO) {
                const fecha = new Date(fechaISO);
                const opciones = {
                    timeZone: 'America/Caracas',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                };

                return fecha.toLocaleTimeString('es-VE', opciones)
                    .replace(/\./g, '')
                    .toUpperCase();
            }

            configurarValidacionParticipantes();
            inicializarFormulario();

            if ($('#fk_idSala').val()) {
                cargarReservasEnCalendario($('#fk_idSala').val());
            }
        });
    </script>
@endsection
