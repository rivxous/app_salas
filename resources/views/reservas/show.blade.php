@extends('layouts.base')

@section('title', 'Reserva')

@section('content')
<div class="container py-4">
  <div class="card shadow">
      <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Detalles de la Reserva #{{ $reserva->id }}</h3>
      </div>
      
      <div class="card-body">
          <!-- Sección de información básica -->
          <div class="row mb-4">
              <div class="col-md-8">
                  <h2 class="h4">{{ $reserva->titulo }}</h2>
                  <p class="lead">{{ $reserva->descripcion }}</p>
                  
                  <div class="row">
                      <div class="col-md-6">
                          <dl class="row">
                              <dt class="col-sm-5">Tipo de Evento:</dt>
                              <dd class="col-sm-7">{{ $reserva->tipoEvento }}</dd>

                              <dt class="col-sm-5">Estado:</dt>
                              <dd class="col-sm-7">
                                  <span class="badge bg-success">Confirmada</span>
                              </dd>

                              <dt class="col-sm-5">Fecha Creación:</dt>
                              <dd class="col-sm-7">{{ $reserva->created_at->format('d/m/Y H:i') }}</dd>
                          </dl>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Horarios -->
          <div class="mb-5">
              <h4 class="border-bottom pb-2 mb-3">Horarios Reservados</h4>
              <div class="row">
                  @foreach($reserva->reserva_horarios as $horario)
                  <div class="col-md-4 mb-3">
                      <div class="card h-100">
                          <div class="card-body">
                              <h5 class="card-title">
                                  <i class="far fa-calendar-alt me-2"></i>
                                  {{ \Carbon\Carbon::parse($horario->fecha)->isoFormat('dddd D MMMM YYYY') }}
                              </h5>
                              <div class="d-flex justify-content-between align-items-center">
                                  <span class="badge bg-primary">
                                      {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - 
                                      {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                  </span>
                              </div>
                          </div>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>

          <!-- Información de la Sala -->
          <div class="mb-5">
              <h4 class="border-bottom pb-2 mb-3">Detalles de la Sala</h4>
              <div class="row">
                  <div class="col-md-6">
                      <dl class="row">
                          <dt class="col-sm-4">Nombre:</dt>
                          <dd class="col-sm-8">{{ $reserva->sala->nombre }}</dd>

                          <dt class="col-sm-4">Ubicación:</dt>
                          <dd class="col-sm-8">{{ $reserva->sala->ubicacion }}</dd>

                          <dt class="col-sm-4">Capacidad:</dt>
                          <dd class="col-sm-8">{{ $reserva->sala->capacidad }} personas</dd>
                      </dl>
                  </div>
                  
                  <div class="col-md-6">
                      <h5>Equipamiento:</h5>
                      <div class="d-flex flex-wrap gap-2">
                          @foreach(json_decode($reserva->sala->atributos) as $equipo)
                          <span class="badge bg-secondary">{{ $equipo }}</span>
                          @endforeach
                      </div>
                      
                      <div class="mt-3">
                          <h5>Horario Disponible:</h5>
                          <p class="mb-0">{{ $reserva->sala->horario_disponibilidad }}</p>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Participantes -->
          <div class="mb-5">
              <h4 class="border-bottom pb-2 mb-3">Participantes</h4>
              <div class="row row-cols-1 row-cols-md-3 g-4">
                  @foreach($reserva->participantes_reservas as $participante)
                  <div class="col">
                      <div class="card h-100">
                          <div class="card-body">
                              <h5 class="card-title">
                                  <i class="fas fa-user me-2"></i>
                                  {{ $participante->usuario->nombre }} {{ $participante->usuario->apellido }}
                              </h5>
                              <p class="card-text mb-1">
                                  <small class="text-muted">{{ $participante->usuario->cargo }}</small>
                              </p>
                              <p class="card-text mb-0">
                                  <span class="badge bg-info">{{ $participante->usuario->departamento }}</span>
                              </p>
                          </div>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>

          <!-- Información del Creador -->
          <div class="mb-4">
              <h4 class="border-bottom pb-2 mb-3">Responsable de la Reserva</h4>
              <div class="card">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="flex-grow-1 ms-3">
                              <h5 class="mb-1">
                                  {{ $reserva->usuario_creador_reserva->nombre }} 
                                  {{ $reserva->usuario_creador_reserva->apellido }}
                              </h5>
                              <p class="mb-1 text-muted">{{ $reserva->usuario_creador_reserva->cargo }}</p>
                              <p class="mb-0">
                                  <i class="fas fa-envelope me-2"></i>
                                  {{ $reserva->usuario_creador_reserva->email }}
                              </p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection

