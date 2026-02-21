<?php

// Conectamos a la base de datos y arrancamos la sesión
require 'db.php';
session_start();

// Si nos piden datos con GET, devolvemos todos los coches en un JSON para el catálogo
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $resultado = mysqli_query($conexion, "SELECT * FROM coches ORDER BY id DESC");
    $coches = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $coches[] = $fila;
    }
    header('Content-Type: application/json');
    echo json_encode($coches);
    mysqli_close($conexion);
    exit();
}

// Para lo que viene ahora (POST), solo dejamos pasar al administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No estás autorizado, pillín']);
    exit();
}

// Miramos qué quiere hacer el admin
$accion = $_POST['accion'] ?? '';

switch ($accion) {
    
    // Para meter un coche nuevo en el sistema
    case 'crear':
        $modelo = $_POST['modelo'];
        $precio = $_POST['precio'];
        $anio   = $_POST['anio'];
        $kms    = $_POST['kms'];
        $motor  = $_POST['motor'];

        // Si no han subido imagen, les damos un toque
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('Oye, que se te ha olvidado la foto.'); history.back();</script>";
            break;
        }
        
        // Comprobamos que sea una imagen de verdad y que no pese un quintal
        $tiposPermitidos = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($_FILES['imagen']['type'], $tiposPermitidos)) {
            echo "<script>alert('Ese formato no me vale. Usa JPG, PNG o WebP.'); history.back();</script>";
            break;
        }
        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            echo "<script>alert('Esa foto es enorme, el máximo son 5 MB.'); history.back();</script>";
            break;
        }
        
        // Le ponemos un nombre único a la imagen para no machacar otras
        $ext    = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre = uniqid('coche_', true) . '.' . $ext;
        $ruta   = dirname(__DIR__) . '/IMAGENES/coches/' . $nombre;
        $rutaWeb = '/proyecto_cochesCBO/IMAGENES/coches/' . $nombre;

        // La movemos a la carpeta de imágenes
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            echo "<script>alert('Error raro al guardar la imagen.'); history.back();</script>";
            break;
        }

        // Lo metemos todo en la base de datos
        $sql = "INSERT INTO coches (modelo, precio, anio, kms, motor, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sdisss", $modelo, $precio, $anio, $kms, $motor, $rutaWeb);
            if (mysqli_stmt_execute($stmt)) {
                $base = dirname($_SERVER['SCRIPT_NAME']);
                echo "<script>
                        alert('Coche registrado de lujo.');
                        window.location.href = '" . dirname($base) . "/admin_panel.php';
                      </script>";
            } else {
                echo "Error al insertar: " . mysqli_error($conexion);
            }
            mysqli_stmt_close($stmt);
        }
        break;

    // Para cambiar los datos de un coche que ya existe
    case 'editar':
        $id     = intval($_POST['id']);
        $modelo = $_POST['modelo'];
        $precio = $_POST['precio'];
        $anio   = intval($_POST['anio']);
        $kms    = $_POST['kms'];
        $motor  = $_POST['motor'];

        // Si suben una foto nueva, la cambiamos. Si no, dejamos la que estaba.
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $tiposPermitidos = ['image/jpeg','image/png','image/webp','image/gif'];
            if (in_array($_FILES['imagen']['type'], $tiposPermitidos) && $_FILES['imagen']['size'] <= 5*1024*1024) {
                $ext    = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre = uniqid('coche_', true) . '.' . $ext;
                $ruta   = dirname(__DIR__) . '/IMAGENES/coches/' . $nombre;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                    $imagen = '/proyecto_cochesCBO/IMAGENES/coches/' . $nombre;
                } else {
                    $imagen = $_POST['imagen_actual'];
                }
            } else {
                $imagen = $_POST['imagen_actual'];
            }
        } else {
            $imagen = $_POST['imagen_actual'];
        }

        // Actualizamos los datos en la DB
        $sql = "UPDATE coches SET modelo=?, precio=?, anio=?, kms=?, motor=?, imagen=? WHERE id=?";
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sdisssi", $modelo, $precio, $anio, $kms, $motor, $imagen, $id);
            $success = mysqli_stmt_execute($stmt);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            mysqli_stmt_close($stmt);
        }
        break;

    // Para marcar un coche como vendido o volver a ponerlo en venta
    case 'estado':
        $id = intval($_POST['id']);
        $nuevo_estado = intval($_POST['estado']);

        $sql = "UPDATE coches SET estado = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $nuevo_estado, $id);
            $success = mysqli_stmt_execute($stmt);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            mysqli_stmt_close($stmt);
        }
        break;

    // Para borrar un coche definitivamente
    case 'borrar':
        $id = intval($_POST['id']);

        $sql = "DELETE FROM coches WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            $success = mysqli_stmt_execute($stmt);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            mysqli_stmt_close($stmt);
        }
        break;

    default:
        // Si nos mandan algo que no entendemos
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No sé qué me estás pidiendo']);
}

// Cerramos la conexión y listo
mysqli_close($conexion);
?>
