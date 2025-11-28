<?php
// Configuración de la conexión a la base de datos
$servidor = 'localhost';
$usuario = 'root';
$contraseña = '';
$nombre_bd = 'itfam_chat';

// Establecer la conexión con MySQL
$conexion = new mysqli($servidor, $usuario, $contraseña, $nombre_bd);

// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>