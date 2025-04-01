<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'userName',
        'name',
        'email',
        'password',
        'apellido',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

//    // Crear un user (Crear)
     public static function crearUsuario(Request $data) {
         return self::create([
             'name' => $data->name,
             'email' => $data->email,
             'password' => Hash::make($data->password), // Cifrar la contraseña
         ]);
     }

    // Obtener todos los usuarios (Leer)
    public static function obtenerTodos() {
        return self::all();
    }

    // Obtener un usuario por ID (Leer)
    public static function obtenerPorId($id) {
        return self::find($id);
    }

    // Actualizar un usuario existente (Guardar)
    public static function actualizarUsuario($id, $data) {
        $user = self::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null; // Retorna null si no se encuentra el usuario
    }

    // Eliminar un usuario (Eliminar)
    public static function eliminarUsuario($id) {
        $user = self::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false; // Retorna false si no se encuentra el usuario
    }

    /**
     * @param string[] $fillable
     * @return User
     */
    public function setFillable(array $fillable): User
    {
        $this->fillable = $fillable;
        return $this;
    }
}
