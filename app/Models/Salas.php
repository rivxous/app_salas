<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Salas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'salas';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'capacidad',
        'status',
        'horario_inicio',
        'horario_fin',
        'atributos'
    ];

    protected $casts = [
        'horario_inicio' => 'datetime:H:i',
        'horario_fin' => 'datetime:H:i',
        'capacidad' => 'integer',
        'deleted_at' => 'datetime'
    ];

    protected $appends = [
        'horario_disponibilidad',
        'disponibilidad_hoy'
    ];



    /**
     * Relación con las reservas de esta sala
     */
    public function reservas()
    {
        return $this->hasMany(Reservas::class, 'fk_idSala');
    }

    /**
     * Relación con los horarios de reserva (acceso directo)
     */
    public function horariosReservas()
    {
        return $this->hasManyThrough(
            HorariosReservas::class,
            Reservas::class,
            'fk_idSala',
            'fk_idReserva'
        );
    }

    /**
     * Scope para salas activas (habilitadas)
     */
    public function scopeActivas($query)
    {
        return $query->where('status', 'Habilitada');
    }

    /**
     * Scope para salas con capacidad mínima
     */
    public function scopeConCapacidad($query, int $personas)
    {
        return $query->where('capacidad', '>=', $personas);
    }

    /**
     * Verificar disponibilidad para un horario específico
     */


    /**
     * Obtener horarios ocupados para un rango de fechas
     */
    public function horariosOcupados(string $fechaInicio, string $fechaFin): array
    {
        return $this->horariosReservas()
            ->select('fecha', 'hora_inicio', 'hora_fin')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('fecha')
            ->map(function ($horarios) {
                return $horarios->map(function ($horario) {
                    return [
                        'inicio' => $horario->hora_inicio,
                        'fin' => $horario->hora_fin
                    ];
                });
            })
            ->toArray();
    }

    /**
     * Atributo: Horario de disponibilidad formateado
     */
    public function getHorarioDisponibilidadAttribute(): string
    {
        return Carbon::parse($this->horario_inicio)->format('h:i A') . ' - ' .
            Carbon::parse($this->horario_fin)->format('h:i A');
    }

    /**
     * Atributo: Disponibilidad para hoy
     */
    public function getDisponibilidadHoyAttribute(): string
    {
        $hoy = now()->format('Y-m-d');
        $horariosOcupados = $this->horariosOcupados($hoy, $hoy);

        if (empty($horariosOcupados)) {
            return 'Disponible todo el día';
        }

        return 'Parcialmente ocupada';
    }
}
