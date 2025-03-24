<?php

namespace App\Http\Controllers;

use App\Models\Reservas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ReservasController extends Controller
{
    public function index() {
        $reservas = Reservas::orderBy('id', 'desc')->get();

        return view('reservas.index', ['reservas' => $reservas]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('reservas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse 
    {
        $request->validate([
            'nombre' => [
                'required',
                Rule::unique('reservas')->whereNull('deleted_at') // Ignora registros "soft deleted"
            ],
            'titulo' => 'required',
            'descripcion' => 'required|numeric',
            'tipoEvento' => 'required',
            'horario' => 'required',
        ], [
            'titulo.required' => 'El campo :attribute es requerido.',
            'titulo.unique' => 'El nombre ingresado ya existe. Por favor, elige otro.',
            'descripcion.required' => 'El campo :attribute es requerido.',
            'tipoEvento.required' => 'El campo :attribute es requerido.',
            'horario.required' => 'El campo :attribute debe ser numérico.',
        ]);
    
        Reservas::create($request->all());
        return redirect()->route('salas.index')->with('success', 'Reserva creada exitosamente!')->withInput();
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id) {
        $reservas = Reservas::find($id);

        if ($reservas) {
            return view('reservas.edit')->with(['reservas' => $reservas]);
            //    return response()->json($sala);
        } else {
            return response()->json(['msj' => 'No se encontro la reserva'], 404);
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
            'titulo' => 'required',
            'descripcion' => 'required|numeric',
            'tipoEvento' => 'required',
            'horario' => 'required',
        ], [
            'titulo.required' => 'El campo :attribute es requerido.',
            'titulo.unique' => 'El nombre ingresado ya existe. Por favor, elige otro.',
            'descripcion.required' => 'El campo :attribute es requerido.',
            'tipoEvento.required' => 'El campo :attribute es requerido.',
            'horario.required' => 'El campo :attribute debe ser numérico.',
        ]);


        // Buscar el modelo por su ID
        $reserva = Reservas::findOrFail($id);

        // Actualizar los datos del modelo
        $reserva->update($validatedData);

        // Redireccionar con un mensaje de éxito
        return redirect()->route('reserva.index')->with('success', 'Reserva actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        // Buscar la sala por su ID
        $reserva = Reservas::findOrFail($id);

        // Eliminar la sala
        $reserva->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada correctamente.');
    }
}
