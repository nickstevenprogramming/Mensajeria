<?php
require_once 'includes/bd.php';

$grupo_id = $_GET['grupo'] ?? 'general';
$id_usuario = $_SESSION['id_usuario'] ?? 0;

if ($grupo_id === 'general') {
    $stmt = $conexion->prepare("
        SELECT mg.id, mg.mensaje, mg.fecha_envio, mg.anclado, u.nombre, mg.usuario_id, u.foto_perfil
        FROM mensajes_grupo mg
        JOIN usuarios u ON mg.usuario_id = u.id
        WHERE mg.grupo_id = ? AND mg.anclado = 0
        ORDER BY mg.fecha_envio
    ");
    $stmt->bind_param("s", $grupo_id);
} else {
    $stmt = $conexion->prepare("
        SELECT mg.id, mg.mensaje, mg.fecha_envio, mg.anclado, u.nombre, mg.usuario_id, u.foto_perfil
        FROM mensajes_grupo mg
        JOIN usuarios u ON mg.usuario_id = u.id
        WHERE mg.grupo_id = ? AND mg.anclado = 0
        ORDER BY mg.fecha_envio
    ");
    $stmt->bind_param("i", $grupo_id);
}
$stmt->execute();
$mensajes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

foreach ($mensajes as $msg) {
    $es_propio = $msg['usuario_id'] == $id_usuario;
    echo '<div class="mensaje' . ($es_propio ? ' mensaje-propio' : '') . '">';
    if (!$es_propio) {
        echo '<div class="mensaje-header">';
        echo '<img src="' . htmlspecialchars($msg['foto_perfil'] ?: 'img/perfiles/user-default.png') . '" alt="Perfil" class="mensaje-img">';
        echo '<b>' . htmlspecialchars($msg['nombre']) . ':</b>';
        echo '</div>';
    }
    echo '<div class="mensaje-contenido">';
    echo htmlspecialchars($msg['mensaje']);
    echo '<span class="fecha">' . date('H:i', strtotime($msg['fecha_envio'])) . '</span>';
    if ($es_propio) {
        echo '<form method="POST" action="" class="anclar-form">';
        echo '<input type="hidden" name="mensaje_id" value="' . $msg['id'] . '">';
        echo '<button type="submit" name="anclar_mensaje" title="Anclar mensaje">📌</button>';
        echo '</form>';
    }
    echo '</div>';
    echo '</div>';
}
?>