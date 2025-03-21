<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UsuarioController extends Controller {
    /**
     * Muestre una lista del recurso.
     */
    public function index() {
        return view('usuarios/index');
    }

    /**
     * Muestre el formulario para crear un nuevo recurso.
     */
    public function create() {
        return view('usuarios/nuevo');
    }

    /**
     * Almacene un recurso recién creado en almacenamiento.
     */
    public function store(Request $request) {
    
        $guardar = Usuario::crearUsuario($request);
        if ($guardar) {
            return response()->json(['mensaje'=>'hola'],404);

            // return response(['status' => 201, 'mensaje' => 'Guardado correctamente']);
        } else {
            return response(['status' => 400, 'mensaje' => 'Error al guardado']);
        }
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show($id) {
    
        $usuario = Usuario::obtenerPorId($id);
        if ($usuario) {
            return view('usuarios/ver', [
                'usuario' => $usuario
            ]);
        } else {
            abort(404, 'Usuario no encontrado');
        }
    }

    /**
     * Muestre el formulario para editar el recurso especificado.
     */
    public function edit($id) {
        $usuario = Usuario::obtenerPorId($id);
        if ($usuario) {
            return view('usuarios/editar', [
                'usuario' => $usuario
            ]);
        } else {
            abort(404, 'Usuario no encontrado');
        }
    }

    /**
     * Actualice el recurso especificado en almacenamiento.
     */
    public function update(Request $request, $id) {

        //
    }

    /**
     * Elimine el recurso especificado del almacenamiento.
     */
    public function destroy($id) {
        //
    }
}
