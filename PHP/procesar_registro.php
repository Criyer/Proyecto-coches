<?php
// Pillamos la conexión a la base de datos
require 'db.php';

// Esto es para que las redirecciones funcionen bien independientemente de dónde estemos
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'];
$base_path = dirname(dirname($_SERVER['SCRIPT_NAME']));
$base_url  = $protocol . '://' . $host . rtrim($base_path, '/');

// La misma función de siempre para las alertas de SweetAlert
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
                // Dejamos los iconos de SweetAlert con fondo blanco
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

// Si nos mandan datos por POST, los guardamos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Encriptamos la contraseña para que no se vea en la base de datos
    $pass_encriptada = password_hash($password, PASSWORD_BCRYPT);
    // Por defecto el rol es 0 (cliente normal)
    $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;
    
    // Insertamos el nuevo usuario en la tabla
    $sql  = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $nombre, $email, $pass_encriptada, $rol);

        if (mysqli_stmt_execute($stmt)) {
            // Si todo ha ido bien, le avisamos y al login
            swal_redirect(
                'success',
                '¡Registro completado!',
                'Ya puedes iniciar sesión con tu nueva cuenta.',
                'INICIAR SESIÓN',
                $base_url . '/HTML/login.html'
            );
        } else {
            // Si falla (probablemente porque el email ya existe)
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
        // Por si revienta el servidor
        swal_redirect(
            'error',
            'Error del servidor',
            'No se pudo preparar la consulta. Inténtalo más tarde.',
            'VOLVER',
            $base_url . '/HTML/registro.html'
        );
    }

    // Cerramos todo antes de irnos
    mysqli_close($conexion);

} else {
    // Si entran por la cara, los mandamos al formulario de registro
    header("Location: " . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/HTML/registro.html');
    exit();
}
?>