<?php
require 'db.php';

$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'];
$base_path = dirname(dirname($_SERVER['SCRIPT_NAME']));
$base_url  = $protocol . '://' . $host . rtrim($base_path, '/');

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $pass_encriptada = password_hash($password, PASSWORD_BCRYPT);
    $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;
    $sql  = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $pass_encriptada, $rol);

        if (mysqli_stmt_execute($stmt)) {
            swal_redirect(
                'success',
                '¡Registro completado!',
                'Ya puedes iniciar sesión con tu nueva cuenta.',
                'INICIAR SESIÓN',
                $base_url . '/HTML/login.html'
            );
        } else {
            swal_redirect(
                'error',
                'Error en el registro',
                'El correo electrónico ya está registrado, o ha surgido un fallo en el servidor.',
                'VOLVER',
                $base_url . '/HTML/registro.html'
            );
        }

        mysqli_stmt_close($stmt);
    } else {
        swal_redirect(
            'error',
            'Error del servidor',
            'No se pudo preparar la consulta. Inténtalo más tarde.',
            'VOLVER',
            $base_url . '/HTML/registro.html'
        );
    }

    mysqli_close($conexion);

} else {
    header("Location: " . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/HTML/registro.html');
    exit();
}
?>