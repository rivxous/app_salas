<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salas;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Reservas;

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
                    ]
                ]
            ],
            'pieData' => $pieData
        ]);
    }
}
