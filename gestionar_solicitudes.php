<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['id_usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $solicitud_id = $_POST['solicitud_id'] ?? 0;
    $accion = $_POST['accion'] ?? '';

    if ($solicitud_id && in_array($accion, ['aceptar', 'rechazar'])) {
        $nuevo_estado = $accion === 'aceptar' ? 'aceptado' : 'rechazado';
        $stmt = $conexion->prepare("UPDATE solicitudes_grupo SET estado = ? WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("sii", $nuevo_estado, $solicitud_id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        if ($accion === 'aceptar') {
            $stmt = $conexion->prepare("SELECT grupo_id FROM solicitudes_grupo WHERE id = ?");
            $stmt->bind_param("i", $solicitud_id);
            $stmt->execute();
            $grupo_id = $stmt->get_result()->fetch_assoc()['grupo_id'];
            $stmt->close();

            $stmt = $conexion->prepare("INSERT INTO grupo_usuarios (grupo_id, usuario_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $grupo_id, $usuario_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header("Location: group.php");
exit();
?>