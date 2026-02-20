<?php
require 'db.php';


function swal_redirect($icon, $title, $text, $btnText, $url) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            title: '" . addslashes($title) . "',
            text: '" . addslashes($text) . "',
            icon: '" . $icon . "',
            confirmButtonColor: '#c40000',
            confirmButtonText: '" . addslashes($btnText) . "',
            allowOutsideClick: false
        }).then(() => {
            window.location.href = '" . $url . "';
        });
    </script>
    </body></html>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $pass_encriptada = password_hash($password, PASSWORD_BCRYPT);

    $sql  = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 0)";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $pass_encriptada);
        if (mysqli_stmt_execute($stmt)) {
            swal_redirect('success', '¡Registro completado!', 'Ya puedes iniciar sesión con tu nueva cuenta.', 'INICIAR SESIÓN', '../HTML/login.html');
        } else {
            swal_redirect('error', 'Error en el registro', 'El correo electrónico ya está registrado, o ha surgido un fallo en el servidor.', 'VOLVER', '../HTML/registro.html');
        }
        mysqli_stmt_close($stmt);
    } else {
        swal_redirect('error', 'Error del servidor', 'No se pudo preparar la consulta. Inténtalo más tarde.', 'VOLVER', '../HTML/registro.html');
    }

    mysqli_close($conexion);

} else {
    header("Location: ../HTML/registro.html");
    exit();
}
?>