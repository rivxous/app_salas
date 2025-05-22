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
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ReservasController extends Controller
{

    public function event($reservas)
    {
        $events = $reservas->flatMap(function ($reserva) {
            return $reserva->reserva_horarios->map(function ($horario) use ($reserva) {
                // Formatear correctamente la fecha/hora
                $start = Carbon::parse($horario->fecha->format('Y-m-d') . ' ' . $horario->hora_inicio)
                    ->setTimezone('UTC')
                    ->toIso8601String();

                $end = Carbon::parse($horario->fecha->format('Y-m-d') . ' ' . $horario->hora_fin)
                    ->setTimezone('UTC')
                    ->toIso8601String();

                return [
                    'title' => $reserva->titulo,
                    'start' => $start,
                    'end' => $end,
                    'color' => '#ff5733',
                    'description' => $reserva->descripcion,
                    'extendedProps' => [
                        'sala' => $reserva->sala->nombre,
                        'organizador' => $reserva->usuario_creador_reserva->nombre
                    ]
                ];
            });
        });
        return $events;
    }

    // Método para obtener los eventos desde la base de datos
    public function listar_reservas_calendario()
    {
        $reservas = Reservas::with('reserva_horarios')->get();

        $events = $reservas->flatMap(function ($reserva) {
            return $reserva->reserva_horarios->map(function ($horario) use ($reserva) {
                return [
                    'title' => $reserva->titulo,
                    'start' => $horario->fecha->toDateString() . ' ' . $horario->hora_inicio->format('H:i:s'),
                    'end' => $horario->fecha->toDateString() . ' ' . $horario->hora_fin->format('H:i:s'),
                    'color' => '#ff5733',
                    'description' => $reserva->descripcion,
                ];
            });
        });

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

    public function verificarParticipantes(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'participantes' => 'required|array|min:1'
        ]);

        $conflictos = [];

        foreach ($request->participantes as $participanteId) {
            $reservas = HorariosReservas::where('fecha', $request->fecha)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                        ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('hora_inicio', '<=', $request->hora_inicio)
                                ->where('hora_fin', '>=', $request->hora_fin);
                        });
                })
                ->whereHas('reserva.participantes_reservas', function ($query) use ($participanteId) {
                    $query->where('fk_idUsuario', $participanteId);
                })
                ->with(['reserva' => function($query) {
                    $query->with(['sala', 'usuario_creador_reserva']);
                }])
                ->get();

            foreach ($reservas as $reserva) {
                // En el controlador, modificar la creación del array de conflictos
                $conflictos[] = [
                    'usuario' => User::find($participanteId)->nombre,
                    'sala' => strtoupper($reserva->reserva->sala->nombre),
                    'titulo' => strtoupper($reserva->reserva->titulo),
                    'hora_inicio' => Carbon::parse($reserva->hora_inicio)
                        ->setTimezone('America/Caracas')
                        ->format('H:i'),

                    'hora_fin' => Carbon::parse($reserva->hora_fin)
                        ->setTimezone('America/Caracas')
                        ->format('H:i')
                ];
            }
        }

        return response()->json([
            'disponible' => empty($conflictos),
            'conflictos' => $conflictos
        ]);
    }
    public function buscar_salas_horarios_disponibles(Request $request)
    {
        $request->validate([
            'id_sala' => 'required',
            'fechas' => 'sometimes|required|date',
            'horas_inicio' => 'sometimes|required|date_format:H:i',
            'horas_fin' => 'sometimes|required|date_format:H:i|after:horas_inicio',
        ]);

        try {
            $reservas = Reservas::where('fk_idSala', $request->id_sala)
                ->with(['reserva_horarios', 'sala', 'usuario_creador_reserva', 'participantes_reservas.usuario']);

            if ($request->has(['fechas', 'horas_inicio', 'horas_fin'])) {
                $fecha = $request->fechas;
                $horaInicio = $request->horas_inicio;
                $horaFin = $request->horas_fin;

                $reservas->whereHas('reserva_horarios', function ($query) use ($fecha, $horaInicio, $horaFin) {
                    $query->where('fecha', $fecha)
                        ->where(function ($q) use ($horaInicio, $horaFin) {
                            $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                                ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                                ->orWhere(function ($subQ) use ($horaInicio, $horaFin) {
                                    $subQ->where('hora_inicio', '<=', $horaInicio)
                                        ->where('hora_fin', '>=', $horaFin);
                                });
                        });
                });
            }

            $reservas = $reservas->get();

            if ($reservas->isEmpty()) {
                return response()->json(['msj' => 'No hay reservas que coincidan con los criterios'], 404);
            }

            return response()->json($reservas, 200);
        } catch (\Exception $e) {
            Log::error('Error en buscar_salas_por_ubicacion@ReservasController: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar la solicitud'], 500);
        }
    }

    public function create()
    {
        $salas = Salas::with('horariosReservas')->get();
        $usuarios = User::pluck('nombre', 'id');
        //        return $salas;

        // Obtener días/horarios ocupados para cada sala
        $horariosOcupados = [];
        foreach ($salas as $sala) {
            $horariosOcupados[$sala->id] = $this->getHorariosOcupados($sala);
        }

        return view('reservas.create', [
            'salas' => $salas,
            'usuarios' => $usuarios,
            'horariosOcupados' => $horariosOcupados
        ]);
    }

    protected function getHorariosOcupados(Salas $sala)
    {
        return $sala->horariosReservas()
            ->where('fecha', '>=', now()->format('Y-m-d'))
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('fecha')
            ->map(function ($horarios) {
                return $horarios->map(function ($horario) {
                    return [
                        'inicio' => $horario->hora_inicio,
                        'fin' => $horario->hora_fin,
                        'reserva_id' => $horario->fk_idReserva
                    ];
                });
            });
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
        // 1. Validación básica
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'tipoEvento' => 'required|in:Reunión,Charla,Curso',
            'fk_idSala' => 'required|exists:salas,id',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'date|after_or_equal:today',
            'horas_inicio' => 'required|array|min:1',
            'horas_inicio.*' => 'date_format:H:i',
            'horas_fin' => 'required|array|min:1',
            'horas_fin.*' => 'date_format:H:i|after:horas_inicio.*'
        ]);

        // 2. Validación avanzada
        $validator->after(function ($validator) use ($request) {
            $sala = Salas::findOrFail($request->fk_idSala);
            $timezone = 'America/Caracas';

            foreach ($request->fechas as $index => $fechaStr) {
                // Parsear fechas/horas con zona horaria
                $horaInicio = Carbon::parse("$fechaStr {$request->horas_inicio[$index]}", $timezone);
                $horaFin = Carbon::parse("$fechaStr {$request->horas_fin[$index]}", $timezone);

                // En el validador after(), modificar las comparaciones de horario:
                if ($horaInicio->format('H:i') < Carbon::parse($sala->horario_inicio)->format('H:i')) {
                    $validator->errors()->add(
                        "horas_inicio.$index",
                        "La hora de inicio debe ser después de las " . Carbon::parse($sala->horario_inicio)->format('H:i')
                    );
                }

                if ($horaFin->format('H:i') > Carbon::parse($sala->horario_fin)->format('H:i')) {
                    $validator->errors()->add(
                        "horas_fin.$index",
                        "La hora de fin debe ser antes de las " . Carbon::parse($sala->horario_fin)->format('H:i')
                    );
                }

                // Validar disponibilidad de sala
                if (!Reservas::salaDisponible(
                    $sala->id,
                    $fechaStr,
                    $request->horas_inicio[$index],
                    $request->horas_fin[$index]
                )) {
                    $validator->errors()->add(
                        "fechas.$index",
                        "La sala ya tiene una reserva en este horario"
                    );
                }

                // Validar conflictos de participantes
                $participantesConflictivos = User::whereIn('id', $request->participantes)
                    ->whereHas('reservas.reserva_horarios', function ($query) use ($fechaStr, $horaInicio, $horaFin) {
                        $query->where('fecha', $fechaStr)
                            ->where(function ($q) use ($horaInicio, $horaFin) {
                                $q->whereTime('hora_inicio', '<', $horaFin->format('H:i:s'))
                                    ->whereTime('hora_fin', '>', $horaInicio->format('H:i:s'));
                            });
                    })
                    ->pluck('nombre')
                    ->toArray();

                if (!empty($participantesConflictivos)) {
                    $validator->errors()->add(
                        "participantes",
                        "Conflictos de horario: " . implode(', ', $participantesConflictivos)
                    );
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Crear la reserva principal
            $reserva = new Reservas();
            $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'fk_idSala']));
            $reserva->fk_idUsuario = Auth::id();
            $reserva->save();

            // Guardar participantes
            $participantesData = collect($request->participantes)->map(function ($usuarioId) use ($reserva) {
                return [
                    'fk_idReserva' => $reserva->id,
                    'fk_idUsuario' => $usuarioId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            Participantes::insert($participantesData);

            // Guardar horarios
            $horariosData = [];
            $fechaInicioBase = null;
            if (!empty($request->fechas)) {
                $fechaInicioBase = Carbon::parse($request->fechas[0]); // Tomamos la primera fecha como base
            }
            if ($fechaInicioBase && $request->has('dias') && is_array($request->dias) && $request->filled('repeticion') && $request->filled('temporalidad')) {
                $repeticion = (int) $request->repeticion;
                $temporalidad = (int) $request->temporalidad;

                for ($i = 0; $i < $repeticion; $i++) {
                    foreach ($request->dias as $diaSemana) {
                        $diaIndice = null;
                        switch (strtolower($diaSemana)) {
                            case 'lunes':
                                $diaIndice = 1;
                                break;
                            case 'martes':
                                $diaIndice = 2;
                                break;
                            case 'miercoles':
                                $diaIndice = 3;
                                break;
                            case 'jueves':
                                $diaIndice = 4;
                                break;
                            case 'viernes':
                                $diaIndice = 5;
                                break;
                            case 'sabado':
                                $diaIndice = 6;
                                break;
                            case 'domingo':
                                $diaIndice = 0;
                                break;
                        }

                        if ($diaIndice !== null) {
                            $fechaEventoBase = $fechaInicioBase->copy();

                            switch ($temporalidad) {
                                case 1: // Semana
                                    $fechaEvento = $fechaEventoBase->addWeeks($i)->startOfWeek()->addDay($diaIndice);
                                    break;
                                case 2: // Mes
                                    $fechaEventoBase->addMonths($i);
                                    $primerDiaDelMes = $fechaEventoBase->startOfMonth();
                                    $fechaEvento = $primerDiaDelMes->copy()->next($diaSemana);
                                    if ($fechaEvento->lt($primerDiaDelMes)) {
                                        $fechaEvento->addWeek();
                                    }
                                    break;
                                case 3: // Año
                                    $fechaEvento = $fechaEventoBase->addYears($i)->startOfYear()->addDayOfYear(($fechaInicioBase->dayOfYear - $fechaInicioBase->dayOfWeek + $diaIndice) % 7);
                                    if ($fechaEvento->lt($fechaEventoBase->startOfYear())) {
                                        $fechaEvento->addWeek();
                                    }
                                    break;
                                default:
                                    continue 2; // Si la temporalidad no es válida, salta a la siguiente iteración de días
                            }

                            // Asegurarse de que la fecha generada sea igual o posterior a la fecha de inicio base
                            if ($fechaEvento->gte($fechaInicioBase)) {
                                // Si solo hay una hora de inicio y fin, usar esas para todas las fechas repetidas
                                if (count($request->horas_inicio) === 1 && count($request->horas_fin) === 1) {
                                    $horariosData[] = [
                                        'fecha' => $fechaEvento->toDateString(),
                                        'hora_inicio' => $request->horas_inicio[0],
                                        'hora_fin' => $request->horas_fin[0],
                                        'fk_idReserva' => $reserva->id,
                                        'fk_idSala' => $request->fk_idSala,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];
                                } else {
                                    // Si hay múltiples horas, asumimos que corresponden al mismo índice de la fecha base
                                    foreach ($request->fechas as $j => $fechaBase) {
                                        if ($fechaBase === $request->fechas[0]) {
                                            $horariosData[] = [
                                                'fecha' => $fechaEvento->toDateString(),
                                                'hora_inicio' => $request->horas_inicio[$j] ?? null,
                                                'hora_fin' => $request->horas_fin[$j] ?? null,
                                                'fk_idReserva' => $reserva->id,
                                                'fk_idSala' => $request->fk_idSala,
                                                'created_at' => now(),
                                                'updated_at' => now()
                                            ];
                                            break; // Usamos solo las horas correspondientes a la primera fecha
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } elseif (!empty($request->fechas) && is_array($request->fechas) && !empty($request->horas_inicio) && is_array($request->horas_inicio) && !empty($request->horas_fin) && is_array($request->horas_fin)) {
                // Si no hay repetición por días, simplemente guarda las fechas y horas proporcionadas
                foreach ($request->fechas as $i => $fecha) {
                    $horariosData[] = [
                        'fecha' => $fecha,
                        'hora_inicio' => $request->horas_inicio[$i] ?? null,
                        'hora_fin' => $request->horas_fin[$i] ?? null,
                        'fk_idReserva' => $reserva->id,
                        'fk_idSala' => $request->fk_idSala,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            HorariosReservas::insert($horariosData);

            DB::commit();

            // Notificar a los participantes
            //            $this->notificarParticipantes($reserva, $request->participantes);

            return redirect()
                ->route('reservas.index')
                ->with('success', 'Reserva creada exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la reserva: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error inesperado al crear la reserva. Por favor intente nuevamente.']);
        }
    }

    protected function notificarParticipantes($reserva, $participantesIds)
    {
        try {
            $participantes = User::whereIn('id', $participantesIds)->get();

            \Illuminate\Support\Facades\Notification::send($participantes, new ReservaCreadaNotification($reserva));

            // También puedes enviar correos o notificaciones push aquí
        } catch (\Exception $e) {
            Log::error('Error al notificar participantes: ' . $e->getMessage());
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
        $reserva = Reservas::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'tipoEvento' => 'required|in:Reunión,Charla,Curso',
            'fk_idSala' => [
                'required',
                'exists:salas,id',
                function ($attribute, $value, $fail) use ($reserva) {
                    // Solo validar estado si se cambia la sala
                    if ($value != $reserva->fk_idSala) {
                        if (!Salas::where('id', $value)->where('status', 'Habilitada')->exists()) {
                            $fail('La sala seleccionada no está disponible para reservas.');
                        }
                    }
                }
            ],
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'required|date|after_or_equal:today',
            'horas_inicio' => 'required|array|min:1',
            'horas_inicio.*' => 'required|date_format:H:i',
            'horas_fin' => 'required|array|min:1',
            'horas_fin.*' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $horaInicio = $request->horas_inicio[$index] ?? null;

                    if ($horaInicio && strtotime($value) <= strtotime($horaInicio)) {
                        $fail('La hora de fin debe ser posterior a la hora de inicio.');
                    }

                    $sala = Salas::find($request->fk_idSala);
                    if ($sala) {
                        if (strtotime($value) > strtotime($sala->horario_fin)) {
                            $fail('La hora de fin excede el horario disponible de la sala.');
                        }
                        if (strtotime($horaInicio) < strtotime($sala->horario_inicio)) {
                            $fail('La hora de inicio es antes del horario disponible de la sala.');
                        }
                    }
                }
            ]
        ], [
            'titulo.required' => 'El título de la reserva es obligatorio.',
            'titulo.max' => 'El título no debe exceder los 255 caracteres.',
            'descripcion.required' => 'Debe ingresar una descripción para la reserva.',
            'descripcion.max' => 'La descripción no debe exceder los 1000 caracteres.',
            'tipoEvento.required' => 'Debe seleccionar un tipo de evento.',
            'fk_idSala.required' => 'Debe seleccionar una sala para la reserva.',
            'fk_idSala.exists' => 'La sala seleccionada no es válida.',
            'participantes.required' => 'Debe seleccionar al menos un participante.',
            'participantes.min' => 'Debe agregar al menos un participante.',
            'participantes.*.exists' => 'Uno o más participantes no son válidos.',
            'fechas.required' => 'Debe seleccionar al menos una fecha.',
            'fechas.min' => 'Debe agregar al menos una fecha.',
            'fechas.*.date' => 'Una o más fechas no son válidas.',
            'fechas.*.after_or_equal' => 'No se pueden seleccionar fechas pasadas.',
            'horas_inicio.required' => 'Las horas de inicio son obligatorias.',
            'horas_inicio.min' => 'Debe agregar al menos una hora de inicio.',
            'horas_inicio.*.date_format' => 'El formato de la hora de inicio no es válido.',
            'horas_fin.required' => 'Las horas de fin son obligatorias.',
            'horas_fin.min' => 'Debe agregar al menos una hora de fin.',
            'horas_fin.*.date_format' => 'El formato de la hora de fin no es válido.',
        ]);

        $validator->after(function ($validator) use ($request, $reserva) {
            if ($request->has('fk_idSala') && $request->has('fechas')) {
                $sala = Salas::find($request->fk_idSala);

                foreach ($request->fechas as $index => $fecha) {
                    $horaInicio = $request->horas_inicio[$index] ?? null;
                    $horaFin = $request->horas_fin[$index] ?? null;

                    if ($sala && $horaInicio && $horaFin) {
                        $existeReserva = HorariosReservas::with('reserva')
                            ->where('fk_idSala', $request->fk_idSala)
                            ->where('fecha', $fecha)
                            ->where('fk_idReserva', '!=', $reserva->id)
                            ->where(function ($query) use ($horaInicio, $horaFin) {
                                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                                    ->orWhere(function ($q) use ($horaInicio, $horaFin) {
                                        $q->where('hora_inicio', '<=', $horaInicio)
                                            ->where('hora_fin', '>=', $horaFin);
                                    });
                            })
                            ->get();

                        if ($existeReserva->isNotEmpty()) {
                            $reservaConflictiva = strtoupper($existeReserva[0]->reserva->titulo);
                            $validator->errors()->add(
                                "fechas.$index",
                                "La sala ya está reservada para el $fecha entre $horaInicio y $horaFin con la reserva: $reservaConflictiva"
                            );
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'fk_idSala']));
            $reserva->save();

            // Actualizar participantes
            $reserva->participantes_reservas()->delete();
            $participantesData = collect($request->participantes)->map(function ($usuarioId) use ($reserva) {
                return [
                    'fk_idReserva' => $reserva->id,
                    'fk_idUsuario' => $usuarioId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();
            Participantes::insert($participantesData);

            // Actualizar horarios
            $reserva->reserva_horarios()->delete();
            $horariosData = [];
            foreach ($request->fechas as $i => $fecha) {
                $horariosData[] = [
                    'fecha' => $fecha,
                    'hora_inicio' => $request->horas_inicio[$i],
                    'hora_fin' => $request->horas_fin[$i],
                    'fk_idReserva' => $reserva->id,
                    'fk_idSala' => $request->fk_idSala,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            HorariosReservas::insert($horariosData);

            DB::commit();

            return redirect()
                ->route('reservas.index')
                ->with('success', 'Reserva actualizada exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar la reserva: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error inesperado al actualizar la reserva. Por favor intente nuevamente.']);
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
