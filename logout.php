<?php
session_start();
session_unset();
session_destroy();
$base = dirname($_SERVER['SCRIPT_NAME']);
header("Location: " . rtrim($base, '/') . "/HTML/index.html");
exit();
?>
