<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

$id_usuario = $_SESSION['id_usuario'];
$receptor_id = intval($_POST['receptor_id']);
$mensaje = trim($_POST['mensaje']);

if ($mensaje !== "") {
  $stmt = $conexion->prepare("INSERT INTO mensajes (emisor_id, receptor_id, mensaje, fecha_envio) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iis", $id_usuario, $receptor_id, $mensaje);
  $stmt->execute();
  echo "Mensaje enviado";
} else {
  echo "Mensaje vacío";
}
?>
