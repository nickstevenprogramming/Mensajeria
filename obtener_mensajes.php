<?php
// Iniciar la sesión para obtener el ID del usuario autenticado
session_start();
// Incluir el archivo de conexión a la base de datos
include __DIR__ . '/bd.php';

// Obtener el ID del usuario actual
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;
// Consultar los mensajes y el nombre del usuario que los envió
$consulta = "SELECT m.*, u.nombre FROM mensajes m JOIN usuarios u ON m.id_usuario = u.id ORDER BY m.fecha_envio ASC";
$resultado = $conexion->query($consulta);

// Iterar sobre los mensajes y generar el HTML para mostrarlos
while ($fila = $resultado->fetch_assoc()) {
    // Determinar si el mensaje es saliente (enviado por el usuario actual) o entrante (enviado por otro usuario)
    $tipo = ($fila['id_usuario'] == $id_usuario_actual) ? 'outgoing' : 'incoming';
    
    // Mostrar el mensaje
    echo "<div class='message $tipo'><p>" . htmlspecialchars($fila['mensaje']) . "</p>";
    
    // Mostrar el nombre del usuario solo si NO es el usuario actual
    if ($fila['id_usuario'] != $id_usuario_actual) {
        echo "<small>" . htmlspecialchars($fila['nombre']) . "</small>";
    }
    
    echo "</div>";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>