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
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado= mysqli_stmt_get_result($stmt);

        if($usuario = mysqli_fetch_assoc($resultado)){
           if(password_verify($password_ingresada,$usuario['password'])) {
            $_SESSION['usuario_id']= $usuario['id'];
            $_SESSION['nombre']= $usuario['nombre'];
            $_SESSION['rol']= $usuario['rol'];
            
           } 
        }
    }

    
}




?>