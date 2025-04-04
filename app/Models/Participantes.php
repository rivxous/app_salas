<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participantes extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $with = ['usuario'];
    protected $fillable = [
        'fk_idReserva',
        'fk_idUsuario',

    ];

    function reserva()
    {
        return $this->hasOne(Reservas::class, 'id', 'fk_idReserva');
    }

    function usuario()
    {
        return $this->hasOne(User::class, 'id', 'fk_idUsuario')->activos();
    }
}
