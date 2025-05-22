<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Log;

class InicioController extends Controller
{

    public function inicio()
    {
        $salas = $this->getSalasForCalendar();
        $user = User::find(Auth::user()->id);
        $reservasDelUsuario = $user->reservas->load('reserva_horarios'); // Cargar los horarios de todas las reservas del usuario

        $eventosParaCalendario = [];

        foreach ($reservasDelUsuario as $reserva) {
            foreach ($reserva->reserva_horarios as $horario) {
                $eventosParaCalendario[] = [
                    'title' => $reserva->titulo,
                    'start' => $horario->fecha->toDateString() . ' ' . $horario->hora_inicio,
                    'end' => $horario->fecha->toDateString() . ' ' . $horario->hora_fin,
                    // Agrega aquí cualquier otra información que necesites para tu calendario
                ];
            }
        }
       // return $eventosParaCalendario;
        return view('inicio', [
            'salas' => $salas,
            'eventos' => $eventosParaCalendario
        ]);
    }

    public function getSalasForCalendar()
    {
        // 1. Obtener los datos de las salas
        // Puedes añadir filtros aquí si solo quieres algunas salas, e.g., Salas::where('status', 'Habilitada')->get();
        $salas = Salas::all();

        // 2. Transformar la colección usando el método map()
        $fullCalendarEvents = $salas->map(function ($sala) {

            $hoy = Carbon::now()->format(format: 'Y-m-d');
            // Combinar la fecha de hoy con los horarios de inicio y fin de la sala
            $startDateTime = $hoy . ' ' . $sala->horario_inicio;
            $endDateTime = $hoy . ' ' . $sala->horario_fin;
            try {
                $startTime = Carbon::parse($sala->horario_inicio);
                $endTime = Carbon::parse($sala->horario_fin);
                if ($endTime->lt($startTime)) {
                    // Si el fin es antes que el inicio, asume que es al día siguiente
                    $endDateTime = Carbon::parse($hoy)->addDay()->format('Y-m-d') . ' ' . $sala->horario_fin;
                }
            } catch (\Exception $e) {
            }
            // --- Fin Manejo Medianoche ---


            // Decodificar el JSON de atributos (si está almacenado como string JSON)
            // El 'true' al final convierte el objeto JSON a un array asociativo de PHP

            //  $atributosArray = json_decode($sala->atributos, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $atributosArray = []; // Asignar un array vacío si hay un error de decodificación

            }


            // Crear el objeto evento para FullCalendar
            return [
                'id' => $sala->id, // Usar el ID de la sala como ID del evento
                'title' => 'Sala: ' . $sala->nombre . ' (' . $sala->ubicacion . ')', // Título del evento
                'start' => $startDateTime, // Fecha y hora de inicio (formato YYYY-MM-DD HH:mm:ss o compatible con ISO 8601)
                'end' => $endDateTime,     // Fecha y hora de fin
                'allDay' => false,

                'extendedProps' => [
                    'ubicacion' => $sala->ubicacion,
                    'capacidad' => $sala->capacidad,
                    'status' => $sala->status,
                    //'atributos' => $atributosArray, // El array de atributos decodificado
                    // Agrega cualquier otro dato de la sala que necesites en el frontend
                ],

            ];
        });

        // 3. Devolver la colección transformada como respuesta JSON
        // Laravel convierte automáticamente las colecciones a arrays JSON.
        return response()->json($fullCalendarEvents);

        // O si prefieres asegurarte de que sea un array PHP nativo antes de enviar (raramente necesario para JSON):
        // return response()->json($fullCalendarEvents->toArray());
    }
}
