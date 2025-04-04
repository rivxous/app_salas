@extends('layouts.base')

@section('content')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Mostrar errores si existen -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card p-4 bg-light shadow">
                    <h1 class="text-center mb-4">Iniciar Sesión</h1>

                    {!! Form::open(['route' => 'login.post', 'method' => 'POST']) !!}
                    @csrf

                    <div class="form-group mb-3">
                        {!! Form::label('username', 'Usuario:', ['class' => 'form-label']) !!}
                        {!! Form::text('username', old('username'), [
                            'id' => 'username',
                            'class' => 'form-control',
                            'placeholder' => 'Ingrese su usuario',
                            'required' => true,
                            'autofocus' => true
                        ]) !!}
                        @error('username')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        {!! Form::label('password', 'Contraseña:', ['class' => 'form-label']) !!}
                        {!! Form::password('password', [
                            'id' => 'password',
                            'class' => 'form-control',
                            'placeholder' => 'Ingrese su contraseña',
                            'required' => true
                        ]) !!}
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary w-100" type="submit">Iniciar sesión</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
