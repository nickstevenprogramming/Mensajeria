<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['escribiendo' => []]);
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$grupo_id = $_GET['grupo_id'] ?? null;

if (!$grupo_id) {
    echo json_encode(['escribiendo' => []]);
    exit;
}

// Obtener los nombres únicos de usuarios que están escribiendo en los últimos 5 segundos, excluyendo al usuario actual
$stmt = $conexion->prepare("
    SELECT DISTINCT u.nombre
    FROM eventos_escribiendo e
    JOIN usuarios u ON e.usuario_id = u.id
    WHERE e.grupo_id = ? AND e.usuario_id != ? AND e.timestamp >= NOW() - INTERVAL 5 SECOND
    ORDER BY u.nombre
");
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$nombres = [];
while ($row = $result->fetch_assoc()) {
    $nombres[] = $row['nombre'];
}
$stmt->close();

// Devolver los nombres en formato JSON
echo json_encode(['escribiendo' => $nombres]);
?>