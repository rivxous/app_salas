@extends('layout')
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

        <form id="registrationForm">
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" name="usuario" placeholder="Ingresa tu usuario">
            </div>
            <div class="mb-3">
                <label for="firstName" class="form-label">Nombres</label>
                <input type="text" class="form-control" name="nombres" placeholder="Ingresa tus nombres">
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Apellidos</label>
                <input type="text" class="form-control" name="apellidos" placeholder="Ingresa tus apellidos">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo</label>
                <input type="email" class="form-control" name="correo" placeholder="Ingresa tu correo">
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#registrationForm').on('submit', function(event) {
                event.preventDefault(); // Evita el envío normal del formulario

                const formData = $(this).serialize(); // Serializa los datos del formulario

                $.ajax({
                    url: url + '/', // Cambia esto a la URL de tu servidor
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        // if (response.status == 201) {
                        // alert(response.mensaje);
                        // setInterval(() => {
                        // window.location.href = "/public/usuarios";
                        // }, 1500);
                        // } else {
                        // alert('Error al guardar');
                        // }
                    },
                    error: function(xhr, status, error) {
                        console.log('estatus:' + xhr.status);

                        console.log('mensaje: ' + xhr.responseJSON.mensaje);
                        // Manejar errores aquí
                        alert('Error al enviar el formulario: ' + error);
                    }
                });
            });
        });
    </script>
@endsection

