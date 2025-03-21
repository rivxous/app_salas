<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'usuarios'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'usuario',
        'nombres',
        'apellidos',
        'correo',
    ];

    // Crear un usuario (Create)
    public static function crearUsuario($data) {
        return self::create([
            'usuario' => $data->usuario,
            'nombres' => $data->nombres,
            'apellidos' => $data->apellidos,
            'correo' => $data->correo
        ]);
    }

    // Obtener todos los usuarios (Read)
    public static function obtenerTodos() {
        return self::all();
    }

    // Obtener un usuario por ID (Read)
    public static function obtenerPorId($id) {
        return self::find($id);
    }

    // Actualizar un usuario existente (Update)
    public static function actualizarUsuario($id, $data) {
        $usuario = self::find($id);
        if ($usuario) {
            $usuario->update($data);
            return $usuario;
        }
        return null; // Retorna null si no se encuentra el usuario
    }

    // Eliminar un usuario (Delete)
    public static function eliminarUsuario($id) {
        $usuario = self::find($id);
        if ($usuario) {
            $usuario->delete();
            return true;
        }
        return false; // Retorna false si no se encuentra el usuario
    }
}
