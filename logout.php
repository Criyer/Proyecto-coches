<?php
// Iniciamos para poder borrarla
session_start();
// Limpiamos todo lo que hubiera en la sesión
session_unset();
// Y la destruimos por completo
session_destroy();

// Calculamos la ruta base para volver a la página principal
$base = dirname($_SERVER['SCRIPT_NAME']);
header("Location: " . rtrim($base, '/') . "/HTML/index.html");
exit();
?>
