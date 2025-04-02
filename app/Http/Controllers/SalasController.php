<?php

namespace App\Http\Controllers;

use App\Models\Salas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class SalasController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $salas = Salas::orderBy('id', 'desc')->get();
            return view('salas.index', ['salas' => $salas]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las salas: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar las salas.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
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
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'nombre' => ['required', Rule::unique('salas')->whereNull('deleted_at')],
            'ubicacion' => 'required',
            'capacidad' => 'required|numeric',
            'status' => 'required',
            'horario_inicio' => 'required',
            'horario_fin' => 'required'
        ]);

        try {
            Salas::create($request->all());
            return redirect()->route('salas.index')->with('success', 'Sala creada exitosamente!')->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear la sala: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al crear la sala.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
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
    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'ubicacion' => 'required',
            'capacidad' => 'required|integer|min:1',
            'status' => 'required|in:Habilitada,Inhabilitada',
            'horario_inicio' => 'required',
            'horario_fin' => 'required',
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        try {
            $sala = Salas::findOrFail($id);
            $sala->delete();
            return redirect()->route('salas.index')->with('success', 'Sala eliminada correctamente.');
        } catch (ModelNotFoundException $e) {
            Log::error('Sala no encontrada: ' . $e->getMessage());
            return redirect()->route('salas.index')->withErrors(['error' => 'No se encontró la sala.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la sala: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al eliminar la sala.']);
        }
    }
}
