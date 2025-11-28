<?php
$servidor   = "sql113.infinityfree.com";           // ← este es tu host
$usuario    = "if0_40532298";                      // ← tu usuario (el prefijo if0_)
$contraseña = ""; // ← ¡¡importante!!
$nombre_bd  = "if0_40532298_itfam_chat";           // ← tu base de datos

$conexion = new mysqli($servidor, $usuario, $contraseña, $nombre_bd);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>