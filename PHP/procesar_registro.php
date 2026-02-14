<?php
require 'db.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nombre= $_POST['nombre'];
    $email= $_POST['email'];
    $password= $_POST['password'];

    $pass_encriptada = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 0)";

    $stmt=mysqli_prepare($conexion, $sql);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $pass_encriptada);
        if(mysqli_stmt_execute($stmt)){
            echo "<script>
                    alert('¡Registro completado con éxito! Ya puedes iniciar sesión.');
                    window.location.href = '../HTML/login.html';
                  </script>";
        }else{
            echo "<script>
                    alert('El correo electrónico ya está registrado, o ha surgido un fallo en el servidor.');
                    window.location.href = '../HTML/registro.html';
                  </script>";
        }
        mysqli_stmt_close($stmt);
    }else{
        echo "Error en la preparación de la consulta" . mysqli_error($conexion);
    }
    mysqli_close($conexion);
    
}else{
    header("Location:../HTML/registro.html");
    exit();
}
?>