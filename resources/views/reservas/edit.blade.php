@extends('layouts.base')
@section('title','Editar Reserva')
@section('content')

    <div class="row">
        <div class="col-12">
            <div>
                <h2>Editar Reserva</h2>
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

        {!! Form::open(['route' => ['reservas.update', $reserva->id], 'method' => 'PUT']) !!}
        <div class="row">

            <!-- Campo Selección de Ubicación -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('ubicacion', 'Elija la Ubicación de la reserva:') !!}
                    <select id="ubicacion" name="ubicacion" class="form-select">
                        <option value="">--Seleccione--</option>
                        @foreach($salas->pluck('ubicacion')->unique() as $ubicacion)
                            <option value="{{ $ubicacion }}" {{ old('ubicacion', $reserva->sala->ubicacion) == $ubicacion ? 'selected' : '' }}>
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
                <div class="form-group">
                    {!! Form::label('fk_idSala', 'Elija la Sala:') !!}
                    <select id="fk_idSala" name="fk_idSala" class="form-select">
                        <option value="">--Seleccione--</option>
                        @foreach ($salas->where('ubicacion', old('ubicacion', $reserva->sala->ubicacion)) as $sala)
                            <option value="{{ $sala->id }}" {{ $sala->id == old('fk_idSala', $reserva->sala->id) ? 'selected' : '' }}>
                                {{ $sala->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('fk_idSala')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Contenedor de los horarios -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">

                <div class="form-group">
                    {!! Form::label('horarios', 'Horarios reservados:') !!}
                    <div id="horarios-container">
                        @foreach ($reserva->reserva_horarios as $horario)
                            <div class="horario-item">
                                {!! Form::input('datetime-local', 'horarios[]', $horario->horario, ['class' => 'form-control']) !!}
                                <button type="button" class="btn btn-danger btn-sm remove-horario">Eliminar</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-horario" class="btn btn-success btn-sm mt-2">Agregar Horario</button>
                </div>
            </div>
            <!-- Campo Tipo de evento -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('tipoEvento', 'Tipo de evento:') !!}
                    {!! Form::select('tipoEvento', ['Reunión' => 'Reunión', 'Charla' => 'Charla', 'Curso' => 'Curso'], old('tipoEvento', $reserva->tipoEvento), ['class' => 'form-select']) !!}
                    @error('tipoEvento')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Campo Título -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('titulo', 'Título de la reserva:') !!}
                    {!! Form::text('titulo', $reserva->titulo, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Campo Descripción -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('descripcion', 'Descripción:') !!}
                    {!! Form::text('descripcion', $reserva->descripcion, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Campo Participantes -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('participantes', 'Elija los participantes:') !!}
                    {!! Form::select('participantes[]', $usuarios, $reserva->participantes_reservas->pluck('usuario.id'), ['id' => 'fk_participantes', 'class' => 'form-select', 'multiple' => 'multiple']) !!}
                </div>
            </div>

            <!-- Botones -->
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2">
                {!! Form::submit('Actualizar Reserva', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    @section('js')
        <script>
            $(document).ready(function () {
                let salas = @json($salas);
                let salasPorUbicacion = {};

                salas.forEach(sala => {
                    if (!salasPorUbicacion[sala.ubicacion]) {
                        salasPorUbicacion[sala.ubicacion] = [];
                    }
                    salasPorUbicacion[sala.ubicacion].push(sala);
                });

                let ubicacionSelect = $("#ubicacion");
                let salasDropdown = $("#fk_idSala");

                // Función para cargar las salas al inicio si hay una ubicación ya seleccionada
                function cargarSalasIniciales() {
                    let seleccionada = ubicacionSelect.val();
                    salasDropdown.empty().append('<option value="">--Seleccione--</option>');

                    if (seleccionada && salasPorUbicacion[seleccionada]) {
                        salasPorUbicacion[seleccionada].forEach(sala => {
                            salasDropdown.append(`<option value="${sala.id}" ${sala.id == "{{ old('fk_idSala', $reserva->sala->id) }}" ? 'selected' : ''}>${sala.nombre}</option>`);
                        });
                    }
                }

                // Cargar salas al inicio si hay una ubicación preseleccionada
                cargarSalasIniciales();

                // Actualizar salas cuando se cambia la ubicación
                ubicacionSelect.change(function () {
                    cargarSalasIniciales();
                });

                // Manejo dinámico de horarios
                $("#add-horario").click(function () {
                    let newHorario = $(".horario-item:first").clone();
                    newHorario.find("input").val("");
                    newHorario.find(".remove-horario").show();
                    $("#horarios-container").append(newHorario);
                });

                $(document).on("click", ".remove-horario", function () {
                    $(this).parent(".horario-item").remove();
                });

                // Inicializar Select2
                $("#fk_participantes").select2({
                    placeholder: "--Seleccione--",
                    allowClear: true,
                    width: "100%"
                });
            });
        </script>
    @endsection

@endsection
