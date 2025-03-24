<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">

</head>

<body>
    
    <h1 class="text-center">Registro de sala</h1>
    <div class="card p-4 bg-light">
        <table class="table table-striped" width="100%">
            <div class="form-group text-left">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" class="form-control"
                    placeholder="Escriba nombre de la sala"
                    value="">
            </div>

            <div class="form-group text-left">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" name="ubicacion " class="form-control"
                    placeholder="Escriba ubicacion de la sala"
                    value="">
            </div>

            <div class="form-group text-left">
                <label for="capacidad">Capacidad:</label>
                <input type="text" name="capacidad" class="form-control"
                    placeholder="Escriba numero aproximado de personas"
                    value="">
            </div>

            <div class="form-group text-left">
                <label for="estado">Estado:</label>
                <select class="form-control" name="estado" id="estado">

                    <option value="void">--- Seleccione ---</option>
                </select>
            </div>

            <div>
                <div class="form-group text-left">
                    <label for="horario_inicio">Hora de inicio:</label>
                    <input type="time" name="horario_inicio" class="form-control" required min="07:30" max="16:30" style="width:10rem;"
                        value="">

                        <label for="horario_fin">Hora de fin:</label>
                    <input type="time" name="horario_fin" class="form-control" required min="07:30" max="16:30" style="width:10rem;"
                        value="">
                </div>

                <div class="form-group text-left">
                    
                </div>
            </div><br>
        </table>
    </div>
    <script src="boostrap/js/boostrap.min.js"></script>

</body>
<a href="" class="btn btn-danger">Guardar</a>
<a href="" class="btn btn-danger">Regresar</a>

</html>