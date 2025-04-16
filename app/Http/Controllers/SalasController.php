<?php

namespace App\Http\Controllers;

use App\Models\Reservas;
use App\Models\Salas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class SalasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listarTodas()
    {
        try {
            $salas = Salas::select('id', 'nombre', 'ubicacion', 'capacidad', 'status', 'horario_inicio', 'horario_fin')
                ->activas()
                ->without('reservas')
                ->orderBy('ubicacion', 'asc')
                ->get();
            return $salas;
        } catch (\Exception $e) {
            Log::error('Error al obtener las salas en el controlador SalasController@listarTodas: ' . $e->getMessage() . ',en la linea:' . $e->getLine());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar las salas.']);
        }
    }

    public function index()
    {
        try {
            $salas = Salas::orderBy('id', 'desc')->get();
//            dd($salas);
            return view('salas.index', ['salas' => $salas]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las salas: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar las salas.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('salas.create');
        } catch (\Exception $e) {
            Log::error('Error al cargar el formulario de creación: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar el formulario de creación.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
//        dd($request->input('atributos'));
        $request->validate([
            'nombre' => ['required', Rule::unique('salas')->whereNull('deleted_at')],
            'ubicacion' => 'required',
            'capacidad' => 'required|numeric',
            'status' => 'required',
            'horario_inicio' => 'required',
            'horario_fin' => 'required',
            'atributos' => 'required',
        ]);

        try {
//            Salas::create($request->all());
            $sala = new Salas($request->all());
            $sala->atributos = json_encode($request->atributos); // Laravel lo convierte automáticamente a JSON
            $sala->save();

            return redirect()->route('salas.index')->with('success', 'Sala creada exitosamente!')->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear la sala: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al crear la sala.']);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $sala = Salas::findOrFail($id);
            return view('salas.edit')->with(['sala' => $sala]);
        } catch (ModelNotFoundException $e) {
            Log::error('Sala no encontrada: ' . $e->getMessage());
            return redirect()->route('salas.index')->withErrors(['error' => 'No se encontró la sala.']);
        } catch (\Exception $e) {
            Log::error('Error al obtener la sala: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar la sala.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'ubicacion' => 'required',
            'capacidad' => 'required|integer|min:1',
            'status' => 'required|in:Habilitada,Inhabilitada',
            'horario_inicio' => 'required',
            'horario_fin' => 'required',
            'atributos' => 'required',
        ]);

        try {
            $sala = Salas::findOrFail($id);
            $sala->update($validatedData);
            return redirect()->route('salas.index')->with('success', 'Sala actualizada correctamente.');
        } catch (ModelNotFoundException $e) {
            Log::error('Sala no encontrada: ' . $e->getMessage());
            return redirect()->route('salas.index')->withErrors(['error' => 'No se encontró la sala.']);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la sala: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al actualizar la sala.']);
        }
        $sala->atributos = $request->input('atributos');
        $sala->save();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //se buscan las reservas de la sala
            $reservas = Reservas::where('fk_idSala', $id)->get();
            if (count($reservas) > 0) {
                return redirect()->route('salas.index')->with('error', 'Ya hay reservaciones en la sala');
            } else {
                $sala = Salas::findOrFail($id);
                $sala->delete();
                return redirect()->route('salas.index')->with('success', 'Sala eliminada correctamente.');
            }

        } catch (ModelNotFoundException $e) {
            Log::error('Sala no encontrada: ' . $e->getMessage());
            return redirect()->route('salas.index')->withErrors(['error' => 'No se encontró la sala.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la sala: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al eliminar la sala.']);
        }
    }
}
