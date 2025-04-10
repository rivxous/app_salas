@extends('layouts.base')
@section('title', 'Creación de Reservas')
@section('content')

    <div class="row border rounded p-3 mb-3">
        <div class="col-12">
            <h2>Crear Reserva</h2>
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

        {!! Form::open(['route' => 'reservas.store', 'method' => 'POST', 'id' => 'reservaForm']) !!}
        <div class="row">

            <!-- Ubicación -->
            <div class="col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('ubicacion', 'Ubicación:') !!}
                    <select id="ubicacion" name="ubicacion" class="form-select">
                        <option value="">--Seleccione--</option>
                        @foreach($salas->pluck('ubicacion')->unique() as $ubicacion)
                            <option value="{{ $ubicacion }}" {{ old('ubicacion') == $ubicacion ? 'selected' : '' }}>
                                {{ $ubicacion }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <!-- Sala -->
            <div class="col-md-12 mt-2">
                <div class="form-group" style="display: none;">
                    {!! Form::label('fk_idSala', 'Sala:') !!}
                    <select id="fk_idSala" name="fk_idSala" class="form-select">
                        <option value="">--Seleccione--</option>
                    </select>
                    @error('fk_idSala') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <!-- Datos de reserva -->
            <div class="campo-reserva" style="{{ $errors->any() ? '' : 'display: none;' }}">

                <!-- Tipo de evento -->
                <div class="form-group mt-3">
                    {!! Form::label('tipoEvento', 'Tipo de Evento:') !!}
                    {!! Form::select('tipoEvento', [null => '--Seleccione--', 'Reunión' => 'Reunión', 'charla' => 'Charla', 'curso' => 'Curso'], null, ['class' => 'form-control', 'id' => 'tipoEvento', 'required']) !!}
                </div>

                <!-- Fechas y horarios -->
                <div id="fechasContainer">
                    <div class="form-row row fecha-grupo">
                        <div class="col-md-4 mt-2">
                            {!! Form::label('fechas[]', 'Fecha:') !!}
                            {!! Form::date('fechas[]', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-4 mt-2">
                            {!! Form::label('horas_inicio[]', 'Hora de Inicio:') !!}
                            {!! Form::time('horas_inicio[]', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-4 mt-2">
                            {!! Form::label('horas_fin[]', 'Hora de Fin:') !!}
                            {!! Form::time('horas_fin[]', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="col-md-1 mt-4 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm eliminarFecha">Eliminar</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="agregarFecha" class="btn btn-success mt-2">Agregar fecha</button>

                <!-- Título -->
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        {!! Form::label('titulo', 'Título:') !!}
                        {!! Form::text('titulo', old('titulo'), ['class' => 'form-control', 'placeholder' => 'Título de la reserva']) !!}
                        @error('titulo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        {!! Form::label('descripcion', 'Descripción:') !!}
                        {!! Form::text('descripcion', old('descripcion'), ['class' => 'form-control', 'placeholder' => 'Descripción de la reserva']) !!}
                        @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Participantes -->
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        {!! Form::label('participantes', 'Participantes:') !!}
                        {!! Form::select('participantes[]', $usuarios, old('participantes'), ['id' => 'fk_participantes', 'class' => 'form-select', 'multiple' => 'multiple']) !!}
                        @error('participantes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="col-md-12 mt-3">
                {!! Form::submit('Crear', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function () {
            const salas = @json($salas);
            const horariosPorSala = {};
            const salasPorUbicacion = {};

            salas.forEach(sala => {
                horariosPorSala[sala.id] = sala.horarios_disponibles;
                if (!salasPorUbicacion[sala.ubicacion]) {
                    salasPorUbicacion[sala.ubicacion] = [];
                }
                salasPorUbicacion[sala.ubicacion].push(sala);
            });

            const $ubicacionSelect = $('#ubicacion');
            const $salaSelect = $('#fk_idSala');
            const $salaSelectContainer = $salaSelect.closest('.form-group');
            const $campoReserva = $('.campo-reserva');
            const $participantesSelect = $('#fk_participantes');
            const $tipoEvento = $('#tipoEvento');
            const $fechasContainer = $('#fechasContainer');
            const $agregarFechaBtn = $('#agregarFecha');

            $campoReserva.hide();
            $salaSelectContainer.hide();

            $ubicacionSelect.on('change', function () {
                const seleccionada = $(this).val();
                $salaSelect.empty().append('<option value="">--Seleccione--</option>');
                $salaSelectContainer.hide();

                if (seleccionada && salasPorUbicacion[seleccionada]) {
                    salasPorUbicacion[seleccionada].forEach(sala => {
                        $salaSelect.append(`<option value="${sala.id}">${sala.nombre}</option>`);
                    });
                    $salaSelectContainer.show();
                } else {
                    $campoReserva.hide();
                }
            });

            $salaSelect.on('change', function () {
                if ($(this).val()) {
                    $campoReserva.show();
                    $participantesSelect.select2({
                        placeholder: "--Seleccione--",
                        allowClear: true,
                        width: "100%"
                    });
                    $participantesSelect.next(".select2-container").show();
                } else {
                    $campoReserva.hide();
                }
            });

            $tipoEvento.on('change', function () {
                if ($(this).val() === 'curso') {
                    $agregarFechaBtn.show();
                } else {
                    $agregarFechaBtn.hide();
                    $fechasContainer.find('.fecha-grupo').slice(1).remove();
                }
            });

            $agregarFechaBtn.on('click', function () {
                const grupo = $(`
            <div class="form-row row fecha-grupo mb-3">
                <div class="col-md-4 mt-2">
                    <label>Fecha:</label>
                    <input type="date" name="fechas[]" class="form-control" required>
                </div>
                <div class="col-md-4 mt-2">
                    <label>Hora de Inicio:</label>
                    <input type="time" name="horas_inicio[]" class="form-control" required>
                </div>
                <div class="col-md-4 mt-2">
                    <label>Hora de Fin:</label>
                    <input type="time" name="horas_fin[]" class="form-control" required>
                </div>
                <div class="col-md-1 mt-4 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm eliminarFecha">Eliminar</button>
                </div>
            </div>
            `);
                $fechasContainer.append(grupo);
            });

            $(document).on('click', '.eliminarFecha', function () {
                $(this).closest('.fecha-grupo').remove();
            });

            $(document).on('change', 'input[name="horas_fin[]"]', function () {
                const $grupo = $(this).closest('.fecha-grupo');
                const inicio = $grupo.find('input[name="horas_inicio[]"]').val();
                const fin = $(this).val();
                if (inicio && fin && fin <= inicio) {
                    alert("La hora de fin debe ser mayor que la hora de inicio.");
                    $(this).val('');
                }
            });
        });
    </script>
@endsection
