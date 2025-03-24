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
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="name" placeholder="Ingresa tu usuario">
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo</label>
                <input type="email" class="form-control" name="email" placeholder="Ingresa tu correo">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Correo</label>
                <input type="password" class="form-control" name="password" placeholder="Ingresa tu clave">
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
                    url: '{{route("guardar_usuario")}}', // Cambia esto a la URL de tu servidor
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

