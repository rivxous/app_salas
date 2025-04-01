<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservas extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $with = ['sala', 'usuario_creador'];

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipoEvento',
        'horario',
        'fk_idSala',
        'fk_idUsuario',
    ];

    public function sala()
    {
        return $this->hasOne(Salas::class, 'id', 'fk_idSala');
    }

    public function usuario_creador()
    {
        return $this->hasOne(User::class, 'id', 'fk_idUsuario');
    }
}
