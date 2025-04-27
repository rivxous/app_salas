<!DOCTYPE html>
<html>
<head>
    <title>Notificación de Reserva</title> 
</head>
<body>
    <h1>¡Hola {{ $user->nombre }}!</h1>
    <p>Has sido incluido en una nueva reserva:</p>
    
    <ul>
        <li><strong>Título:</strong> {{ $reserva->titulo }}</li>
        <li><strong>Sala:</strong> {{ $reserva->sala->nombre }}</li>
        <li><strong>Fechas:</strong>
            <ul>
                @foreach($reserva->reserva_horarios as $horario)
                <li>{{ $horario->fecha->format('d/m/Y') }} de {{ $horario->hora_inicio }} a {{ $horario->hora_fin }}</li>
                @endforeach
            </ul>
        </li>
        <li><strong>Descripción:</strong> {{ $reserva->descripcion }}</li>
    </ul>
    
    <p>
        <a href="{{ route('reservas.show', $reserva->id) }}">Ver detalles de la reserva</a>
    </p>
</body>
</html>