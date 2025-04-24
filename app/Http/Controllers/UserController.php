<?php

namespace App\Http\Controllers;

use App\Models\Salas;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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
        try {
            $conexion = ldap_connect(env('LDAP_HOST'));
            ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conexion, LDAP_OPT_REFERRALS, 0);


            if (!$conexion) {
                return redirect()->route('usuarios.index')->withErrors(['error' => 'No se pudo conectar al servidor LDAP.']);
            }

            if (!@ldap_bind($conexion, env('LDAP_USER') . '@' . env('LDAP_DOMAIN'), env('LDAP_PASSWORD'))) {
                return redirect()->route('usuarios.index')->withErrors(['error' => 'Error al autenticarse contra LDAP.']);
            }
//            $filter = "(sAMAccountName=BARRETODA)";
            $filter = '(objectClass=user)';

            $consulta = ldap_search($conexion, env('LDAP_DN'), $filter);
            $entradas = ldap_get_entries($conexion, $consulta);
//            dd($entradas);


            for ($i = 0; $i < $entradas['count']; $i++) {
                $entrada = $entradas[$i];
//             dd($entrada['samaccountname'][0]);

                if (isset($entrada['samaccountname'][0])) {
                    $cantidad_user = DB::table('users')
                        ->where('username', '=', $entrada['samaccountname'][0])
                        ->count();
//                    dd($cantidad_user);

                    if ($cantidad_user == 0) {
//                        dd($cantidad_user,$entrada['samaccountname'][0]);
                        User::create([
                            'username' => $entrada['samaccountname'][0] ?? '',
                            'nombre' => $entrada['givenname'][0] ?? '',
                            'apellido' => $entrada['sn'][0] ?? '',
                            'unidad_funcinal' => $entrada['department'][0] ?? '',
                            'email' => $entrada['samaccountname'][0].'@'.env('LDAP_DOMAIN') ?? '',
                            'password' => null
                        ]);
                    }
                }

            }
            ldap_close($conexion);

            Session::flash('success', 'Usuarios sincronizados correctamente desde LDAP.');

            $users = User::get();
            return view('usuarios.index', compact('users'));

        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->withErrors(['error' => 'Error al sincronizar usuarios: ' . $e->getMessage()]);
        }
    }


}
