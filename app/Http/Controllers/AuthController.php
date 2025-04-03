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

        // VERIFICAR LDAP
        if (true) { // COLOCAR EN FALSE SI NO SE CUENTA CON EL LDAP
            $conexion = ldap_connect(env('LDAP_HOST'));
            ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conexion, LDAP_OPT_REFERRALS, 0);
            // dd($conexion, "{$credentials['username']}@" . env('LDAP_DOMAIN'), $credentials['password']);
            if (ldap_bind($conexion, "{$credentials['username']}@" . env('LDAP_DOMAIN'), $credentials['password'])) {
                $user=User::where('username',$credentials['username'])->get();
//                $user = User::obtener($credentials['username'], 'username'); // CONSULTA USUARIO EN MYSQL

                if (!$user==false) { // SI NO EXISTE EL USUARIO EN LA BASE DE DATOS LOCAL, LO CREA

                    $consulta = ldap_search($conexion, env('LDAP_DN'), "(samaccountname={$credentials['username']})");
                    $data = ldap_get_entries($conexion, $consulta);
                    User::crearUsuario(new Request([
                        'username' => $data[0]['samaccountname'][0],
                        'password' => $credentials['password'],
                        'apellido' => $data[0]['sn'][0],
                        'nombre' => $data[0]['givenname'][0],
                        'email' => $data[0]['mail'][0]
                    ]));
                } else { // SI NO ACTUALIZA LA CONTRASEÑA
                    User::actualizarUsuario($user['id'], new Request([
                        'password' => $credentials['password']
                    ]));
                }
                ldap_close($conexion); // CERRAR CONECCIÓN LDAP
            }
        }

        if (Auth::attempt($credentials)) {
            return redirect()->route('salas.index');
        }

        return redirect()->back()->withErrors(['error' => 'Credenciales incorrectas.']);
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}
