<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Controller;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;

class Sala extends Controller
{
    use HasApiTokens, Notifiable;

    protected $table = 'salas'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'estado',
        'horario',
    ];

   // Crear un sala (Crear)
    public static function crearSala(Request $data) {
        return self::create([
            'nombre' => $data->nombre,
            'ubicacion' => $data->ubicacion,
            'capacidad' => $data->capacidad,
            'estado' => $data->estado,
            'hora_inicio' => $data->hora_inicio,
            'hora_fin' => $data->hora_fin,

        ]);
    }

    // Obtener todos los Salas (Leer)
    public static function obtenerTodos() {
        return self::all();
    }

    // Obtener un sala por ID (Leer)
    public static function obtenerPorId($id) {
        return self::find($id);
    }

    // Actualizar un sala existente (Guardar)
    public static function actualizarSala($id, $data) {
        $sala = self::find($id);
        if ($sala) {
            $sala->update($data);
            return $sala;
        }
        return null; // Retorna null si no se encuentra el sala
    }

    // Eliminar un sala (Eliminar)
    public static function eliminarSala($id) {
        $sala = self::find($id);
        if ($sala) {
            $sala->delete();
            return true;
        }
        return false; // Retorna false si no se encuentra el sala
    }
}
