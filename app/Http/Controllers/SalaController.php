<?php

namespace App\Http\Controllers;

use App\Models\Sala;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SalaController extends Controller {
    /**
     * Muestre una lista del recurso.
     */
    public function index() {
        return view('salas/index');
    }

    /**
     * Muestre el formulario para crear un nuevo recurso.
     */
    public function create() {
        return view('salas/nuevo');
    }

    /**
     * Almacene un recurso recién creado en almacenamiento.
     */
    public function store(Request $request) {
        // Validar los datos enviados en la solicitud
        $request->validate([
            'nombre' => 'required|string|id|unique:salas',
            'ubicacion' => 'required|string',
            'capacidad' => 'required|string',
            'estado' => 'required|string',
            'horario' => 'required|string',


        ]);
        $guardar = Sala::crearSala($request);
        if ($guardar) {
            return response()->json(['mensaje' => 'listo']);

            // return response(['status' => 201, 'mensaje' => 'Guardado correctamente']);
        } else {
            return response(['status' => 400, 'mensaje' => 'Error al guardado']);
        }
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show($id) {

        $sala = Sala::obtenerPorId($id);
        if ($sala) {
            return view('salas/ver', [
                'sala' => $sala
            ]);
        } else {
            abort(404, 'Sala no encontrada');
        }
    }

    /**
     * Muestre el formulario para editar el recurso especificado.
     */
    public function edit($id) {
        $sala = Sala::obtenerPorId($id);
        if ($sala) {
            return view('salas/editar', [
                'sala' => $sala
            ]);
        } else {
            abort(404, 'Sala no encontrada');
        }
    }
    
    /**
     * Elimine el recurso especificado del almacenamiento.
     */
    public function destroy($id) {
        $sala = Sala::obtenerPorId($id);
        if ($sala) {
            return view('salas/eliminar', [
                'sala' => $sala
            ]);
        } else {
            abort(404, 'Sala no encontrada');
        }
    }
}
