<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('/');
        }

        return redirect()->back()->withErrors(['error' => 'Credenciales incorrectas.']);
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}

// public function verificar()
// 	{		
	
// 		$errores = [];
// 		if ($_SERVER['REQUEST_METHOD']=="POST") {
// 			$usuario = $_POST["usuario"]??"";
// 			$clave = $_POST["clave"] ?? "";
// 			$recordar = isset($_POST["recordar"])?"on":"off";
			
// 			if(empty($usuario)){
// 				$errores[] = "Debe ingresar el usuario";
// 			}
// 			if(empty($clave)){
// 				$errores[] = "Debe ingresar la clave";
// 			}

// 			if (empty($errores)) {
// 				error_reporting(0); // OCULTAR ERRORES
// 				// VERIFICAR LDAP
// 				$host = '10.32.126.130'; 
// 				$dominio = 'QUIRIQUIREGAS.COM'; 
// 				$dn = "OU=QQGAS,DC=QUIRIQUIREGAS,DC=COM";
// 				$conexion = ldap_connect("$host");
// 				ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
// 				ldap_set_option($conexion, LDAP_OPT_REFERRALS, 0);

			
// 				if(ldap_bind($conexion, "$usuario@$dominio", $clave)){
// 					$consulta = ldap_search($conexion, $dn, "(samaccountname=$usuario)");
// 					$data = ldap_get_entries($conexion, $consulta);
					
// 					$user = $this->modelo->getUsuariocorreo_electronico($data[0]['samaccountname'][0]); //Consulta MYSQL
		
// 					if (!$user) {
// 						$user = $this->modelo_usuario->altaUsuario([
// 							'usuario' => $data[0]['samaccountname'][0],
// 							'clave' => password_hash($clave, PASSWORD_DEFAULT),
// 							'apellidos' => $data[0]['sn'][0],
// 							'nombres' => $data[0]['givenname'][0],
// 							'correo' => $data[0]['samaccountname'][0],
// 							'unidad_funcional' => 'Sistemas de infomacion',
// 						]);
					
// 					} else {
// 						// ACTUALIZAR CLAVE	
											
// 					}
// 				} else { // SI NO CON MYSQL
					
// 					$errores = $this->modelo->verificar($usuario, $clave);
// 				}

// 				//recuerdame
// 				$valor = $usuario."|".$clave;
// 				$valor = Helper::encriptar($valor);
// 				if($recordar=="on"){
// 					$fecha = time()+(60*60*24*7);
// 				} else {
// 					$fecha = time() - 1;
// 				}
// 				setcookie("datos",$valor,$fecha,RUTA);

// 				ldap_close($conexion); // CERRAR CONECCIÓN LDAP
// 				error_reporting(1); // MOSTRAR ERRORES
// 			}
// 			//Validacion
// 			if (empty($errores)) {
// 				//Iniciamos sesión
// 				$data = $this->modelo->getUsuariocorreo_electronico($usuario);
// 				$sesion = new Sesion();
// 				$sesion->iniciarLogin($data);
// 				//
// 				header("location:".RUTA."tablero");
// 			} else {
// 				//Datos erróneos
// 				$datos = [
// 				  "titulo" => "Login",
// 				  "subtitulo" => "Entrada al sistema",
// 				  "menu" => false,
// 				  "errores" => $errores
// 				];
// 				$this->vista("loginVista",$datos);
// 			}
