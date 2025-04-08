<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HorariosReservas extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'horario',
        'fk_idReserva',
    ];
    protected $casts = [
        'horario' => 'datetime',
    ];
    public function reserva()
    {
        return $this->hasOne(Reservas::class, 'id', 'fk_idReserva');
    }

}
