@extends('layouts.base')
@section('title','Creación de Reservas')
@section('content')

    <div class="row">
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
                    {!! Form::label('ubicacion', 'Elija la Ubicación de la reserva:') !!}
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
                    {!! Form::label('fk_idSala', 'Elija la Sala:') !!}
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
                <!-- Campo Tipo de evento -->
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    <div class="form-group">
                        {!! Form::label('tipoEvento', 'Tipo de evento:') !!}
                        {!! Form::select('tipoEvento', ['' => '-- Elige un evento --', 'Reunión' => 'Reunión', 'Charla' => 'Charla', 'Curso' => 'Curso'], old('tipoEvento'), ['class' => 'form-select']) !!} <!--SELECCION-->
                        @error('tipoEvento')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Contenedor de los horarios como botones -->
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2 campo-reserva">
                    <div class="form-group">
                        {!! Form::label('horario', 'Horarios disponibles:') !!}
                        <div id="horarios-botones-container" class="mt-2"></div>
                        <small class="text-muted">Haz clic en un horario para seleccionarlo. Puedes seleccionar varios.</small>
                    </div>
                </div>


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

                <!-- Botones --> <!--cREAR RESERVA Y REGRESAR AL LISTADO-->
                <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                    {!! Form::submit('Crear', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
                </div>
            </div>

        </div>
        {!! Form::close() !!}
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
        </script>


    @endsection

@endsection
