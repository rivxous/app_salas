@extends('layout')

@section('title', 'Inicio')

@section('content')
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
</head>
<body>
<h1>Bienvenido al sistema</h1>
<nav>
    <ul>
        <li><a href="modulo1.php">Módulo 1</a></li>
        <li><a href="modulo2.php">Módulo 2</a></li>
        <li><a href="modulo3.php">Módulo 3</a></li>
    </ul>
</nav>
</body>
</html>


    <h1>Bienvenido a mi página</h1>
    <p>Esta es la página de inicio.</p>


@endsection
