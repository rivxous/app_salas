<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservas extends Model {
    use HasFactory;
    use SoftDeletes;
    protected $with=['sala'];

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipoEvento',
        'horario',
        'fk_idSala',
    ];
    public function sala()
    {
        return $this->belongsTo(Salas::class);
    }
}
