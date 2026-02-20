<?php

//Requerimos la conexión a la base de datos
require 'db.php';

//Ahora iniciamos la sesión para poder guardar los datos del usuario
session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email= $_POST['email'];
    $password_ingresada=$_POST['password'];
    $sql= "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?";
    $stmt= mysqli_prepare($conexion, $sql);

    if($stmt){
        
    }

    
}




?>