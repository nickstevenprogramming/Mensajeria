<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

$emisor_id   = $_SESSION['id_usuario'] ?? 0;
$receptor_id = intval($_POST['receptor_id'] ?? 0);
$mensaje     = trim($_POST['mensaje'] ?? '');

if ($emisor_id && $receptor_id && $mensaje !== '') {
  // Insertar mensaje con leido=0 (para el receptor)
  $stmt = $conexion->prepare("INSERT INTO mensajes (emisor_id, receptor_id, mensaje, fecha_envio, leido) 
                              VALUES (?, ?, ?, NOW(), 0)");
  $stmt->bind_param("iis", $emisor_id, $receptor_id, $mensaje);
  $stmt->execute();
  echo "OK";
} else {
  echo "No se pudo enviar";
}
