<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$grupo_id = $_POST['grupo_id'] ?? null;
$escribiendo = $_POST['escribiendo'] ?? 0;

if (!$grupo_id) {
    http_response_code(400);
    exit;
}

// Eliminar eventos antiguos (mayores a 5 segundos)
$stmt = $conexion->prepare("DELETE FROM eventos_escribiendo WHERE timestamp < NOW() - INTERVAL 5 SECOND");
$stmt->execute();
$stmt->close();

if ($escribiendo == 1) {
    // Verificar si ya existe un evento reciente para este usuario en este grupo
    $stmt = $conexion->prepare("
        SELECT id FROM eventos_escribiendo 
        WHERE usuario_id = ? AND grupo_id = ? AND timestamp >= NOW() - INTERVAL 5 SECOND
        LIMIT 1
    ");
    $stmt->bind_param("ii", $usuario_id, $grupo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si existe un evento reciente, actualizar su timestamp
        $stmt = $conexion->prepare("
            UPDATE eventos_escribiendo 
            SET timestamp = NOW() 
            WHERE usuario_id = ? AND grupo_id = ?
        ");
        $stmt->bind_param("ii", $usuario_id, $grupo_id);
        $stmt->execute();
    } else {
        // Si no existe, insertar un nuevo evento
        $stmt = $conexion->prepare("
            INSERT INTO eventos_escribiendo (usuario_id, grupo_id, timestamp) 
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ii", $usuario_id, $grupo_id);
        $stmt->execute();
    }
    $stmt->close();
} else {
    // Si escribiendo es 0, eliminar el evento de este usuario
    $stmt = $conexion->prepare("
        DELETE FROM eventos_escribiendo 
        WHERE usuario_id = ? AND grupo_id = ?
    ");
    $stmt->bind_param("ii", $usuario_id, $grupo_id);
    $stmt->execute();
    $stmt->close();
}

http_response_code(200);
?>