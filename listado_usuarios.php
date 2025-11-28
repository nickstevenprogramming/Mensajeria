<?php
session_start();
include 'bd.php';

// Obtener el ID del usuario actual
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

// Función para obtener usuarios por rol
function obtenerUsuariosPorRol($conexion, $rol, $id_usuario_actual) {
    $consulta = "SELECT id, nombre, apellido, rol, curso FROM usuarios WHERE rol = ? AND id != ?";
    $declaracion = $conexion->prepare($consulta);
    $declaracion->bind_param("si", $rol, $id_usuario_actual);
    $declaracion->execute();
    $resultado = $declaracion->get_result();
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

// Obtener usuarios para cada rol
$estudiantes = obtenerUsuariosPorRol($conexion, 'estudiante', $id_usuario_actual);
$profesores = obtenerUsuariosPorRol($conexion, 'profesor', $id_usuario_actual);
$orientadores = obtenerUsuariosPorRol($conexion, 'orientador', $id_usuario_actual);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajería</title>
    <link rel="stylesheet" href="styles/mensajeria.css">
</head>
<body>
    <?php include 'nav.php'; ?>  <main>
        <section id="estudiantes">
            <h2>Estudiantes</h2>
            <?php if (count($estudiantes) > 0): ?>
                <ul>
                    <?php foreach ($estudiantes as $estudiante): ?>
                        <li>
                            <?php echo htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?>
                            (<?php echo htmlspecialchars($estudiante['curso']); ?>)
                            <button onclick="iniciarChat(<?php echo $estudiante['id']; ?>)">Iniciar Chat</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay estudiantes registrados.</p>
            <?php endif; ?>
        </section>

        <section id="profesores">
            <h2>Profesores</h2>
            <?php if (count($profesores) > 0): ?>
                <ul>
                    <?php foreach ($profesores as $profesor): ?>
                        <li>
                            <?php echo htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']); ?>
                            <button onclick="iniciarChat(<?php echo $profesor['id']; ?>)">Iniciar Chat</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay profesores registrados.</p>
            <?php endif; ?>
        </section>

        <section id="orientadores">
            <h2>Orientadores</h2>
            <?php if (count($orientadores) > 0): ?>
                <ul>
                    <?php foreach ($orientadores as $orientador): ?>
                        <li>
                            <?php echo htmlspecialchars($orientador['nombre'] . ' ' . $orientador['apellido']); ?>
                            <button onclick="iniciarChat(<?php echo $orientador['id']; ?>)">Iniciar Chat</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay orientadores registrados.</p>
            <?php endif; ?>
        </section>

        <section id="grupos">
            <h2>Grupos</h2>
            <p>Funcionalidad de grupos en desarrollo...</p>
        </section>
    </main>

    <footer>
        <p>© 2025 ITFAM. Todos los derechos reservados.</p>
    </footer>

    <script>
        function iniciarChat(idUsuario) {
            // Redirigir a la página de chat con el ID del usuario seleccionado
            window.location.href = 'index.php?id_destinatario=' + idUsuario;
        }
    </script>
</body>
</html>