<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PhpParser\Node\Expr\Cast\Object_;

class AuthController extends Controller {
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        try {
            // Conexión a LDAP
            $conexion = ldap_connect(env('LDAP_HOST'));
            ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conexion, LDAP_OPT_REFERRALS, 0);

            if (!$conexion) {
                return redirect()->back()->withErrors(['error' => 'No se pudo conectar al servidor LDAP.']);
            }

            // Intento de autenticación con LDAP
            if (true) { // COLOCAR EN FALSE SI NO SE CUENTA CON EL LDAP
                if (!@ldap_bind($conexion, "{$credentials['username']}@" . env('LDAP_DOMAIN'), $credentials['password'])) {
                    return redirect()->back()->withErrors(['error' => 'Credenciales LDAP incorrectas. Verifica tu usuario y contraseña.']);
                }

                // Obtener usuario en la base de datos
                $user = User::where('username', $credentials['username'])->first();

                if (!$user) { // Si no existe, lo crea
                    $consulta = ldap_search($conexion, env('LDAP_DN'), "(samaccountname={$credentials['username']})");
                    $data = ldap_get_entries($conexion, $consulta);

                    User::crearUsuario(new Request([
                        'username' => $data[0]['samaccountname'][0],
                        'password' => $credentials['password'],
                        'apellido' => $data[0]['sn'][0],
                        'nombre' => $data[0]['givenname'][0],
                        'email' => $data[0]['mail'][0]
                    ]));
                    ldap_close($conexion);
                } else {
                    User::actualizarUsuario($user->id, new Request([
                        'password' => $credentials['password']
                    ]));
                }
            }

            // Autenticación en Laravel
            if (Auth::attempt($credentials)) {
                return redirect()->route('/');
            }

            return redirect()->back()->withErrors(['error' => 'Credenciales incorrectas.']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error en autenticación: ' . $e->getMessage()]);
        }
    }



    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}
