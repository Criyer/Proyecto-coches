<?php

require 'db.php';

session_start();

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

    $email = $_POST['email'];
    $password_ingresada = $_POST['password'];

    $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($usuario = mysqli_fetch_assoc($resultado)) {

            if (password_verify($password_ingresada, $usuario['password'])) {

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['rol']        = $usuario['rol'];

                if ($usuario['rol'] == 1) {
                    swal_redirect('success', '¡Bienvenido Administrador!', 'Accediendo al panel de gestión.', 'ACCEDER AL PANEL', '../admin_panel.php');
                } else {
                    swal_redirect('success', '¡Hola, ' . $usuario['nombre'] . '!', 'Bienvenido de nuevo a CR MOTORS.', 'ENTRAR', '../HTML/index.html');
                }

            } else {
                swal_redirect('error', 'Contraseña incorrecta', 'La contraseña introducida no es correcta. Por favor, inténtalo de nuevo.', 'VOLVER', '../HTML/login.html');
            }

        } else {
            swal_redirect('warning', 'Correo no encontrado', 'No existe ninguna cuenta asociada a este correo electrónico.', 'VOLVER', '../HTML/login.html');
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conexion);

} else {
    header("Location: ../HTML/login.html");
    exit();
}
?>