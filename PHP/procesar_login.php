<?php

//Requerimos la conexión a la base de datos
require 'db.php';

//Ahora iniciamos la sesión para poder guardar los datos del usuario
session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    //Vamos a recoger los datos del formulario
    $email= $_POST['email'];
    $password_ingresada=$_POST['password'];
    //Ahora vamos a preparar la consulta
    $sql= "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?";
    $stmt= mysqli_prepare($conexion, $sql);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado= mysqli_stmt_get_result($stmt);
         //¿Existe el usuario? Verificamos los parámetros
        if($usuario = mysqli_fetch_assoc($resultado)){
           if(password_verify($password_ingresada,$usuario['password'])) {
            $_SESSION['usuario_id']= $usuario['id'];
            $_SESSION['nombre']= $usuario['nombre'];
            $_SESSION['rol']= $usuario['rol'];
            //Ahora verificamos el rol y damos acceso

            if($usuario['rol']==1){
                header("Location: ../PHP/admin_panel.php");
            }else {
                    // Si eres cliente, a la web principal
                    header("Location: ../HTML/index.html");
                }
                exit();
           } else{
            //Contraseña incorrecta
            echo "<script>
                        alert('La contraseña introducida no es correcta.');
                        window.history.back();
                      </script>";
           }
           //Sino hay ninguna cuenta con ese correo asociado
        }else {
            echo "<script>
                    alert('No existe ninguna cuenta asociada a este correo electrónico.');
                    window.history.back();
                  </script>";
    }
       mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conexion);
} else {
    header("Location: ../login.html");
}




?>