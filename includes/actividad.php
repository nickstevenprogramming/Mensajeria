<?php
require_once 'bd.php';
session_start();

if (isset($_SESSION['id_usuario'])) {
    $id = $_SESSION['id_usuario'];
    $conexion->query("UPDATE usuarios SET ultima_actividad = NOW() WHERE id = $id");
}
