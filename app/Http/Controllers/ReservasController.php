<?php

namespace App\Http\Controllers;

use App\Models\HorariosReservas;
use App\Models\Participantes;
use App\Models\Reservas;
use App\Models\Salas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservasController extends Controller
{

    // Método para obtener los eventos desde la base de datos
    public function listar_reservas_calendario()
    {
        // Obtén las reservas de la base de datos
        $reservas = Reservas::all();

        // Formatear las reservas para FullCalendar
        $events = $reservas->map(function ($reserva) {
            return [
                'title' => $reserva->titulo,  // Título del evento
                'start' => Carbon::parse($reserva->reserva_horarios->fecha_inicio)->format('Y-m-d\TH:i:s'), // Fecha de inicio
                'end' => Carbon::parse($reserva->fecha_fin)->format('Y-m-d\TH:i:s'), // Fecha de fin
                'color' => '#ff5733', // Puedes cambiar el color del evento si quieres
                'description' => $reserva->descripcion, // Agrega más detalles si es necesario
            ];
        });

        // Devuelve los eventos como JSON
        return response()->json($events);
    }

    public function listarReservaSalas()
    {
        //listar las salas con su ubicacion
        $salas = Salas::get();
        return $salas;
    }

    public function index()
    {
        try {
            $reservas = Reservas::with(['sala', 'usuario_creador_reserva', 'participantes_reservas.usuario'])
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($reserva) {
                    // Eliminamos posibles nombres de usuario duplicados y valores vacíos
                    $participantes = strtoupper(
                        $reserva->participantes_reservas
                            ->pluck('usuario.username')
                            ->unique()
                            ->filter() // Filtra valores vacíos
                            ->implode(', ')
                    );

                    // Convertir cada horario a formato Carbon antes de concatenarlo
                    $horarios = strtoupper(
                        $reserva->reserva_horarios
                            ->pluck('horario')
                            ->unique()
                            ->filter()
                            ->map(fn($horario) => Carbon::parse($horario)->format('d-m-Y H:i a')) // Convierte a Carbon y da formato
                            ->implode(', ')
                    );

                    $reserva->participantes = $participantes;
                    $reserva->horarios_new = $horarios;
                    unset($reserva->participantes_reservas);
                    unset($reserva->reserva_horarios);
                    return $reserva;
                });

            return view('reservas.index', ['reservas' => $reservas]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las reservas: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar las reservas.']);
        }
    }


    public function buscar_salas_horios_disponibles(Request $request)
    {
        $request->validate([
            'id_sala' => 'required',
        ]);
        try {
            $reservas = Reservas::where('fk_idSala', $request->id_sala)
                ->get();


            if (count($reservas) > 0) {
                return $reservas;
            } else {
                return response()->json(['msj' => 'No hay disponibilidad en la sala'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscar_salas_por_ubicacion@ReservasController: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'No hay disponibilidad en la sala']);
        }
    }

    public function create()
    {
        try {
            $salas = Salas::without('reservas')->get();
            $usuarios = User::pluck('nombre', 'id');

            // Obtener los horarios ya reservados
            $horariosReservas = HorariosReservas::pluck('horario')->map(fn($horario) => Carbon::parse($horario)->format('Y-m-d\TH:i'))->toArray();

            foreach ($salas as $sala) {
                $horariosOcupados = HorariosReservas::whereHas('reserva', function ($query) use ($sala) {
                    $query->where('fk_idSala', $sala->id);
                })->pluck('horario')->map(fn($horario) => Carbon::parse($horario))->toArray();

                // Generar horarios disponibles (asumimos que retorna un array de Carbon o datetime)
                $horariosDisponibles = $this->generarHorariosDisponibles($horariosOcupados);

                // Formatear los horarios disponibles
                $sala->horarios_disponibles = collect($horariosDisponibles)->map(function ($horario) {
                    return Carbon::parse($horario)->format('d-m-Y h:i a'); // formato para inputs datetime-local
                })->toArray();
            }

//            return $sala;
            return view('reservas.create', compact('salas', 'usuarios', 'horariosReservas'));
        } catch (\Exception $e) {
            Log::error('Error al cargar el formulario de creación: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar el formulario de creación.']);
        }
    }

    /**
     * Genera los horarios disponibles excluyendo el intervalo de 12:00 p. m. - 1:00 p. m.
     */
    private function generarHorariosDisponibles(array $horariosOcupados)
    {
        $horaInicio = Carbon::createFromTime(7, 30);
        $horaFin = Carbon::createFromTime(16, 30);
        $horaLibreInicio = Carbon::createFromTime(12, 0);
        $horaLibreFin = Carbon::createFromTime(13, 0);

        $horariosDisponibles = [];

        while ($horaInicio < $horaFin) {
            if ($horaInicio->between($horaLibreInicio, $horaLibreFin)) {
                $horaInicio->addMinutes(30); // Saltar el intervalo bloqueado
                continue;
            }

            if (!in_array($horaInicio, $horariosOcupados)) {
                $horariosDisponibles[] = $horaInicio->format('Y-m-d\TH:i');
            }

            $horaInicio->addMinutes(30); // Intervalos de media hora
        }

        return $horariosDisponibles;
    }

    public function store(Request $request)
    {
        dd($request->all());
        $request->validate([
            'titulo' => 'required',
            'descripcion' => 'required|string',
            'tipoEvento' => 'required',
            'horarios' => 'required|array|min:1', // Debe ser un array con al menos un elemento
            'horarios.*' => 'required|date_format:Y-m-d\TH:i', // Cada horario debe ser una fecha válida
            'fk_idSala' => 'required|exists:salas,id',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
        ], [
            'titulo.required' => 'El título de la reserva es obligatorio.',
            'descripcion.required' => 'Debe ingresar una descripción para la reserva.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'tipoEvento.required' => 'Debe seleccionar un tipo de evento.',
            'horarios.required' => 'Debe seleccionar al menos un horario.',
            'horarios.array' => 'Los horarios deben ser una lista.',
            'horarios.min' => 'Debe agregar al menos un horario.',
            'horarios.*.required' => 'Cada horario es obligatorio.',
            'horarios.*.date_format' => 'El formato de fecha y hora no es válido. Debe ser YYYY-MM-DDTHH:MM.',
            'fk_idSala.required' => 'Debe seleccionar una sala para la reserva.',
            'fk_idSala.exists' => 'La sala seleccionada no es válida.',
            'participantes.required' => 'Debe seleccionar al menos un participante.',
            'participantes.array' => 'Los participantes deben ser una lista.',
            'participantes.min' => 'Debe agregar al menos un participante.',
            'participantes.*.exists' => 'Uno o más participantes no son válidos.',
        ]);


        try {
//            dd(Auth::id());
            $reserva = new Reservas();
            $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'horario', 'fk_idSala']));
            $reserva->fk_idUsuario = Auth::id();
            $reserva->save();

            if ($request->has('participantes')) {
                $participantes = collect($request->participantes)->map(fn($usuarioId) => new Participantes([
                    'fk_idReserva' => $reserva->id,
                    'fk_idUsuario' => $usuarioId,
                ]));
                $reserva->participantes_reservas()->saveMany($participantes);
            }
            if ($request->has('horarios')) {
                $horarios = collect($request->horarios)->map(fn($horario) => new HorariosReservas([
                    'horario' => $horario,
                    'fk_idReserva' => $reserva->id,
                ]));
                $reserva->reserva_horarios()->saveMany($horarios);
            }

            return redirect()->route('reservas.index')->with('success', 'Reserva creada exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear la reserva: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al crear la reserva.']);
        }
        $request->validate([
            'tipoEvento' => 'required|in:Reunión,charla,curso',
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'required|date',
            'horas_inicio' => 'required|array|min:1',
            'horas_inicio.*' => 'required|date_format:H:i',
            'horas_fin' => 'required|array|min:1',
            'horas_fin.*' => 'required|date_format:H:i',
        ]);

        $tipoEvento = $request->input('tipoEvento');
        $fechas = $request->input('fechas');
        $horasInicio = $request->input('horas_inicio');
        $horasFin = $request->input('horas_fin');

        if (in_array($tipoEvento, ['Reunión', 'charla']) && count($fechas) > 1) {
            return back()->withErrors(['fechas' => 'Solo se puede seleccionar una fecha para reuniones o charlas.'])->withInput();
        }

        foreach ($fechas as $i => $fecha) {
            // Validación adicional por si la hora de inicio es mayor o igual a la de fin
            if (strtotime($horasInicio[$i]) >= strtotime($horasFin[$i])) {
                return back()->withErrors(["horas_fin.$i" => "La hora de fin debe ser mayor a la hora de inicio en la fecha $fecha."])->withInput();
            }

            Reserva::create([
                'tipo_evento' => $tipoEvento,
                'fecha' => $fecha,
                'hora_inicio' => $horasInicio[$i],
                'hora_fin' => $horasFin[$i],
                // otros campos si los hay
            ]);
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva(s) creada(s) correctamente.');

    }

    public
function show($id)
{
    try {
        $reserva = Reservas::findOrFail($id);
        $salas = Salas::without('reservas')->get();
        $usuarios = User::pluck('nombre', 'id');


        return view('reservas.edit', compact('reserva', 'salas', 'usuarios'));
    } catch (ModelNotFoundException $e) {
        Log::error('Reserva no encontrada: ' . $e->getMessage());
        return redirect()->route('reservas.index')->withErrors(['error' => 'No se encontró la reserva.']);
    } catch (\Exception $e) {
        Log::error('Error al obtener la reserva: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar la reserva.']);
    }
}

public
function update(Request $request, $id)
{
//        return $request->all();
    $request->validate([
        'titulo' => 'required',
        'descripcion' => 'required|string',
        'tipoEvento' => 'required',
        'horarios' => 'required|array|min:1', // Debe ser un array con al menos un elemento
        'horarios.*' => 'required|date_format:Y-m-d\TH:i', // Cada horario debe ser una fecha válida
        'fk_idSala' => 'required|exists:salas,id',
        'participantes' => 'required|array|min:1',
        'participantes.*' => 'exists:users,id',
    ], [
        'horarios.required' => 'Debe seleccionar al menos un horario.',
        'horarios.array' => 'Los horarios deben ser una lista.',
        'horarios.min' => 'Debe agregar al menos un horario.',
        'horarios.*.required' => 'Cada horario es obligatorio.',
        'horarios.*.date_format' => 'El formato de fecha y hora no es válido.',
    ]);

    try {
        // Buscar la reserva
        $reserva = Reservas::findOrFail($id);

        // Actualizar los datos de la reserva
        $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'fk_idSala']));
        $reserva->save();

        // Actualizar participantes
        if ($request->has('participantes')) {
            // Eliminar participantes actuales
            $reserva->participantes_reservas()->delete();

            // Insertar nuevos participantes
            $participantes = collect($request->participantes)->map(fn($usuarioId) => new Participantes([
                'fk_idReserva' => $reserva->id,
                'fk_idUsuario' => $usuarioId,
            ]));

            $reserva->participantes_reservas()->saveMany($participantes);
        }
        // Actualizar horarios
        if ($request->has('horarios')) {
            // Eliminar participantes actuales
            $reserva->reserva_horarios()->delete();

            // Insertar nuevos participantes
            $horarios = collect($request->horarios)->map(fn($horarios) => new HorariosReservas([
                'horario' => $horarios,
                'fk_idReserva' => $reserva->id,
            ]));

            $reserva->reserva_horarios()->saveMany($horarios);
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    } catch (ModelNotFoundException $e) {
        Log::error('Reserva no encontrada: ' . $e->getMessage());
        return redirect()->route('reservas.index')->withErrors(['error' => 'No se encontró la reserva.']);
    } catch (\Exception $e) {
        Log::error('Error al actualizar la reserva: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Hubo un problema al actualizar la reserva.']);
    }
}


public
function destroy($id)
{
    try {
        $reserva = Reservas::findOrFail($id);
        $reserva->delete();

        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada correctamente.');
    } catch (ModelNotFoundException $e) {
        Log::error('Reserva no encontrada: ' . $e->getMessage());
        return redirect()->route('reservas.index')->withErrors(['error' => 'No se encontró la reserva.']);
    } catch (\Exception $e) {
        Log::error('Error al eliminar la reserva: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Hubo un problema al eliminar la reserva.']);
    }
}
}
