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
use Illuminate\Support\Facades\Validator;

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
                            ->unique('fecha') // Si querés seguir evitando duplicados
                            ->filter()
                            ->map(function ($horario) {
                                $fecha = Carbon::parse($horario->fecha)->format('d-m-Y');
                                $horaInicio = Carbon::parse($horario->hora_inicio)->format('h:i A');
                                $horaFin = Carbon::parse($horario->hora_fin)->format('h:i A');
                                return "$fecha $horaInicio a $horaFin";
                            })
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
            // Obtener todas las salas disponibles sin reservas activas
            $salas = Salas::without('reservas')->get();
            $usuarios = User::pluck('nombre', 'id');

            return view('reservas.create', compact('salas', 'usuarios'));

        } catch (\Exception $e) {
            Log::error('Error al cargar el formulario de creación: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar el formulario de creación.']);
        }
    }






    /**
     * Genera los horarios disponibles excluyendo el intervalo de 12:00 p. m. - 1:00 p. m.
     */
    protected function generarHorariosDisponibles($horariosOcupados)
    {
        $horariosDisponibles = [];
        // Suponiendo que trabajamos con bloques de 30 minutos como ejemplo
        $intervalo = 30; // en minutos

        // Obtén el rango total de horarios (puedes ajustar esto a tus necesidades)
        $rangoInicio = Carbon::now()->startOfDay(); // Por ejemplo, desde el inicio del día
        $rangoFin = Carbon::now()->endOfDay(); // Hasta el final del día

        // Generar bloques de horarios disponibles
        $periodo = new \DatePeriod($rangoInicio, new \DateInterval("PT{$intervalo}M"), $rangoFin);

        foreach ($periodo as $fechaHora) {
            $horarioInicio = $fechaHora;
            $horarioFin = (clone $fechaHora)->addMinutes($intervalo); // Asegúrate de que sea el mismo bloque de 30 minutos

            // Verifica si el horario está ocupado
            $encontrado = false;
            foreach ($horariosOcupados as $horario) {
                if ($horario['inicio'] <= $horarioFin && $horario['fin'] >= $horarioInicio) {
                    $encontrado = true;
                    break;
                }
            }

            // Si no está ocupado, agrega a la lista de horarios disponibles
            if (!$encontrado) {
                $horariosDisponibles[] = [
                    'inicio' => $horarioInicio,
                    'fin' => $horarioFin,
                ];
            }
        }

        return $horariosDisponibles; // Devuelve objetos con inicio y fin
    }


    public function store(Request $request)
    {

        $request->validate([
            'titulo' => 'required',
            'descripcion' => 'required|string',
            'tipoEvento' => 'required|in:Reunión,charla,curso',
            'fk_idSala' => 'required|exists:salas,id',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'required|date',
            'horas_inicio' => 'required|array|min:1',
            'horas_inicio.*' => 'required|date_format:H:i',
            'horas_fin' => 'required|array|min:1',
            'horas_fin.*' => 'required|date_format:H:i',
        ], [
            'titulo.required' => 'El título de la reserva es obligatorio.',
            'descripcion.required' => 'Debe ingresar una descripción para la reserva.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'tipoEvento.required' => 'Debe seleccionar un tipo de evento.',
            'fk_idSala.required' => 'Debe seleccionar una sala para la reserva.',
            'fk_idSala.exists' => 'La sala seleccionada no es válida.',
            'participantes.required' => 'Debe seleccionar al menos un participante.',
            'participantes.array' => 'Los participantes deben ser una lista.',
            'participantes.min' => 'Debe agregar al menos un participante.',
            'participantes.*.exists' => 'Uno o más participantes no son válidos.',
            'fechas.required' => 'Debe seleccionar al menos una fecha.',
            'fechas.array' => 'Las fechas deben ser una lista.',
            'fechas.min' => 'Debe agregar al menos una fecha.',
            'fechas.*.date' => 'Una o más fechas no son válidas.',
            'horas_inicio.required' => 'Las horas de inicio son obligatorias.',
            'horas_inicio.array' => 'Las horas de inicio deben ser una lista.',
            'horas_inicio.min' => 'Debe agregar al menos una hora de inicio.',
            'horas_inicio.*.date_format' => 'El formato de la hora de inicio no es válido.',
            'horas_fin.required' => 'Las horas de fin son obligatorias.',
            'horas_fin.array' => 'Las horas de fin deben ser una lista.',
            'horas_fin.min' => 'Debe agregar al menos una hora de fin.',
            'horas_fin.*.date_format' => 'El formato de la hora de fin no es válido.',
        ]);

        $errores = [];
        foreach ($request->horas_inicio as $i => $horaInicio) {
            $horaFin = $request->horas_fin[$i];
            if (strtotime($horaFin) <= strtotime($horaInicio)) {
                $errores["horas_fin.$i"] = "La hora de fin debe ser posterior a la hora de inicio para la fecha " . $request->fechas[$i];
            }
        }
        if (!empty($errores)) {
            return back()->withErrors($errores)->withInput();
        }
        try {
            // Validación de que las horas de fin son mayores a las horas de inicio
            // Crear la reserva principal
            $reserva = new Reservas();
            $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'fk_idSala']));
            $reserva->fk_idUsuario = Auth::id();
            $reserva->save();

            // Guardar participantes
            if ($request->has('participantes')) {
                $participantes = collect($request->participantes)->map(fn($usuarioId) => new Participantes([
                    'fk_idReserva' => $reserva->id,
                    'fk_idUsuario' => $usuarioId,
                ]));
                $reserva->participantes_reservas()->saveMany($participantes);
            }

            // Guardar horarios
            foreach ($request->fechas as $i => $fecha) {
                $horario = new HorariosReservas([
                    'fecha' => $fecha,
                    'hora_inicio' => $request->horas_inicio[$i],
                    'hora_fin' => $request->horas_fin[$i],
                    'fk_idReserva' => $reserva->id,
                ]);
                $horario->save();
            }

            return redirect()->route('reservas.index')->with('success', 'Reserva creada exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear la reserva: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al crear la reserva.']);
        }
    }


    public function show($id)
    {
        try {
            $reserva = Reservas::with(['reserva_horarios', 'participantes_reservas'])->findOrFail($id);
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

    public function update(Request $request, $id)
    {
//        return $request->all();
//        $request->merge([
//            'horas_inicio' => array_map(function ($hora) {
//                return Carbon::createFromFormat('H:i:s', $hora)->format('H:i');
//            }, $request->horas_inicio),
//            'horas_fin' => array_map(function ($hora) {
//                return Carbon::createFromFormat('H:i:s', $hora)->format('H:i');
//            }, $request->horas_fin),
//        ]);

        $request->validate([
            'titulo' => 'required',
            'descripcion' => 'required|string',
            'tipoEvento' => 'required|in:Reunión,charla,curso',
            'fk_idSala' => 'required|exists:salas,id',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'required|date',
            'horas_inicio' => 'required|array|min:1',
            'horas_inicio.*' => 'required|date_format:H:i',
            'horas_fin' => 'required|array|min:1',
            'horas_fin.*' => 'required|date_format:H:i',
        ], [
            'titulo.required' => 'El título de la reserva es obligatorio.',
            'descripcion.required' => 'Debe ingresar una descripción para la reserva.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'tipoEvento.required' => 'Debe seleccionar un tipo de evento.',
            'fk_idSala.required' => 'Debe seleccionar una sala para la reserva.',
            'fk_idSala.exists' => 'La sala seleccionada no es válida.',
            'participantes.required' => 'Debe seleccionar al menos un participante.',
            'participantes.array' => 'Los participantes deben ser una lista.',
            'participantes.min' => 'Debe agregar al menos un participante.',
            'participantes.*.exists' => 'Uno o más participantes no son válidos.',
            'fechas.required' => 'Debe seleccionar al menos una fecha.',
            'fechas.array' => 'Las fechas deben ser una lista.',
            'fechas.min' => 'Debe agregar al menos una fecha.',
            'fechas.*.date' => 'Una o más fechas no son válidas.',
            'horas_inicio.required' => 'Las horas de inicio son obligatorias.',
            'horas_inicio.array' => 'Las horas de inicio deben ser una lista.',
            'horas_inicio.min' => 'Debe agregar al menos una hora de inicio.',
            'horas_inicio.*.date_format' => 'El formato de la hora de inicio no es válido.',
            'horas_fin.required' => 'Las horas de fin son obligatorias.',
            'horas_fin.array' => 'Las horas de fin deben ser una lista.',
            'horas_fin.min' => 'Debe agregar al menos una hora de fin.',
            'horas_fin.*.date_format' => 'El formato de la hora de fin no es válido.',
        ]);

        // Validación personalizada: fecha de fin > fecha de inicio
        $errores = [];
        foreach ($request->fechas as $i => $fecha) {
            $fechaInicio = $fecha . ' ' . $request->horas_inicio[$i];
            $fechaFin = $fecha . ' ' . $request->horas_fin[$i];


            if (strtotime($fechaFin) <= strtotime($fechaInicio)) {
                $errores["horas_fin.$i"] = "La hora de fin debe ser posterior a la hora de inicio para la fecha " . $fecha;
            }
        }

        if (!empty($errores)) {
            return back()->withErrors($errores)->withInput();
        }

        try {
            $reserva = Reservas::findOrFail($id);
            $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'fk_idSala']));
            $reserva->save();

            // Actualizar participantes
            $reserva->participantes_reservas()->delete();
            $participantes = collect($request->participantes)->map(fn($usuarioId) => new Participantes([
                'fk_idReserva' => $reserva->id,
                'fk_idUsuario' => $usuarioId,
            ]));
            $reserva->participantes_reservas()->saveMany($participantes);

            // Actualizar horarios
            $reserva->reserva_horarios()->delete();
            foreach ($request->fechas as $i => $fecha) {
                $horario = new HorariosReservas([
                    'fecha' => $fecha,
                    'hora_inicio' => $request->horas_inicio[$i],
                    'hora_fin' => $request->horas_fin[$i],
                    'fk_idReserva' => $reserva->id,
                ]);
                $horario->save();
            }

            return redirect()->route('reservas.index')->with('success', 'Reserva actualizada exitosamente!');
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
