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
}
