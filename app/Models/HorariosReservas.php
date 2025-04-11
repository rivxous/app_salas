<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HorariosReservas extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'horarios_reservas';
    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'fk_idReserva',
        'fk_idSala',
    ];
    protected $casts = [
        'fecha' => 'datetime',
    ];
    public function reserva()
    {
        return $this->belongsTo(Reservas::class, 'fk_idReserva')->without('reserva_horarios');
    }
    public function sala()
    {
        return $this->belongsTo(Reservas::class, 'fk_idReserva');
    }

}
