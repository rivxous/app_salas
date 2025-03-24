<?php

namespace App\Http\Controllers;

use App\Models\Salas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalasController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $salas = Salas::orderBy('id', 'desc')->get();

        return view('salas.index', ['salas' => $salas]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('salas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse 
    {
        $request->validate([
            'nombre' => [
                'required',
                Rule::unique('salas')->whereNull('deleted_at') // Ignora registros "soft deleted"
            ],
            'ubicacion' => 'required',
            'capacidad' => 'required|numeric',
            'status' => 'required',
            'horario_inicio' => 'required',
            'horario_fin' => 'required'
        ], [
            'nombre.required' => 'El campo :attribute es requerido.',
            'nombre.unique' => 'El nombre ingresado ya existe. Por favor, elige otro.',
            'ubicacion.required' => 'El campo :attribute es requerido.',
            'capacidad.required' => 'El campo :attribute es requerido.',
            'capacidad.numeric' => 'El campo :attribute debe ser numérico.',
            'status.required' => 'El campo :attribute es requerido.',
            'horario_inicio.required' => 'El campo :attribute es requerido.',
            'horario_fin.required' => 'El campo :attribute es requerido.',
        ]);
    
        Salas::create($request->all());
        return redirect()->route('salas.index')->with('success', 'Sala creada exitosamente!')->withInput();
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id) {
        $sala = Salas::find($id);

        if ($sala) {
            return view('salas.edit')->with(['sala' => $sala]);
            //    return response()->json($sala);
        } else {
            return response()->json(['msj' => 'No se encontro la sala'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
 
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {

        $validatedData = $request->validate([
            // 'nombre' => 'required|string|unique:salas,nombre,' . $id, // Valida la unicidad excluyendo el registro actual
            'ubicacion' => 'required',
            'capacidad' => 'required|integer|min:1',
            'status' => 'required|in:Habilitada,Inhabilitada',
            'horario_inicio' => 'required',
            'horario_fin' => 'required',
        ], [
            
            'ubicacion.required' => 'El campo :attribute es requerido.',
            'capacidad.required' => 'El campo :attribute es requerido.',
            'capacidad.numeric' => 'El campo :attribute debe ser numérico.',
            'status.required' => 'El campo :attribute es requerido.',
            'horario_inicio.required' => 'El campo :attribute es requerido.',
            'horario_fin.required' => 'El campo :attribute es requerido.',
        ]);


        // Buscar el modelo por su ID
        $sala = Salas::findOrFail($id);

        // Actualizar los datos del modelo
        $sala->update($validatedData);

        // Redireccionar con un mensaje de éxito
        return redirect()->route('salas.index')->with('success', 'Sala actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        // Buscar la sala por su ID
        $sala = Salas::findOrFail($id);

        // Eliminar la sala
        $sala->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('salas.index')->with('success', 'Sala eliminada correctamente.');
    }
}
