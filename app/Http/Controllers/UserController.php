<?php

namespace App\Http\Controllers;

use App\Models\Salas;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function filterUser($query)
    {
        if($query === "todos"){
            return User::get(['id' , 'nombre']);
        }
       
        $usuarios = User::where('unidad_funcional' , $query)->get(['id' , 'nombre']);

    
        return response()->json($usuarios);
    }
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
      
        // Validar los datos enviados en la solicitud
        /** 
        $request->validate([
          //  'username' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
          //  'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);*/
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
        Log::info("ldap" );
        try {
            $conexion = ldap_connect(env('LDAP_HOST'));
            ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conexion, LDAP_OPT_REFERRALS, 0);

            if (!$conexion) {
                return redirect()->route('usuarios.index')->withErrors(['error' => 'No se pudo conectar al servidor LDAP.']);
            }
            Log::info("paso la conexion  ldap" );
            /** 
            if (!@ldap_bind($conexion, env('LDAP_USER'), env('LDAP_PASSWORD'))) {
                Log::info("entro en el condicional" );
                return redirect()->route('usuarios.index')->withErrors(['error' => 'Error al autenticarse contra LDAP.']);
            }*/
            Log::info("segunda validacion  ldap" );
            $consulta = ldap_search($conexion, env('LDAP_DN'), filter: '(objectClass=user)');
            Log::info($consulta);
            $entradas = ldap_get_entries($conexion, $consulta);
            Log::info("entradas ldap" );
            Log::info($entradas );
            $users = [];

            for ($i = 0; $i < $entradas['count']; $i++) {
                $entrada = $entradas[$i];

                if (!isset($entrada['samaccountname'][0])) continue;

                $users[] = (object) [
                    'username' => $entrada['samaccountname'][0] ?? '',
                    'nombre' => $entrada['givenname'][0] ?? '',
                    'apellido' => $entrada['sn'][0] ?? '',
                    'unidad_funcinal' => $entrada['department'][0] ?? '',
                    'email' => $entrada['mail'][0] ?? '',
                    'id' => null // Para evitar errores con el botón eliminar
                ];
            }

            ldap_close($conexion);

            Session::flash('success', 'Usuarios sincronizados correctamente desde LDAP.');

            return view('usuarios.index', compact('users'));

        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->withErrors(['error' => 'Error al sincronizar usuarios: ' . $e->getMessage()]);
        }
    }


}
