@extends('layouts.base')
@section('title','Creación de Reservas')
@section('content')

    <div class="row border rounded p-3 mb-3">
        <div class="col-12">
            <div>
                <h2>Crear Reserva</h2>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! Form::open(['route' => 'reservas.store', 'method' => 'POST']) !!}
        <div class="row">

            <!-- Campo Selección de Ubicación -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('ubicacion', 'Elija la ubicación de la reserva:') !!}
                    <select id="ubicacion" name="ubicacion" class="form-select">
                        <option value="">--Seleccione--</option>
                        @foreach($salas->pluck('ubicacion')->unique() as $ubicacion)
                            <option value="{{ $ubicacion }}" {{ old('ubicacion') == $ubicacion ? 'selected' : '' }}>
                                {{ $ubicacion }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Selección de Sala -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group" style="display: none;">
                    {!! Form::label('fk_idSala', 'Elija la sala:') !!}
                    <select id="fk_idSala" name="fk_idSala" class="form-select">
                        <option value="">--Seleccione--</option>
                    </select>
                    @error('fk_idSala')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>


            <!-- Campos adicionales (ocultos inicialmente) -->
            <div class="campo-reserva"  style="{{ $errors->any() ? '' : 'display: none;' }}">

                <!-- Campo Tipo de evento y horario -->
                {!! Form::open(['route' => 'reservas.store']) !!}
                <div class="form-group">
                    {!! Form::label('tipoEvento', 'Tipo de Evento:') !!}
                    {!! Form::select('tipoEvento', [null => '--Seleccione--', 'Reunión' => 'Reunión', 'charla' => 'Charla', 'curso' => 'Curso'], null, ['class' => 'form-control', 'id' => 'tipoEvento', 'required']) !!}
                </div>

                <div id="fechasContainer">
                    <div class="form-row row fecha-grupo">
                        <div class="col-xs-12 col-sm-12 col-md-4 mt-2">
                            {!! Form::label('fechas[]', 'Fecha:') !!}
                            {!! Form::date('fechas[]', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 mt-2">
                            {!! Form::label('horas_inicio[]', 'Hora de Inicio:') !!}
                            {!! Form::time('horas_inicio[]', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 mt-2">
                            {!! Form::label('horas_fin[]', 'Hora de Fin:') !!}
                            {!! Form::time('horas_fin[]', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                </div>

                <button type="button" id="agregarFecha" class="btn btn-success mt-2" style="display:none;">Agregar fecha</button>

                {!! Form::close() !!}

                <!-- Contenedor de los horarios como botones -->
{{--                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">--}}
{{--                    <div class="form-group">--}}
{{--                        --}}{{-- Este input puede cambiar dinámicamente con JS para aceptar uno o varios --}}
{{--                        {!! Form::label('fechas[]', 'Fecha(s)') !!}--}}
{{--                        {!! Form::date('fechas[]', null)!!}--}}

{{--                    </div>--}}
{{--                </div>--}}


                <!-- Campo Título -->
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    <div class="form-group">
                        {!! Form::label('titulo', 'Titulo:') !!}
                        {!! Form::text('titulo', old('titulo'), ['class' => 'form-control', 'placeholder' => 'Titulo de la reserva']) !!}
                        @error('titulo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Campo Descripción -->
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    <div class="form-group">
                        {!! Form::label('descripcion', 'Descripción:') !!}
                        {!! Form::text('descripcion', old('descripcion'), ['class' => 'form-control', 'placeholder' => 'Descripción de la reserva']) !!}
                        @error('descripcion')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Campo Selección de Participantes -->
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    <div class="form-group">
                        {!! Form::label('participantes', 'Elija los participantes:') !!}
                        {!! Form::select('participantes[]', $usuarios, old('participantes'), ['id' => 'fk_participantes', 'class' => 'form-select', 'multiple' => 'multiple']) !!} <!--AGREGAR USUARIOS EN RESERVA-->
                        @error('participantes')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>


            </div>

        </div>
        {!! Form::close() !!}
    </div>
    <!-- Botones --> <!--cREAR RESERVA Y REGRESAR AL LISTADO-->
    <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
        {!! Form::submit('Crear', ['class' => 'btn btn-primary']) !!}
        <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    @section('js')
        <script>
            $(document).ready(function () {
                let salas = @json($salas);
                let horariosPorSala = {};

                salas.forEach(sala => {
                    horariosPorSala[sala.id] = sala.horarios_disponibles;
                });

                let horariosBotonesContainer = $("#horarios-botones-container");
                let camposReserva = $(".campo-reserva");
                let participantesSelect = $("#fk_participantes");

                // Función para generar los botones de horarios
                function renderizarHorariosBotones(horarios) {
                    horariosBotonesContainer.empty();

                    if (horarios.length === 0) {
                        horariosBotonesContainer.append('<p class="text-danger">No hay horarios disponibles para esta sala.</p>');
                        return;
                    }

                    horarios.forEach(horario => {
                        let boton = $(`
                    <button type="button" class="btn btn-outline-primary m-1 horario-btn" data-horario="${horario}">
                        ${horario}
                    </button>
                `);
                        horariosBotonesContainer.append(boton);
                    });

                    // Eliminar inputs anteriores
                    $("input[name='horarios[]']").remove();
                }

                // Manejo del cambio de sala
                $("#fk_idSala").change(function () {
                    let salaId = $(this).val();

                    if (salaId && horariosPorSala[salaId]) {
                        renderizarHorariosBotones(horariosPorSala[salaId]);
                        camposReserva.fadeIn();

                        participantesSelect.select2("destroy").select2({
                            placeholder: "--Seleccione--",
                            allowClear: true,
                            width: "100%"
                        });

                        participantesSelect.next(".select2-container").hide().fadeIn();
                    } else {
                        renderizarHorariosBotones([]);
                        camposReserva.fadeOut();
                        participantesSelect.next(".select2-container").fadeOut();
                    }
                });

                // Selección de horarios (cambiar estilo y guardar como input hidden)
                $(document).on('click', '.horario-btn', function () {
                    $(this).toggleClass('btn-outline-primary btn-primary');

                    // Eliminar todos los inputs antes de regenerar
                    $("input[name='horarios[]']").remove();

                    let seleccionados = [];
                    $(".horario-btn.btn-primary").each(function () {
                        seleccionados.push($(this).data("horario"));
                    });

                    seleccionados.forEach(horario => {
                        horariosBotonesContainer.append(`<input type="hidden" name="horarios[]" value="${horario}" />`);
                    });
                });

                // Inicializar select2 de participantes
                participantesSelect.select2({
                    placeholder: "--Seleccione--",
                    allowClear: true,
                    width: "100%"
                }).next(".select2-container").hide();

                // Manejar la lógica de selección de ubicación/sala como ya tenías
                let salasPorUbicacion = {};
                salas.forEach(sala => {
                    if (!salasPorUbicacion[sala.ubicacion]) {
                        salasPorUbicacion[sala.ubicacion] = [];
                    }
                    salasPorUbicacion[sala.ubicacion].push(sala);
                });

                let ubicacionSelect = $("#ubicacion");
                let salaSelect = $("#fk_idSala").parent();
                let salasDropdown = $("#fk_idSala");

                camposReserva.hide();
                salaSelect.hide();

                ubicacionSelect.change(function () {
                    let seleccionada = $(this).val();
                    salasDropdown.empty().append('<option value="">--Seleccione--</option>');

                    salaSelect.fadeOut(function () {
                        if (seleccionada && salasPorUbicacion[seleccionada]) {
                            salasPorUbicacion[seleccionada].forEach(sala => {
                                salasDropdown.append(`<option value="${sala.id}">${sala.nombre}</option>`);
                            });

                            salaSelect.fadeIn();
                        } else {
                            camposReserva.fadeOut();
                            participantesSelect.next(".select2-container").fadeOut();
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                const tipoEvento = document.getElementById('tipoEvento');
                const fechasContainer = document.getElementById('fechasContainer');
                const agregarFechaBtn = document.getElementById('agregarFecha');

                tipoEvento.addEventListener('change', function () {
                    if (this.value === 'curso') {
                        agregarFechaBtn.style.display = 'inline-block';
                    } else {
                        agregarFechaBtn.style.display = 'none';

                        // Limpiar fechas adicionales si no es curso
                        const grupos = fechasContainer.querySelectorAll('.fecha-grupo');
                        grupos.forEach((grupo, index) => {
                            if (index > 0) grupo.remove();
                        });
                    }
                });

                agregarFechaBtn.addEventListener('click', function () {
                    const grupo = document.createElement('div');
                    grupo.classList.add('form-row', 'row', 'fecha-grupo', 'mb-3');

                    grupo.innerHTML = `
        <div class="col-xs-12 col-sm-12 col-md-4 mt-2">
            <label>Fecha:</label>
            <input type="date" name="fechas[]" class="form-control" required>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 mt-2">
            <label>Hora de Inicio:</label>
            <input type="time" name="horas_inicio[]" class="form-control" required>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 mt-2">
            <label>Hora de Fin:</label>
            <input type="time" name="horas_fin[]" class="form-control" required>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-1 mt-4 d-flex align-items-end">
          <button type="button" class="btn btn-danger btn-sm eliminarFecha">Eliminar</button> <!--agregar funcion eliminar-->
        </div>
    `;

                    fechasContainer.appendChild(grupo);
                });

            });
        </script>


    @endsection

@endsection
