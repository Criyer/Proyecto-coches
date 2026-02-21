<?php
// Arrancamos la sesión para saber quién está ahí
session_start();
// Le decimos al navegador que vamos a soltar un JSON
header('Content-Type: application/json');

// Si el usuario ya se ha logueado, le pasamos sus datos
if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'logueado' => true,
        'nombre'   => $_SESSION['nombre'],
        'rol'      => $_SESSION['rol']
    ]);
} else {
    // Si no, pues le decimos que no hay nadie
    echo json_encode(['logueado' => false]);
}
?>
