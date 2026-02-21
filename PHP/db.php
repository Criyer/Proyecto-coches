<?php

// Datos para conectar a la base de datos de local
$servidor="localhost";
$usuario="root";
$contraseña="";
$basededatos="concesionario_db";

// Aquí hacemos la conexión, si falla nos cargamos el proceso y avisamos
$conexion = mysqli_connect($servidor, $usuario, $contraseña, $basededatos);

if (!$conexion) {
    die("Vaya, no hemos podido conectar con la base de datos: " . mysqli_connect_error());
}

// Ponemos el charset en utf8 para que no salgan cosas raras con las tildes
$conexion->set_charset("utf8");

?>