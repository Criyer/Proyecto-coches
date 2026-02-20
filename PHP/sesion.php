<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'logueado' => true,
        'nombre'   => $_SESSION['nombre'],
        'rol'      => $_SESSION['rol']
    ]);
} else {
    echo json_encode(['logueado' => false]);
}
?>
