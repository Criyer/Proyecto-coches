<?php

// Traemos la conexión a la base de datos
require 'db.php';

// Iniciamos sesión para guardar quién entra
session_start();

// Esta función es para soltar una alerta chula y luego mandar al usuario a otro sitio
function swal_redirect($icon, $title, $text, $btnText, $url) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><style>
        * { box-sizing:border-box; }
        body { margin:0; background:#fff; }
    </style></head><body>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            title: '" . addslashes($title) . "',
            text: '" . addslashes($text) . "',
            icon: '" . $icon . "',
            background: '#ffffff',
            confirmButtonColor: '#c40000',
            confirmButtonText: '" . addslashes($btnText) . "',
            allowOutsideClick: false,
            didOpen: () => {
                // Quitamos fondos raros de los iconos de SweetAlert
                document.querySelectorAll(
                    '.swal2-success-circular-line-left,' +
                    '.swal2-success-circular-line-right,' +
                    '.swal2-success-fix'
                ).forEach(el => el.style.backgroundColor = '#ffffff');
            }
        }).then(() => {
            window.location.href = '" . $url . "';
        });
    </script>
    </body></html>";

    exit();
}

// Solo procesamos si vienen datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password_ingresada = $_POST['password'];

    // Buscamos al usuario por su email
    $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        // Si lo encontramos...
        if ($usuario = mysqli_fetch_assoc($resultado)) {

            // Comprobamos si la contraseña coincide con el hash de la base de datos
            if (password_verify($password_ingresada, $usuario['password'])) {

                // Guardamos los datos en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['rol']        = $usuario['rol'];

                // Si es admin (rol 1), al panel de control
                if ($usuario['rol'] == 1) {
                    swal_redirect('success', '¡Bienvenido Administrador!', 'Accediendo al panel de gestión.', 'ACCEDER AL PANEL', '../admin_panel.php');
                } else {
                    // Si es cliente, al index normal
                    swal_redirect('success', '¡Hola, ' . $usuario['nombre'] . '!', 'Bienvenido de nuevo a CR MOTORS.', 'ENTRAR', '../HTML/index.html');
                }

            } else {
                // Si falla el password
                swal_redirect('error', 'Contraseña incorrecta', 'La contraseña introducida no es correcta. Por favor, inténtalo de nuevo.', 'VOLVER', '../HTML/login.html');
            }

        } else {
            // Si el email no está en nuestra lista
            swal_redirect('warning', 'Correo no encontrado', 'No existe ninguna cuenta asociada a este correo electrónico.', 'VOLVER', '../HTML/login.html');
        }

        mysqli_stmt_close($stmt);
    }

    // Cerramos la conexión para no dejarla abierta por ahí
    mysqli_close($conexion);

} else {
    // Si intentan entrar a este archivo directamente, los echamos al login
    header("Location: ../HTML/login.html");
    exit();
}
?>