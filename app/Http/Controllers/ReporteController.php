<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salas;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Reservas;
use Illuminate\Support\Facades\DB;


class ReporteController extends Controller
{
    public function index()
    {
        
        // Obtener datos mensuales
        $users = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $salas = Salas::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $reservas = Reservas::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Determinar rango de fechas
        $dates = [
            User::min('created_at'),
            Salas::min('created_at'),
            Reservas::min('created_at'),
            User::max('created_at'),
            Salas::max('created_at'),
            Reservas::max('created_at')
        ];

        $minDate = min(array_filter($dates));
        $maxDate = max(array_filter($dates));

        // Crear período mensual
        $start = Carbon::parse($minDate)->startOfMonth();
        $end = Carbon::parse($maxDate)->endOfMonth();
        $months = CarbonPeriod::create($start, '1 month', $end);

        // Preparar estructura de datos
        $labels = [];
        $userData = [];
        $salaData = [];
        $reservaData = [];

        $userCounts = $users->pluck('count', 'month');
        $salaCounts = $salas->pluck('count', 'month');
        $reservaCounts = $reservas->pluck('count', 'month');

        foreach ($months as $month) {
            $key = $month->format('Y-m');

            $labels[] = $month->translatedFormat('M Y');
            $userData[] = $userCounts[$key] ?? 0;
            $salaData[] = $salaCounts[$key] ?? 0;
            $reservaData[] = $reservaCounts[$key] ?? 0;
        }

        // Datos para gráfico de pastel (último mes)
        $lastMonth = end($labels);
        $pieData = [
            'labels' => ['Usuarios', 'Salas', 'Reservas'],
            'data' => [
                end($userData) ?: 0,
                end($salaData) ?: 0,
                end($reservaData) ?: 0
            ]
        ];

         $salaMasReservada = Reservas::select('fk_idSala', DB::raw('count(*) as total_reservas'))
            ->groupBy('fk_idSala')
            ->orderByDesc('total_reservas')
            ->limit(5) // Puedes ajustar el límite
            ->with('sala')
            ->get();

        // Reporte: Qué usuarios hacen más reservas
        $usuariosMasReservas = Reservas::select('fk_idUsuario', DB::raw('count(*) as total_reservas'))
            ->groupBy('fk_idUsuario')
            ->orderByDesc('total_reservas')
            ->limit(5) // Puedes ajustar el límite
            ->with('usuario_creador_reserva')
            ->get();

        // Reporte: Qué departamento (unidad funcional) hace más reservas
        $departamentosMasReservas = User::join('reservas', 'users.id', '=', 'reservas.fk_idUsuario')
            ->select('departamento', DB::raw('count(*) as total_reservas'))
            ->groupBy('departamento')
            ->orderByDesc('total_reservas')
            ->limit(5) // Puedes ajustar el límite
            ->get();

        // Reporte: Porcentaje de reservas semanal
        $reservasSemanal = Reservas::selectRaw('YEARWEEK(created_at, 1) as semana, COUNT(*) as total_reservas')
            ->groupBy('semana')
            ->orderBy('semana')
            ->get()
            ->map(function ($item) {
                $totalGeneralSemanal = Reservas::whereRaw('YEARWEEK(created_at, 1) = ?', [$item->semana])->count();
                $item->porcentaje = ($totalGeneralSemanal > 0) ? round(($item->total_reservas / $totalGeneralSemanal) * 100, 2) : 0;
                return $item;
            });

        // Reporte: Porcentaje de reservas mensual
        $reservasMensual = Reservas::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total_reservas')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function ($item) {
                $totalGeneralMensual = Reservas::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$item->mes])->count();
                $item->porcentaje = ($totalGeneralMensual > 0) ? round(($item->total_reservas / $totalGeneralMensual) * 100, 2) : 0;
                return $item;
            });

        // Reporte: Porcentaje de reservas anual
        $reservasAnual = Reservas::selectRaw('YEAR(created_at) as año, COUNT(*) as total_reservas')
            ->groupBy('año')
            ->orderBy('año')
            ->get()
            ->map(function ($item) {
                $totalGeneralAnual = Reservas::whereRaw('YEAR(created_at) = ?', [$item->año])->count();
                $item->porcentaje = ($totalGeneralAnual > 0) ? round(($item->total_reservas / $totalGeneralAnual) * 100, 2) : 0;
                return $item;
            });

        return view('reportes.index', [
            'lineData' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Usuarios',
                        'data' => $userData,
                        'backgroundColor' => '#FF6384',
                    ],
                    [
                        'label' => 'Salas',
                        'data' => $salaData,
                        'backgroundColor' => '#36A2EB',
                    ],
                    [
                        'label' => 'Reservas',
                        'data' => $reservaData,
                        'backgroundColor' => '#4BC0C0',
                    ],
                ],
            ],
            'pieData' => $pieData,
            'salaMasReservada' => $salaMasReservada,
            'usuariosMasReservas' => $usuariosMasReservas,
            'departamentosMasReservas' => $departamentosMasReservas,
            'reservasSemanal' => $reservasSemanal,
            'reservasMensual' => $reservasMensual,
            'reservasAnual' => $reservasAnual,
        ]);
    }
}
