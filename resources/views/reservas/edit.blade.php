@extends('layouts.base')
@section('title', 'Editar Reserva')
@section('content')

    <div class="row border rounded p-3 mb-3">
        <div class="col-12">
            <h2>Editar Reserva</h2>
        </div>

        {!! Form::model($reserva, ['route' => ['reservas.update', $reserva->id], 'method' => 'PUT', 'id' => 'reservaForm']) !!}
        <div class="row">

            <!-- Ubicación -->
            <div class="col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('ubicacion', 'Ubicación:') !!}
                    <select id="ubicacion" name="ubicacion" class="form-select">
                        <option value="">--Seleccione--</option>
                        @foreach($salas->pluck('ubicacion')->unique() as $ubicacion)
                            <option value="{{ $ubicacion }}" {{ $reserva->sala->ubicacion == $ubicacion ? 'selected' : '' }}>
                                {{ $ubicacion }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <!-- Sala -->
            <div class="col-md-12 mt-2">
                <div class="form-group">
                    {!! Form::label('fk_idSala', 'Sala:') !!}
                    <select id="fk_idSala" name="fk_idSala" class="form-select">
                        <option value="">--Seleccione--</option>
                        @foreach($salas as $sala)
                            <option value="{{ $sala->id }}" {{ $reserva->fk_idSala == $sala->id ? 'selected' : '' }}>
                                {{ $sala->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('fk_idSala') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <!-- Datos de reserva -->
            <div class="campo-reserva">

                <!-- Tipo de evento -->
                <div class="form-group mt-3">
                    {!! Form::label('tipoEvento', 'Tipo de Evento:') !!}
                    {!! Form::select('tipoEvento', [null => '--Seleccione--', 'Reunión' => 'Reunión', 'charla' => 'Charla', 'curso' => 'Curso'], $reserva->tipoEvento, ['class' => 'form-control', 'id' => 'tipoEvento', 'required']) !!}
                    @error('tipoEvento') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Fechas y horarios -->
                <div id="fechasContainer">
                    @foreach ($reserva->reserva_horarios as $horario)
                        <div class="form-row row fecha-grupo">
                            <div class="col-md-4 mt-2">
                                {!! Form::label('fechas[]', 'Fecha:') !!}
                                {!! Form::date('fechas[]', \Carbon\Carbon::parse($horario->fecha)->format('Y-m-d'), ['class' => 'form-control', 'required']) !!}
                                @error('fechas.*') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mt-2">
                                {!! Form::label('horas_inicio[]', 'Hora de Inicio:') !!}
                                {!! Form::time('horas_inicio[]', \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i'), ['class' => 'form-control', 'required']) !!}
                                @error('horas_inicio.*') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mt-2">
                                {!! Form::label('horas_fin[]', 'Hora de Fin:') !!}
                                {!! Form::time('horas_fin[]', \Carbon\Carbon::parse($horario->hora_fin)->format('H:i'), ['class' => 'form-control', 'required']) !!}
                                @error('horas_fin.*') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-1 mt-4 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm eliminarFecha">Eliminar</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="agregarFecha" class="btn btn-success mt-2">Agregar fecha</button>

                <!-- Título -->
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        {!! Form::label('titulo', 'Título:') !!}
                        {!! Form::text('titulo', old('titulo', $reserva->titulo), ['class' => 'form-control', 'placeholder' => 'Título de la reserva']) !!}
                        @error('titulo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        {!! Form::label('descripcion', 'Descripción:') !!}
                        {!! Form::text('descripcion', old('descripcion', $reserva->descripcion), ['class' => 'form-control', 'placeholder' => 'Descripción de la reserva']) !!}
                        @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Participantes -->
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        {!! Form::label('participantes', 'Participantes:') !!}
                        {!! Form::select('participantes[]', $usuarios, $reserva->participantes_reservas->pluck('fk_idUsuario')->toArray(), ['id' => 'fk_participantes', 'class' => 'form-select', 'multiple' => 'multiple']) !!}
                        @error('participantes') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="col-md-12 mt-3">
                {!! Form::submit('Actualizar', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function () {
            const $form = $('#reservaForm'); // Asegúrate de seleccionar el formulario correcto

            $form.on('submit', function (e) {
                let valid = true;

                $('.fecha-grupo').each(function () {
                    const fecha = $(this).find('input[name="fechas[]"]').val();
                    const horaInicio = $(this).find('input[name="horas_inicio[]"]').val();
                    const horaFin = $(this).find('input[name="horas_fin[]"]').val();

                    const fechaInicio = new Date(`${fecha}T${horaInicio}`);
                    const fechaFin    = new Date(`${fecha}T${horaFin}`);

                    if (fechaFin <= fechaInicio) {
                        valid = false;
                        alert(`La hora de fin debe ser posterior a la hora de inicio para la fecha ${fecha}`);
                        $(this).find('input[name="horas_inicio[]"], input[name="horas_fin[]"]')
                            .addClass('is-invalid');
                        return false; // sale del each
                    } else {
                        $(this).find('input[name="horas_inicio[]"], input[name="horas_fin[]"]')
                            .removeClass('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();  // cancela el submit
                }
                // <-- aquí devolvemos explícitamente
                return valid;
            });



            const $agregarFechaBtn = $('#agregarFecha');
            const $fechasContainer = $('#fechasContainer');

            // Inicializa select2 para el campo de participantes
            $('#fk_participantes').select2({
                placeholder: "--Seleccione--",
                allowClear: true,
                width: "100%"
            });

            // Evento para agregar nueva fecha
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

            // Evento para eliminar fecha
            $(document).on('click', '.eliminarFecha', function () {
                $(this).closest('.fecha-grupo').remove();
            });
        });
    </script>
@endsection
