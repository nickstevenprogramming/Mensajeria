<?php
// Iniciar la sesión para poder destruirla
session_start();
// Destruir todas las variables de sesión
session_destroy();
// Redirigir a la página de inicio de sesión
header('Location: inicio.php');
exit;
?>