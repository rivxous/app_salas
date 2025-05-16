<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use SoftDeletes;

    protected $table = 'users'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'nombre',
        'apellido',
        'ubicacion',
        'unidad_funcional',
        'departamento',
        'area',
        'cargo',
        'email',
        'rol',
        'password',
        'estatus'  // 1 activo, 0 inactivo
    ];

    protected $hidden = [
        'remember_token',
    ];
// Añade este método al modelo User
    public function tieneConflictoHorario($fecha, $horaInicio, $horaFin)
    {
        return Participantes::where('fk_idUsuario', $this->id)
            ->whereHas('reserva.reserva_horarios', function($query) use ($fecha, $horaInicio, $horaFin) {
                $query->where('fecha', $fecha)
                    ->where(function($q) use ($horaInicio, $horaFin) {
                        $q->where('hora_inicio', '<', $horaFin)
                            ->where('hora_fin', '>', $horaInicio);
                    });
            })
            ->exists();
    }
    public function scopeActivos($query)
    {
        return $query->where('estatus', 1);
    }
    public function getFullNameAttribute($query)
    {
        return strtoupper("$this->apellido, $this->nombre");
    }
    public function scopeInactivos($query)
    {
        return $query->where('estatus', 0);
    }

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //    // Crear un user (Crear)
    public static function crearUsuario(Request $data)
    {
        $userData = $data->only((new self())->getFillable());

        // Asegura que la contraseña sea cifrada antes de crear el usuario
        if (!empty($data->input('password'))) {
            $userData['password'] = Hash::make($data->input('password'));
        }

        return self::create($userData);
    }


    // Obtener todos los usuarios (Leer)
    public static function obtenerTodos()
    {
        return self::all();
    }

    public static function obtener($username, string $column = 'usuername')
    {
        if (!in_array($column, (new self())->getFillable())) {
            throw new \InvalidArgumentException("La columna '{$column}' no está en fillable y no puede ser consultada.");
        }
        return self::where($column, $username)->first();
    }

    // Obtener un usuario por ID (Leer)
    public static function obtenerPorId($id)
    {
        return self::find($id);
    }

    public static function actualizarUsuario($id, Request $data)
    {
        $user = self::find($id);
        if ($user) {
            $userData = $data->only(array_merge((new self())->getFillable(), ['password']));

            // Verifica si se ha pasado una nueva contraseña
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']); // Cifra la nueva contraseña
            } else {
                unset($userData['password']); // Evita sobreescribir la contraseña si no se pasa una nueva
            }

            // Actualiza los datos incluyendo 'password' si está presente
            $user->update($userData);

            return $user;
        }
        return null; // Retorna null si el usuario no existe
    }


    // Eliminar un usuario (Eliminar)
    public static function eliminarUsuario($id)
    {
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

//    public function getAuthIdentifierName()
//    {
//        return 'id';
//    }
}
