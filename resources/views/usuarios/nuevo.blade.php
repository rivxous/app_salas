@extends('layouts.base')

@section('title', 'Inicio')

@section('css')
    <style>
        .container {
            background: red;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <h2>Formulario de Registro de usuario</h2>

        {{-- Formulario de Laravel Collective --}}
        {!! Form::open(['route' => 'guardar_usuario']) !!}
        @csrf
        <div class="mb-3">
            {!! Form::label('name', 'Nombre', ['class' => 'form-label']) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingresa tu usuario']) !!}
        </div>

        <div class="mb-3">
            {!! Form::label('email', 'Correo', ['class' => 'form-label']) !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Ingresa tu correo']) !!}
        </div>

        <div class="mb-3">
            {!! Form::label('password', 'Clave', ['class' => 'form-label']) !!}
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Ingresa tu clave']) !!}
        </div>

        {!! Form::submit('Enviar', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endsection
