<?php

namespace App\Http\Controllers;

use App\Models\Salas;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Muestre una lista del recurso.
     */
    public function create()
    {
        return view('usuarios.nuevo');
    }

    public function index()
    {
//        dd('aqui');
        $user = User::activos()->get();
        return view('usuarios.index')->with(['users' => $user]);
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
    public function store(Request $request)
    {
//        dd($request->all());
        // Validar los datos enviados en la solicitud
        $request->validate([
            'username' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
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
    public function show($id)
    {
        try {
            $usuario = User::findOrFail($id);
            return view('users.edit')->with(['usuario' => $usuario]);
        } catch (ModelNotFoundException $e) {
            Log::error('Usuario no encontrado: ' . $e->getMessage());
            return redirect()->route('users.index')->withErrors(['error' => 'No se encontró el usuario.']);
        } catch (\Exception $e) {
            Log::error('Error al obtener el usuario: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al cargar el usuario.']);
        }
    }


    /**
     * Muestre el formulario para editar el recurso especificado.
     */
    public function edit($id)
    {
//        $usuario = User::obtenerPorId($id);
//        if ($usuario) {
//            return view('usuarios/editar', [
//                'usuario' => $usuario
//            ]);
//        } else {
//            abort(404, 'Usuario no encontrado');
//        }
    }

    /**
     * Elimine el recurso especificado del almacenamiento.
     */
    public function destroy($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->estatus = 0; //inactivar el usuario
            $usuario->save();
            return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (ModelNotFoundException $e) {
            Log::error('Usuario no encontrado: ' . $e->getMessage());
            return redirect()->route('users.index')->withErrors(['error' => 'No se encontró el usuario.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el usuario: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Hubo un problema al eliminar el usuario.']);
        }
    }

    public function sync()
    {
        // Sincronizar usuarios desde una fuente externa - LDAP
        Session::flash('success', 'Usuarios sincronizados exitosamente.');
        return redirect()->route('users.index');
    }


}
