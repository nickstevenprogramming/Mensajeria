<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$chat_con_id = isset($_GET['usuario']) ? intval($_GET['usuario']) : 0;
if (!$id_usuario || !$chat_con_id) exit;

// Marcar mensajes como leídos (para receptor)
$conexion->query("UPDATE mensajes 
                  SET leido = 1
                  WHERE receptor_id = $id_usuario AND emisor_id = $chat_con_id");

$sql = "
  SELECT m.*, u.nombre, u.apellido, m.fecha_envio
  FROM mensajes m
  JOIN usuarios u ON m.emisor_id = u.id
  WHERE 
    (m.emisor_id = $id_usuario AND m.receptor_id = $chat_con_id)
    OR 
    (m.emisor_id = $chat_con_id AND m.receptor_id = $id_usuario)
  ORDER BY m.fecha_envio ASC
";
$res = $conexion->query($sql);
while ($msg = $res->fetch_assoc()) {
  $tipo    = ($msg['emisor_id'] == $id_usuario) ? 'out' : 'in';
  $mensaje = htmlspecialchars($msg['mensaje']);
  $hora    = date("H:i", strtotime($msg['fecha_envio']));
  $estado  = "";
  if ($tipo === 'out') {
    // Si leido=1 => doble cotejo (azul), si no => uno
    if ($msg['leido']) {
      $estado = "<span class='cotejo cotejo-leido'>✓✓</span>";
    } else {
      $estado = "<span class='cotejo'>✓</span>";
    }
  }

  echo "<div class='mensaje $tipo'>
          $mensaje
          <div class='info-mensaje'><span class='hora'>$hora</span> $estado</div>
        </div>";
}
