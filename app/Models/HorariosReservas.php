<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HorariosReservas extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'horariosreservas';
    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'fk_idReserva',
    ];
    protected $casts = [
        'fecha' => 'datetime',
    ];
    public function reserva()
    {
        return $this->hasOne(Reservas::class, 'id', 'fk_idReserva');
    }

}
