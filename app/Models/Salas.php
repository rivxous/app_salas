<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salas extends Model {
    use HasFactory;
    use SoftDeletes;
    protected $with=['reservas'];

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'status',
        'horario_inicio',
        'horario_fin'
    ];
    public function scopeActivas($query)
    {
        return $query->where('status', 'Habilitada');
    }
    function reservas()
    {
        return $this->hasMany(Reservas::class, 'fk_idSala', 'id');
    }


}
