@extends('layouts.base')

@section('title', 'Creación de Reservas')

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

                        <!-- Ubicación -->
                        <div class="mb-3">
                            {!! Form::label('ubicacion', 'Ubicación', ['class' => 'form-label fw-bold']) !!}
                            <select id="ubicacion" name="ubicacion" class="form-select" required>
                                <option value="">-- Seleccione una ubicación --</option>
                                @foreach ($salas->pluck('ubicacion')->unique() as $ubicacion)
                                    <option value="{{ $ubicacion }}"
                                        {{ old('ubicacion') == $ubicacion ? 'selected' : '' }}>
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

                        <!-- Sala (oculto inicialmente) -->
                        <div class="mb-3" id="salaContainer" style="display: none;">
                            {!! Form::label('fk_idSala', 'Sala', ['class' => 'form-label fw-bold']) !!}
                            <select id="fk_idSala" name="fk_idSala" class="form-select" required>
                                <option value="">-- Seleccione una sala --</option>
                            </select>
                            @error('fk_idSala')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Campos de reserva (ocultos inicialmente) -->
                        <div id="camposReserva" style="display: none;">
                            <div id="horarioInfo" class="alert alert-info mb-3" style="display: none;">
                                Horario permitido: <span id="horarioPermitido"></span>
                            </div>
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
                                    null,
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
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-bold">Temporalidad</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="temporalidad" id="todo_mes" value="todo_mes">
                                    <label class="form-check-label" for="todo_mes">
                                        Todo el mes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="temporalidad" id="toda_semana" value="toda_semana">
                                    <label class="form-check-label" for="toda_semana">
                                        Toda la semana
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="temporalidad" id="ultoma" value="ultoma" checked>
                                    <label class="form-check-label" for="ultoma">
                                        Últoma
                                    </label>
                                </div>
                            </div>
                            <!-- Fechas y horarios -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-bold">Fechas y Horarios</label>
                                    <button type="button" id="agregarFecha" class="btn btn-sm btn-success"
                                        style="display: none;">
                                        <i class="bi bi-plus-circle"></i> Agregar fecha
                                    </button>
                                </div>

                                <div id="fechasContainer">
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
                            @php
                                    use App\Models\UnidadFuncional;
                                    $unidad_funcional = [];
                                    foreach(UnidadFuncional::all() as $unidad)
                                    {
                                        $unidad_funcional[$unidad->nombre] = $unidad->nombre;
                                    }
                            @endphp
                            <div class="mb-3">
                                {!! Form::label('filtro_rol', 'Filtrar por unidad funcional', ['class' => 'form-label fw-bold']) !!}
                                {!! Form::select(
                                    'filtro_rol',
                                  
                                    $unidad_funcional,
                                    null,
                                    [
                                        'id' => 'filtro_rol',
                                        'class' => 'form-select',
                                        'placeholder' => 'Seleccione un rol',
                                    ],
                                ) !!}
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
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                            {!! Form::button('<i class="bi bi-save"></i> Crear Reserva', [
                                'type' => 'submit',
                                'class' => 'btn btn-primary',
                            ]) !!}
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="container-fluid shadow-lg bg-white p-3 rounded-3">
                    <h2>
                        Calendario de Reservas
                    </h2>
                    <div id="calendar" class=""></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const salas = @json($salas);
            let currentHorario = {
                inicio: '08:00',
                fin: '20:00'
            };
            let salaSeleccionada = null;

            const $form = $('#reservaForm');
            const $ubicacion = $('#ubicacion');
            const $sala = $('#fk_idSala');
            const $camposReserva = $('#camposReserva');
            const $horarioInfo = $('#horarioInfo');
            const $fechasContainer = $('#fechasContainer');
            const $agregarFecha = $('#agregarFecha');
            const $participantesSelect = $('#fk_participantes');
            const $camposPostEvento = $('#camposPostEvento');
            const $tipoEvento = $('#tipoEvento');

            $participantesSelect.select2({
                placeholder: "Seleccione participantes",
                allowClear: true
            });

            $ubicacion.on('change', function() {
                const ubicacion = $(this).val();
                $sala.val('').trigger('change');
                $sala.empty().append('<option value="">-- Seleccione una sala --</option>');

                if (ubicacion) {
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

            $sala.on('change', function() {
                const salaId = $(this).val();
                const $submitBtn = $form.find('button[type="submit"]');

                if (salaId) {
                    salaSeleccionada = salas.find(s => s.id == salaId);

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

                        $camposReserva.hide(300);
                        $horarioInfo.hide();
                        $submitBtn.prop('disabled', true);
                        $sala.addClass('is-invalid');
                        return;
                    }

                    $submitBtn.prop('disabled', false);
                    $sala.removeClass('is-invalid');
                    currentHorario = {
                        inicio: salaSeleccionada.horario_inicio,
                        fin: salaSeleccionada.horario_fin
                    };

                    $('input[type="time"]').attr({
                        'min': currentHorario.inicio,
                        'max': currentHorario.fin
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

            $tipoEvento.on('change', function() {
                const tipo = $(this).val();
                if (tipo) {
                    $camposPostEvento.show(300);
                    $agregarFecha.toggle(tipo === 'Curso');
                } else {
                    $camposPostEvento.hide(300);
                    $agregarFecha.hide();
                }
            });

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

            const validarHorarios = () => {
                let errores = [];
                const horarios = [];
                const esCurso = $('#tipoEvento').val() === 'Curso';

                $('.fecha-grupo').each((index, grupo) => {
                    const $grupo = $(grupo);
                    const fecha = $grupo.find('input[name="fechas[]"]').val();
                    const inicio = $grupo.find('input[name="horas_inicio[]"]').val();
                    const fin = $grupo.find('input[name="horas_fin[]"]').val();

                    $grupo.find('.is-invalid').removeClass('is-invalid');

                    // Validaciones básicas (mantenidas)
                    if (!fecha) errores.push(`Grupo ${index + 1}: Fecha requerida`);
                    if (!inicio) errores.push(`Grupo ${index + 1}: Hora de inicio requerida`);
                    if (!fin) errores.push(`Grupo ${index + 1}: Hora final requerida`);

                    if (inicio && fin && inicio >= fin) {
                        errores.push(
                            `Grupo ${index + 1}: Hora final debe ser posterior a la de inicio`);
                        $grupo.find('input[name="horas_fin[]"]').addClass('is-invalid');
                    }

                    if (inicio < currentHorario.inicio) {
                        errores.push(
                            `Grupo ${index + 1}: Hora inicio no puede ser antes de ${currentHorario.inicio}`
                        );
                        $grupo.find('input[name="horas_inicio[]"]').addClass('is-invalid');
                    }

                    if (fin > currentHorario.fin) {
                        errores.push(
                            `Grupo ${index + 1}: Hora final no puede ser después de ${currentHorario.fin}`
                        );
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

                // Validación específica para cursos
                if (esCurso) {
                    // 1. Validar mínimo una fecha
                    if (horarios.length === 0) {
                        errores.push('Los cursos deben tener al menos una fecha programada');
                    }

                    // 2. Validar superposición de horarios en la misma fecha
                    const horariosPorFecha = {};
                    horarios.forEach(horario => {
                        if (!horariosPorFecha[horario.fecha]) {
                            horariosPorFecha[horario.fecha] = [];
                        }
                        horariosPorFecha[horario.fecha].push(horario);
                    });

                    // Verificar conflictos por fecha
                    Object.values(horariosPorFecha).forEach(horariosFecha => {
                        horariosFecha.forEach((horario, i) => {
                            horariosFecha.slice(i + 1).forEach(other => {
                                const inicio1 = horario.inicio;
                                const fin1 = horario.fin;
                                const inicio2 = other.inicio;
                                const fin2 = other.fin;

                                if ((inicio1 >= inicio2 && inicio1 < fin2) ||
                                    (fin1 > inicio2 && fin1 <= fin2) ||
                                    (inicio1 <= inicio2 && fin1 >= fin2)) {

                                    errores.push(
                                        `Conflicto en ${horario.fecha}: Grupos ${horario.grupo} y ${other.grupo} se solapan`
                                    );
                                    horario.element.find('input').addClass(
                                        'is-invalid');
                                    other.element.find('input').addClass('is-invalid');
                                }
                            });
                        });
                    });
                }

                const $valError = $('#horarioValidation');
                if (errores.length) {
                    $valError.html(errores.map(e => `<div class="text-danger small">${e}</div>`).join(''))
                        .removeClass('d-none');
                    return false;
                }
                $valError.addClass('d-none');
                return true;
            };

            $form.on('submit', function(e) {
                e.preventDefault();

                if (salaSeleccionada && salaSeleccionada.status === 'Mantenimiento') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Acción no permitida',
                        text: 'No se puede reservar una sala en mantenimiento',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                const valido = this.checkValidity() && validarHorarios();

                if (!valido) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                Swal.fire({
                    title: '¿Confirmar reserva?',
                    text: 'Revise los datos antes de continuar',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Revisar',
                    customClass: {
                        confirmButton: 'btn btn-primary me-2',
                        cancelButton: 'btn btn-outline-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            $form.on('input', 'input, select', validarHorarios);
        });
    </script>
@endsection

@section('scripts')
    <script>
        const $horarioInfo = $('#horarioInfo');
        $(document).ready(function() {
            // Datos de las salas
            const salas = @json($salas);
            const salasPorUbicacion = {};
            document.getElementById('filtro_rol').addEventListener('change', function() {
                cargarUsuariosDesdeAPI(this.value);
            });
            function cargarUsuariosDesdeAPI(rol) {
                $.ajax({
                    url: `http://127.0.0.1:8000/api/usuario/${rol}`,
                    type: 'GET',
                    data: {
                        role: rol // Asegúrate de que tu API acepte este parámetro
                    },
                    success: function(response) {
                        // Mapear la respuesta de la API al formato que Select2 entiende
                        const usuarios = response.map(user => ({
                            id: user.id,
                            text: user.nombre // Ajusta según el campo de tu API
                        }));
                        console.log(usuarios)
                        // Actualizar el select
                        $('#fk_participantes').empty().select2({
                            data: usuarios,
                            placeholder: "Seleccione participantes",
                            allowClear: true
                        });
                       
                    },
                    error: function(xhr) {
                        console.error('Error al cargar usuarios:', xhr);
                    }
                });
            }
            cargarUsuariosDesdeAPI('admin'); // Rol por defecto
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                slotMinTime: '07:30:00',
                slotMaxTime: '18:00:00',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'es',
                timeZone: 'America/Lima',
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
                dateClick: function(info) {
                    // 'info' es un objeto que contiene información sobre el clic
                    var fechaCompleta = info.dateStr; // Esto te dará la fecha en formato 'YYYY-MM-DD'
                    var HoraInicio = fechaCompleta.split("T")[1]
                    document.querySelector(' [name="horas_inicio\\[\\]"] ').value = HoraInicio;
                    document.querySelector(' [name="fechas\\[\\]"] ').value = fechaCompleta.split("T")[
                        0];

                    console.log(fechaCompleta)
                    console.log(HoraInicio)


                    // Aquí puedes realizar las acciones que necesites con la fecha y la hora capturadas,
                    // como enviarlas a un servidor, mostrarlas en un formulario, etc.
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

                /*  Popover y demás opciones… */
                eventDidMount(info) {
                    const content = `
                        <div class="popover-content-custom">
                            <p class="mb-1"><strong>${info.event.title}</strong></p>
                            <p class="mb-1">${info.event.extendedProps.description}</p>
                            <hr class="my-1">
                            <small>Sala: ${info.event.extendedProps.sala}</small><br>
                            <small>Organizador: ${info.event.extendedProps.organizador}</small>
                        </div>`;
                    new bootstrap.Popover(info.el, {
                        title: `<i class="fas fa-calendar me-2"></i>Detalles de reserva`,
                        content,
                        trigger: 'hover',
                        placement: 'auto',
                        container: 'body',
                        html: true,
                        sanitize: false
                    });
                }
            });

            calendar.render();
            fk_idSala.addEventListener('change', async (e) => {
                try {
                    const res = await fetch("{{ route('buscar_salas_horios_disponibles') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_sala: e.target.value
                        })
                    });
                    const data = await res.json();
                    const currentHorario = data.sala
                    console.log(data);
                    if (data) {
                        $horarioInfo.html(`
                        <i class="bi bi-clock"></i>
                        Horario permitido: ${currentHorario.horario_disponibilidad} 
                        <span class="badge bg-success ms-2"></span>
                    `).show();
                    }

                    // Agrego la fuente de eventos que acaba de llegar
                    calendar.addEventSource(data.data); // o simplemente  events: data  si creas nuevo

                } catch (err) {
                    console.error(err);
                    alert('Error al cargar eventos');
                }
            });



            salas.forEach(sala => {
                if (!salasPorUbicacion[sala.ubicacion]) {
                    salasPorUbicacion[sala.ubicacion] = [];
                }
                salasPorUbicacion[sala.ubicacion].push(sala);
            });

            // Elementos del DOM
            const $ubicacionSelect = $('#ubicacion');
            const $salaSelect = $('#fk_idSala');
            const $salaContainer = $('#salaContainer');
            const $camposReserva = $('#camposReserva');
            const $participantesSelect = $('#fk_participantes');
            const $tipoEvento = $('#tipoEvento');
            const $fechasContainer = $('#fechasContainer');
            const $agregarFechaBtn = $('#agregarFecha');

            // Inicializar Select2 para participantes
            $participantesSelect.select2({
                placeholder: "Seleccione participantes",
                allowClear: true
            });

            // Cambio de ubicación
            $ubicacionSelect.on('change', function() {
                const ubicacionSeleccionada = $(this).val();
                $salaSelect.empty().append('<option value="">-- Seleccione una sala --</option>');
                $salaContainer.hide();
                $camposReserva.hide();

                if (ubicacionSeleccionada && salasPorUbicacion[ubicacionSeleccionada]) {
                    salasPorUbicacion[ubicacionSeleccionada].forEach(sala => {
                        $salaSelect.append(`<option value="${sala.id}">${sala.nombre}</option>`);
                    });
                    $salaContainer.show();
                }
            });

            // Cambio de sala
            $salaSelect.on('change', function() {
                if ($(this).val()) {
                    $camposReserva.show();
                } else {
                    $camposReserva.hide();
                }
            });

            // Cambio de tipo de evento
            $tipoEvento.on('change', function() {
                if ($(this).val() === 'Curso') {
                    $agregarFechaBtn.show();
                } else {
                    $agregarFechaBtn.hide();
                    // Eliminar todos los grupos de fecha excepto el primero
                    $fechasContainer.find('.fecha-grupo').not(':first').remove();
                    // Habilitar el botón eliminar del primer grupo
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

                // Habilitar eliminar en el primer grupo si hay más de uno
                if ($fechasContainer.find('.fecha-grupo').length > 1) {
                    $fechasContainer.find('.fecha-grupo:first .eliminarFecha').prop('disabled', false);
                }
            });

            // Eliminar fecha
            $(document).on('click', '.eliminarFecha', function() {
                $(this).closest('.fecha-grupo').remove();

                // Si solo queda un grupo, deshabilitar su botón eliminar
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
