<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserController extends Controller {
    /**
     * Muestre una lista del recurso.
     */
    public function create()
    {
        return view('usuarios.nuevo');
    }
    public function index() {
        return view('usuarios.index');
    }

    /**
     * Muestre el formulario para crear un nuevo recurso.
     */
    // public function create() {
    //     return view('usuarios/nuevo');
    // }

    /**
     * Almacene un recurso recién creado en almacenamiento.
     */
    public function store(Request $request) {
//        dd($request->all());
        // Validar los datos enviados en la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        $guardar = User::crearUsuario($request);
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

        $usuario = User::obtenerPorId($id);
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
        $usuario = User::obtenerPorId($id);
        if ($usuario) {
            return view('usuarios/editar', [
                'usuario' => $usuario
            ]);
        } else {
            abort(404, 'Usuario no encontrado');
        }
    }

        /**
     * Elimine el recurso especificado del almacenamiento.
     */
    public function destroy($id) {
            $usuario = User::obtenerPorId($id);
            if ($usuario) {
                return view('usuarios/eliminar', [
                    'usuario' => $usuario
                ]);
            } else {
                abort(404, 'Usuario no encontrado');
            }
        }
}
