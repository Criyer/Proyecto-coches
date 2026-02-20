<?php

require 'db.php';
session_start();

// Peticion GET: devuelve todos los coches en formato JSON
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

// Peticion POST: solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    
    // Nuevo coche
    case 'crear':
        $modelo = $_POST['modelo'];
        $precio = $_POST['precio'];
        $anio   = $_POST['anio'];
        $kms    = $_POST['kms'];
        $motor  = $_POST['motor'];

        // Comprobar imagen
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('Error: debes seleccionar una imagen.'); history.back();</script>";
            break;
        }
        $tiposPermitidos = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($_FILES['imagen']['type'], $tiposPermitidos)) {
            echo "<script>alert('Formato no válido. Usa JPG, PNG o WebP.'); history.back();</script>";
            break;
        }
        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            echo "<script>alert('La imagen supera los 5 MB.'); history.back();</script>";
            break;
        }
        $ext    = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre = uniqid('coche_', true) . '.' . $ext;
        $ruta   = dirname(__DIR__) . '/IMAGENES/coches/' . $nombre;
        $rutaWeb = '/proyecto_cochesCBO/IMAGENES/coches/' . $nombre;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            echo "<script>alert('Error al guardar la imagen en el servidor.'); history.back();</script>";
            break;
        }

        $sql = "INSERT INTO coches (modelo, precio, anio, kms, motor, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sdisss", $modelo, $precio, $anio, $kms, $motor, $rutaWeb);
            if (mysqli_stmt_execute($stmt)) {
                $base = dirname($_SERVER['SCRIPT_NAME']);
                echo "<script>
                        alert('Vehículo registrado correctamente.');
                        window.location.href = '" . dirname($base) . "/admin_panel.php';
                      </script>";
            } else {
                echo "Error al insertar: " . mysqli_error($conexion);
            }
            mysqli_stmt_close($stmt);
        }
        break;

    // Editar coche
    case 'editar':
        $id     = intval($_POST['id']);
        $modelo = $_POST['modelo'];
        $precio = $_POST['precio'];
        $anio   = intval($_POST['anio']);
        $kms    = $_POST['kms'];
        $motor  = $_POST['motor'];

        // Si hay nueva imagen la reemplazamos, si no se queda la actual
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

    // Cambiar estado
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

    // Borrar coche
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
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
}

mysqli_close($conexion);
?>
