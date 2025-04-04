<?php

namespace App\Http\Controllers;

use App\Models\Participantes;
use App\Models\Reservas;
use App\Models\Salas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservasController extends Controller
{
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

                    $reserva->participantes = $participantes;
                    unset($reserva->participantes_reservas);
                    return $reserva;
                });

            return view('reservas.index', ['reservas' => $reservas]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las reservas: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar las reservas.']);
        }
    }



    public function create()
    {
        try {
            $salas = Salas::pluck('nombre', 'id');
            $usuarios = User::pluck('nombre', 'id');
//            dd($usuarios);

            return view('reservas.create', compact('salas', 'usuarios'));
        } catch (\Exception $e) {
            Log::error('Error al cargar el formulario de creación: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar el formulario de creación.']);
        }
    }

    public function store(Request $request)
    {
//        dd($request->all());
        $request->validate([
            'titulo' => 'required|unique:reservas,titulo,NULL,id,deleted_at,NULL',
            'descripcion' => 'required',
            'tipoEvento' => 'required',
            'horario' => 'required|date',
            'fk_idSala' => 'required|exists:salas,id',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
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

            return redirect()->route('reservas.index')->with('success', 'Reserva creada exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear la reserva: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al crear la reserva.']);
        }
    }

    public function show($id)
    {
        try {
            $reserva = Reservas::findOrFail($id);
            $salas = Salas::pluck('nombre', 'id');
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
        $request->validate([
            'titulo' => 'required',
            'descripcion' => 'required|string',
            'tipoEvento' => 'required',
            'horario' => 'required|date',
            'fk_idSala' => 'required|exists:salas,id',
            'participantes' => 'required|array|min:1',
            'participantes.*' => 'exists:users,id',
        ]);

        try {
            // Buscar la reserva
            $reserva = Reservas::findOrFail($id);

            // Actualizar los datos de la reserva
            $reserva->fill($request->only(['titulo', 'descripcion', 'tipoEvento', 'horario', 'fk_idSala']));
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

            return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
        } catch (ModelNotFoundException $e) {
            Log::error('Reserva no encontrada: ' . $e->getMessage());
            return redirect()->route('reservas.index')->withErrors(['error' => 'No se encontró la reserva.']);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la reserva: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al actualizar la reserva.']);
        }
    }


    public function destroy($id)
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
