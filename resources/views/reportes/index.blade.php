@extends('layouts.base')

@section('title', 'Listado de Salas')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <canvas id="lineChart" height="300"></canvas>
            </div>
            <div class="col-md-4">
                <canvas id="pieChart" height="300"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Sala Más Reservada</h2>
                        @if($salaMasReservada->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($salaMasReservada as $sala)
                                    <li class="list-group-item">
                                        <strong>{{ $sala->sala->nombre }}:</strong> {{ $sala->total_reservas }} reservas
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text">No hay datos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Usuarios con Más Reservas</h2>
                        @if($usuariosMasReservas->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($usuariosMasReservas as $usuario)
                                    <li class="list-group-item">
                                         <strong>{{ $usuario->usuario_creador_reserva->name }}:</strong> {{ $usuario->total_reservas }} reservas
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text">No hay datos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Departamentos con Más Reservas</h2>
                        @if($departamentosMasReservas->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($departamentosMasReservas as $departamento)
                                    <li class="list-group-item">
                                        <strong>{{ $departamento->departamento }}:</strong> {{ $departamento->total_reservas }} reservas
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text">No hay datos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Porcentaje de Reservas Semanal</h2>
                        @if($reservasSemanal->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($reservasSemanal as $reserva)
                                    <li class="list-group-item">
                                        Semana: {{ $reserva->semana }} - Total: {{ $reserva->total_reservas }} - Porcentaje: {{ $reserva->porcentaje }}%
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text">No hay datos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Porcentaje de Reservas Mensual</h2>
                        @if($reservasMensual->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($reservasMensual as $reserva)
                                     <li class="list-group-item">
                                        Mes: {{ $reserva->mes }} - Total: {{ $reserva->total_reservas }} - Porcentaje: {{ $reserva->porcentaje }}%
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text">No hay datos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Porcentaje de Reservas Anual</h2>
                        @if($reservasAnual->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($reservasAnual as $reserva)
                                    <li class="list-group-item">
                                         Año: {{ $reserva->año }} - Total: {{ $reserva->total_reservas }} - Porcentaje: {{ $reserva->porcentaje }}%
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="card-text">No hay datos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico lineal
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'bar',
            data: @json($lineData),
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Cantidad de Reservas'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    }
                }
            }
        });

        // Gráfico de pastel
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($pieData['labels']),
                datasets: [{
                    data: @json($pieData['data']),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribución del último mes'
                    }
                }
            }
        });
    </script>
@endsection
