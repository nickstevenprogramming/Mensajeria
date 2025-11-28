<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

header('Content-Type: application/json');

$grupo_id = $_GET['grupo'] ?? 'general';
$id_usuario = $_SESSION['id_usuario'];

if ($grupo_id === 'general') {
    $stmt = $conexion->prepare("
        SELECT mg.id, mg.mensaje, mg.fecha_envio, mg.anclado, u.nombre, mg.usuario_id, u.foto_perfil
        FROM mensajes_grupo mg
        LEFT JOIN usuarios u ON mg.usuario_id = u.id
        WHERE mg.grupo_id = ?
        ORDER BY mg.fecha_envio
    ");
    $stmt->bind_param("s", $grupo_id);
} else {
    $stmt = $conexion->prepare("
        SELECT mg.id, mg.mensaje, mg.fecha_envio, mg.anclado, u.nombre, mg.usuario_id, u.foto_perfil
        FROM mensajes_grupo mg
        LEFT JOIN usuarios u ON mg.usuario_id = u.id
        WHERE mg.grupo_id = ?
        ORDER BY mg.fecha_envio
    ");
    $stmt->bind_param("i", $grupo_id);
}

if (!$stmt->execute()) {
    error_log("Error executing query in mensajes_grupo_ajax.php: " . $stmt->error);
    echo json_encode(['messages' => [], 'status' => 'error', 'error' => 'Database query failed']);
    exit;
}

$result = $stmt->get_result();
$messages = [];

while ($msg = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $msg['id'],
        'mensaje' => htmlspecialchars($msg['mensaje']),
        'fecha_envio' => date('H:i', strtotime($msg['fecha_envio'])),
        'nombre' => htmlspecialchars($msg['nombre'] ?? 'Usuario Desconocido'),
        'usuario_id' => $msg['usuario_id'],
        'foto_perfil' => htmlspecialchars($msg['foto_perfil'] ?: 'img/perfiles/user-default.png')
    ];
}

$stmt->close();

if (empty($messages)) {
    echo json_encode(['messages' => [], 'status' => 'empty']);
} else {
    echo json_encode(['messages' => $messages, 'status' => 'success']);
}
?>