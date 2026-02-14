<?php

$servidor="localhost";
$usuario="root";
$contraseña="";
$basededatos="concesionario_db";

$conexion = mysqli_connect($servidor, $usuario, $contraseña, $basededatos);

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

$conexion=set_charset("utf8");

?>