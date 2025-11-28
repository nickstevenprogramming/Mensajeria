<?php
require_once 'includes/bd.php';
require_once 'includes/auth.php';

$nombre = $_POST['nombre_grupo'] ?? '';
$miembros = $_POST['usuarios'] ?? [];
$creador = $_SESSION['id_usuario'];

if ($nombre && count($miembros) > 0) {
    $stmt = $conexion->prepare("INSERT INTO grupos (nombre, creador_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $creador);
    $stmt->execute();
    $grupo_id = $stmt->insert_id;

    // Agregar creador
    $conexion->query("INSERT INTO grupo_usuarios (grupo_id, usuario_id) VALUES ($grupo_id, $creador)");

    // Agregar miembros seleccionados
    foreach ($miembros as $id_usuario) {
        $conexion->query("INSERT INTO grupo_usuarios (grupo_id, usuario_id) VALUES ($grupo_id, $id_usuario)");
    }

    header("Location: group.php?grupo=$grupo_id");
} else {
    echo "<script>alert('Completa todos los campos'); window.location='group.php';</script>";
}
