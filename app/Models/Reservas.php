<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservas extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $with = ['sala', 'usuario_creador_reserva','participantes_reservas','reserva_horarios'];
    protected $primaryKey = 'id';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipoEvento',
        'horario',
        'fk_idSala',
        'fk_idUsuario',
    ];

    public static function salaDisponible($salaId, $fecha, $horaInicio, $horaFin)
    {
        return !HorariosReservas::where('fk_idSala', $salaId)
            ->where('fecha', $fecha)
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->where(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<', $horaFin)
                        ->where('hora_fin', '>', $horaInicio);
                });
            })
            ->exists();
    }
    public function usuario_creador_reserva()
    {
        return $this->hasOne(User::class, 'id', 'fk_idUsuario')->activos();
    }
    public function reserva_horarios()
    {
        return $this->hasMany(HorariosReservas::class, 'fk_idReserva', 'id');
    }

    public function sala()
    {
        return $this->hasOne(Salas::class, 'id', 'fk_idSala')->without('reservas');
    }

    public function participantes_reservas()
    {
        return $this->hasMany(Participantes::class, 'fk_idReserva', 'id');
    }


    public function tieneConflicto(string $fecha, string $horaInicio, string $horaFin): bool
    {
        return $this->reserva_horarios()
            ->where('fecha', $fecha)
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                    ->orWhere(function($q) use ($horaInicio, $horaFin) {
                        $q->where('hora_inicio', '<=', $horaInicio)
                            ->where('hora_fin', '>=', $horaFin);
                    });
            })
            ->exists();
    }
}
