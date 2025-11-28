<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';
require_once 'includes/funciones.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$grupo_id = $_GET['grupo'] ?? 1;

$stmt = $conexion->prepare("SELECT id FROM grupos WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt = $conexion->prepare("INSERT INTO grupos (nombre, creador_id, fecha_creacion) VALUES ('Grupo General', ?, NOW())");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    error_log("Grupo con id = 1 (Grupo General) creado automáticamente por usuario $id_usuario");
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mensaje'])) {
        $mensaje = $_POST['mensaje'];

        $stmt = $conexion->prepare("SELECT id FROM grupos WHERE id = ?");
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            error_log("Error: El grupo $grupo_id no existe en la tabla grupos");
            $stmt = $conexion->prepare("INSERT INTO grupos (nombre, creador_id, fecha_creacion) VALUES (?, ?, NOW())");
            $nombre_grupo = $grupo_id == 1 ? 'Grupo General' : 'Grupo Desconocido';
            $stmt->bind_param("si", $nombre_grupo, $id_usuario);
            $stmt->execute();
            $grupo_id = $conexion->insert_id;
        }
        $stmt->close();

        $stmt = $conexion->prepare("INSERT INTO mensajes_grupo (grupo_id, usuario_id, mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $grupo_id, $id_usuario, $mensaje);
        if ($stmt->execute()) {
            $mensaje_id = $conexion->insert_id;
            error_log("Mensaje insertado con ID: $mensaje_id para grupo: $grupo_id");
            
            if ($grupo_id == 1) {
                $stmt = $conexion->prepare("
                    INSERT INTO mensajes_grupo_leidos (mensaje_id, usuario_id, grupo_id, leido)
                    SELECT ?, id, ?, 0
                    FROM usuarios
                    WHERE id != ?
                ");
                $stmt->bind_param("iii", $mensaje_id, $grupo_id, $id_usuario);
            } else {
                $stmt = $conexion->prepare("
                    INSERT INTO mensajes_grupo_leidos (mensaje_id, usuario_id, grupo_id, leido)
                    SELECT ?, usuario_id, ?, 0
                    FROM grupo_usuarios
                    WHERE grupo_id = ? AND usuario_id != ?
                ");
                $stmt->bind_param("iiii", $mensaje_id, $grupo_id, $grupo_id, $id_usuario);
            }
            $stmt->execute();
            $stmt->close();

            $stmt = $conexion->prepare("UPDATE usuarios SET ultima_actividad = NOW() WHERE id = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("Error al insertar mensaje: " . $stmt->error);
        }
    }

    if (isset($_POST['crear_grupo'])) {
        $nombre_grupo = trim($_POST['nombre_grupo']); // Trim para evitar espacios en blanco
        $usuarios_seleccionados = $_POST['usuarios'] ?? [];

        if (count($usuarios_seleccionados) < 2) {
            $error = "Debes seleccionar al menos 2 usuarios para crear un grupo.";
        } else {
            // Verificar si el nombre ya existe
            $stmt = $conexion->prepare("SELECT id FROM grupos WHERE nombre = ?");
            $stmt->bind_param("s", $nombre_grupo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Ya existe un grupo con el nombre '$nombre_grupo'. Por favor, elige un nombre diferente.";
            } else {
                $stmt = $conexion->prepare("INSERT INTO grupos (nombre, creador_id, fecha_creacion) VALUES (?, ?, NOW())");
                $stmt->bind_param("si", $nombre_grupo, $id_usuario);
                if ($stmt->execute()) {
                    $nuevo_grupo_id = $conexion->insert_id;

                    $stmt = $conexion->prepare("INSERT INTO grupo_usuarios (grupo_id, usuario_id) VALUES (?, ?)");
                    $stmt->bind_param("ii", $nuevo_grupo_id, $id_usuario);
                    $stmt->execute();

                    $stmt = $conexion->prepare("INSERT INTO solicitudes_grupo (grupo_id, usuario_id, estado) VALUES (?, ?, 'pendiente')");
                    foreach ($usuarios_seleccionados as $usuario_id) {
                        $stmt->bind_param("ii", $nuevo_grupo_id, $usuario_id);
                        $stmt->execute();
                    }
                    $stmt->close();
                    header("Location: group.php?grupo=$nuevo_grupo_id");
                    exit();
                } else {
                    $error = "Error al crear el grupo: " . $stmt->error;
                    error_log("Error al crear grupo: " . $stmt->error);
                }
            }
            $stmt->close();
        }
    }

    if (isset($_POST['anclar_mensaje'])) {
        $mensaje_id = $_POST['mensaje_id'];
        $stmt = $conexion->prepare("UPDATE mensajes_grupo SET anclado = 1 WHERE id = ? AND grupo_id = ?");
        $stmt->bind_param("ii", $mensaje_id, $grupo_id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['desanclar_mensaje'])) {
        $mensaje_id = $_POST['mensaje_id'];
        $stmt = $conexion->prepare("UPDATE mensajes_grupo SET anclado = 0 WHERE id = ? AND grupo_id = ?");
        $stmt->bind_param("ii", $mensaje_id, $grupo_id);
        $stmt->execute();
        $stmt->close();
    }
}

$stmt = $conexion->prepare("UPDATE usuarios SET ultima_actividad = NOW() WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->close();

$stmt = $conexion->prepare("SELECT id, nombre, rol, ultima_actividad, foto_perfil FROM usuarios WHERE id != ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conexion->prepare("
    SELECT g.id, g.nombre, 
           (SELECT COUNT(*) 
            FROM mensajes_grupo mg 
            LEFT JOIN mensajes_grupo_leidos mgl ON mg.id = mgl.mensaje_id AND mgl.usuario_id = ?
            WHERE mg.grupo_id = g.id AND (mgl.leido = 0 OR mgl.leido IS NULL)) as mensajes_no_leidos
    FROM grupos g
    JOIN grupo_usuarios gu ON g.id = gu.grupo_id
    WHERE gu.usuario_id = ?
");
$stmt->bind_param("ii", $id_usuario, $id_usuario);
$stmt->execute();
$grupos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conexion->prepare("
    SELECT s.id, g.nombre as grupo_nombre
    FROM solicitudes_grupo s
    JOIN grupos g ON s.grupo_id = g.id
    WHERE s.usuario_id = ? AND s.estado = 'pendiente'
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$solicitudes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$miembros_grupo = [];
if ($grupo_id == 1) {
    $stmt = $conexion->prepare("
        SELECT id, nombre, rol, ultima_actividad, foto_perfil
        FROM usuarios
    ");
    $stmt->execute();
    $miembros_grupo = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $stmt = $conexion->prepare("
        SELECT u.id, u.nombre, u.rol, u.ultima_actividad, u.foto_perfil
        FROM grupo_usuarios gu
        JOIN usuarios u ON gu.usuario_id = u.id
        WHERE gu.grupo_id = ?
    ");
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $miembros_grupo = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$stmt = $conexion->prepare("
    SELECT mg.id, mg.mensaje, mg.fecha_envio, u.nombre
    FROM mensajes_grupo mg
    JOIN usuarios u ON mg.usuario_id = u.id
    WHERE mg.grupo_id = ? AND mg.anclado = 1
    ORDER BY mg.fecha_envio DESC
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$mensajes_anclados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($grupo_id == 1) {
    $stmt = $conexion->prepare("
        SELECT mg.id, mg.mensaje, mg.fecha_envio, mg.anclado, u.nombre, mg.usuario_id, u.foto_perfil
        FROM mensajes_grupo mg
        LEFT JOIN usuarios u ON mg.usuario_id = u.id
        WHERE mg.grupo_id = ?
        ORDER BY mg.fecha_envio
    ");
    $stmt->bind_param("i", $grupo_id);
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
$stmt->execute();
$mensajes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conexion->prepare("
    UPDATE mensajes_grupo_leidos 
    SET leido = 1 
    WHERE grupo_id = ? AND usuario_id = ?
");
$stmt->bind_param("ii", $grupo_id, $id_usuario);
$stmt->execute();
$stmt->close( );
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Grupo - ITFAM</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_group.css">
</head>
<body class="light-theme">
    <?php include 'includes/navbar_group.php'; ?>

    <div class="chat-layout">
        <aside class="grupo-usuarios">
            <div class="theme-toggle">
                <button id="theme-toggle-btn" title="Cambiar tema">
                    <span class="theme-icon">🌙</span>
                </button>
            </div>
            <h2>Usuarios</h2>
            <div class="usuarios-lista" id="usuarios-lista">
                <ul>
                    <?php foreach ($usuarios as $u): ?>
                        <li title="Última actividad: <?php echo $u['ultima_actividad'] ? date('d/m/Y H:i', strtotime($u['ultima_actividad'])) : 'Nunca'; ?>">
                            <?php
                            $clase = usuario_activo($u['ultima_actividad']) ? 'online' : 'offline';
                            $foto = htmlspecialchars($u['foto_perfil'] ?: 'img/perfiles/user-default.png');
                            $nombre = htmlspecialchars($u['nombre']);
                            $rol = htmlspecialchars($u['rol']);
                            $ultima_actividad = $u['ultima_actividad'] ? time() - strtotime($u['ultima_actividad']) : -1;
                            $tiempo_conectado = usuario_activo($u['ultima_actividad']) ? 'Conectado ahora' : ($ultima_actividad > 0 ? 'Activo hace ' . floor($ultima_actividad / 60) . ' min' : 'Nunca conectado');
                            ?>
                            <img src="<?php echo $foto; ?>" alt="Perfil" class="user-img">
                            <span class="status <?php echo $clase; ?>"></span>
                            <div class="user-info">
                                <span class="user-name"><?php echo "$nombre ($rol)"; ?></span>
                                <span class="user-status"><?php echo $tiempo_conectado; ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <h2>Solicitudes Pendientes</h2>
            <div class="solicitudes-lista">
                <ul>
                    <?php if (empty($solicitudes)): ?>
                        <li>No tienes solicitudes pendientes.</li>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <li>
                                Invitación a: <?php echo htmlspecialchars($solicitud['grupo_nombre']); ?>
                                <form method="POST" action="gestionar_solicitudes.php" class="solicitud-form">
                                    <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">
                                    <button type="submit" name="accion" value="aceptar" class="btn-aceptar">Aceptar</button>
                                    <button type="submit" name="accion" value="rechazar" class="btn-rechazar">Rechazar</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <h2 class="grupos-title">Grupos</h2>
            <button class="btn-mostrar-grupos" onclick="mostrarGrupos()">Ver Grupos</button>

            <h2>Crear Grupo</h2>
            <form method="POST" action="">
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <input type="text" name="nombre_grupo" placeholder="Nombre del grupo" required>
                <select name="usuarios[]" multiple required title="Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples usuarios">
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="form-hint">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples usuarios.</p>
                <button type="submit" name="crear_grupo">Crear</button>
            </form>
        </aside>

        <section class="chat-mensajes">
            <h2 data-grupo="<?php echo $grupo_id == 1 ? 'general' : 'personalizado'; ?>">
                <?php echo $grupo_id == 1 ? 'Grupo General' : 'Grupo Personalizado'; ?>
                <button class="btn-mostrar-miembros" onclick="mostrarMiembros()">👥 Ver Miembros</button>
            </h2>

            <?php if (!empty($mensajes_anclados)): ?>
                <div class="mensajes-anclados">
                    <h3>Mensajes Anclados</h3>
                    <?php foreach ($mensajes_anclados as $msg): ?>
                        <div class="mensaje mensaje-anclado">
                            <b><?php echo htmlspecialchars($msg['nombre']); ?>:</b>
                            <?php echo htmlspecialchars($msg['mensaje']); ?>
                            <span class="fecha"><?php echo date('H:i', strtotime($msg['fecha_envio'])); ?></span>
                            <form method="POST" action="" class="anclar-form">
                                <input type="hidden" name="mensaje_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="desanclar_mensaje" title="Desanclar mensaje">✖</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="escribiendo-indicador" id="escribiendo-indicador"></div>

            <div class="mensajes" id="mensajes">
                <?php foreach ($mensajes as $msg): ?>
                    <div class="mensaje<?php echo $msg['usuario_id'] == $id_usuario ? ' mensaje-propio' : ''; ?>" data-message-id="<?php echo $msg['id']; ?>">
                        <?php if ($msg['usuario_id'] != $id_usuario): ?>
                            <div class="mensaje-header">
                                <img src="<?php echo htmlspecialchars($msg['foto_perfil'] ?: 'img/perfiles/user-default.png'); ?>" alt="Perfil" class="mensaje-img">
                                <b><?php echo htmlspecialchars($msg['nombre'] ?? 'Usuario Desconocido'); ?>:</b>
                            </div>
                        <?php endif; ?>
                        <div class="mensaje-contenido">
                            <?php echo htmlspecialchars($msg['mensaje']); ?>
                            <span class="fecha"><?php echo date('H:i', strtotime($msg['fecha_envio'])); ?></span>
                            <?php if ($msg['usuario_id'] == $id_usuario): ?>
                                <form method="POST" action="" class="anclar-form">
                                    <input type="hidden" name="mensaje_id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" name="anclar_mensaje" title="Anclar mensaje">📌</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" action="" id="form-chat">
                <input type="text" name="mensaje" placeholder="Escribe tu mensaje..." required>
                <button type="submit">Enviar</button>
            </form>
        </section>
    </div>

    <div class="modal" id="modal-grupos">
        <div class="modal-content">
            <span class="modal-close" onclick="cerrarModal('modal-grupos')">×</span>
            <h2>Grupos</h2>
            <ul>
                <li>
                    <a href="group.php?grupo=1">
                        <span class="grupo-nombre">Grupo General</span>
                        <?php
                        $stmt = $conexion->prepare("
                            SELECT COUNT(*) as mensajes_no_leidos
                            FROM mensajes_grupo mg
                            LEFT JOIN mensajes_grupo_leidos mgl ON mg.id = mgl.mensaje_id AND mgl.usuario_id = ?
                            WHERE mg.grupo_id = 1 AND (mgl.leido = 0 OR mgl.leido IS NULL)
                        ");
                        $stmt->bind_param("i", $id_usuario);
                        $stmt->execute();
                        $no_leidos_general = $stmt->get_result()->fetch_assoc()['mensajes_no_leidos'];
                        error_log("Mensajes no leídos en Grupo General para usuario $id_usuario: $no_leidos_general");
                        $stmt->close();
                        if ($no_leidos_general > 0) {
                            echo "<span class='badge'>$no_leidos_general</span>";
                        }
                        ?>
                    </a>
                </li>
                <?php foreach ($grupos as $grupo): ?>
                    <li>
                        <a href="group.php?grupo=<?php echo $grupo['id']; ?>">
                            <span class="grupo-nombre"><?php echo htmlspecialchars($grupo['nombre']); ?></span>
                            <?php if ($grupo['mensajes_no_leidos'] > 0): ?>
                                <span class="badge"><?php echo $grupo['mensajes_no_leidos']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="modal" id="modal-miembros">
        <div class="modal-content">
            <span class="modal-close" onclick="cerrarModal('modal-miembros')">×</span>
            <h2>Miembros del grupo</h2>
            <ul>
                <?php foreach ($miembros_grupo as $miembro): ?>
                    <li>
                        <?php
                        $clase = usuario_activo($miembro['ultima_actividad']) ? 'online' : 'offline';
                        $foto = htmlspecialchars($miembro['foto_perfil'] ?: 'img/perfiles/user-default.png');
                        $nombre = htmlspecialchars($miembro['nombre']);
                        $rol = htmlspecialchars($miembro['rol']);
                        $ultima_actividad = $miembro['ultima_actividad'] ? time() - strtotime($miembro['ultima_actividad']) : -1;
                        $tiempo_conectado = usuario_activo($miembro['ultima_actividad']) ? 'Conectado ahora' : ($ultima_actividad > 0 ? 'Activo hace ' . floor($ultima_actividad / 60) . ' min' : 'Nunca conectado');
                        ?>
                        <img src="<?php echo $foto; ?>" alt="Perfil" class="user-img">
                        <span class="status <?php echo $clase; ?>"></span>
                        <div class="user-info">
                            <span class="user-name"><?php echo "$nombre ($rol)"; ?></span>
                            <span class="user-status"><?php echo $tiempo_conectado; ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const mensajesBox = document.getElementById("mensajes");
        let lastMessageId = 0;

        const existingMessages = mensajesBox.querySelectorAll('.mensaje');
        if (existingMessages.length > 0) {
            lastMessageId = parseInt(existingMessages[existingMessages.length - 1].dataset.messageId || 0);
        }
        console.log('Initial lastMessageId:', lastMessageId);

        if (mensajesBox) {
            mensajesBox.scrollTop = mensajesBox.scrollHeight;
        }

        const form = document.getElementById("form-chat");
        const mensajeInput = form.querySelector('input[name="mensaje"]');
        let escribiendoTimeout;

        if (form) {
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                if (mensajeInput.value.trim() === "") return;

                const datos = new FormData(form);
                fetch("group.php?grupo=<?php echo $grupo_id; ?>", { 
                    method: "POST", 
                    body: datos 
                })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`Error del servidor: ${res.status} ${res.statusText}`);
                        }
                        return res.text();
                    })
                    .then(data => {
                        mensajeInput.value = "";
                        actualizarMensajes();
                        clearTimeout(escribiendoTimeout);
                        actualizarEscribiendo(false);
                    })
                    .catch(err => {
                        console.error("Error al enviar el mensaje:", err);
                        alert("No se pudo enviar el mensaje. Revisa la consola para más detalles.");
                    });
            });

            mensajeInput.addEventListener("input", function() {
                clearTimeout(escribiendoTimeout);
                actualizarEscribiendo(true);
                escribiendoTimeout = setTimeout(() => {
                    actualizarEscribiendo(false);
                }, 2000);
            });
        }

        function actualizarMensajes() {
            const startTime = performance.now();
            fetch("mensajes_grupo_ajax.php?grupo=<?php echo $grupo_id; ?>")
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Error al obtener mensajes: ${res.status} ${res.statusText}`);
                    }
                    return res.json();
                })
                .then(data => {
                    const endTime = performance.now();
                    console.log(`Tiempo de respuesta de mensajes_grupo_ajax.php: ${(endTime - startTime).toFixed(2)}ms`);

                    if (data.status === 'error') {
                        console.error('Error en el servidor:', data.error);
                        return;
                    }

                    if (data.status === 'empty') {
                        console.log('No se encontraron mensajes nuevos.');
                        return;
                    }

                    const messages = data.messages;
                    console.log('Mensajes recibidos:', messages);

                    if (!messages || messages.length === 0) {
                        console.log('No hay mensajes para mostrar.');
                        return;
                    }

                    const newMessages = messages.filter(msg => msg.id > lastMessageId);
                    console.log('Mensajes nuevos:', newMessages);

                    if (newMessages.length === 0) {
                        console.log('No hay mensajes nuevos para agregar.');
                        return;
                    }

                    lastMessageId = messages[messages.length - 1].id;
                    console.log('Nuevo lastMessageId:', lastMessageId);

                    const isScrolledToBottom = mensajesBox.scrollHeight - mensajesBox.scrollTop - mensajesBox.clientHeight < 10;

                    newMessages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `mensaje${msg.usuario_id == <?php echo $id_usuario; ?> ? ' mensaje-propio' : ''}`;
                        messageDiv.dataset.messageId = msg.id;

                        if (msg.usuario_id != <?php echo $id_usuario; ?>) {
                            const headerDiv = document.createElement('div');
                            headerDiv.className = 'mensaje-header';
                            const img = document.createElement('img');
                            img.src = msg.foto_perfil;
                            img.alt = 'Perfil';
                            img.className = 'mensaje-img';
                            const bold = document.createElement('b');
                            bold.textContent = `${msg.nombre}:`;
                            headerDiv.appendChild(img);
                            headerDiv.appendChild(bold);
                            messageDiv.appendChild(headerDiv);
                        }

                        const contentDiv = document.createElement('div');
                        contentDiv.className = 'mensaje-contenido';
                        contentDiv.textContent = msg.mensaje;

                        const fechaSpan = document.createElement('span');
                        fechaSpan.className = 'fecha';
                        fechaSpan.textContent = msg.fecha_envio;
                        contentDiv.appendChild(fechaSpan);

                        if (msg.usuario_id == <?php echo $id_usuario; ?>) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '';
                            form.className = 'anclar-form';
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'mensaje_id';
                            input.value = msg.id;
                            const button = document.createElement('button');
                            button.type = 'submit';
                            button.name = 'anclar_mensaje';
                            button.title = 'Anclar mensaje';
                            button.textContent = '📌';
                            form.appendChild(input);
                            form.appendChild(button);
                            contentDiv.appendChild(form);
                        }

                        messageDiv.appendChild(contentDiv);
                        mensajesBox.appendChild(messageDiv);
                        console.log('Mensaje agregado al DOM:', msg);
                    });

                    if (isScrolledToBottom) {
                        mensajesBox.scrollTop = mensajesBox.scrollHeight;
                    }
                })
                .catch(err => console.error("Error al actualizar mensajes:", err));
        }

        function actualizarEscribiendo(escribiendo) {
            fetch("actualizar_escribiendo.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "usuario_id=<?php echo $id_usuario; ?>&grupo_id=<?php echo $grupo_id; ?>&escribiendo=" + (escribiendo ? 1 : 0)
            })
                .catch(err => console.error("Error al actualizar estado de escribiendo:", err));
        }

        function verificarEscribiendo() {
            fetch("verificar_escribiendo.php?grupo_id=<?php echo $grupo_id; ?>&usuario_id=<?php echo $id_usuario; ?>")
                .then(res => res.json())
                .then(data => {
                    const indicador = document.getElementById("escribiendo-indicador");
                    if (data.escribiendo.length > 0) {
                        indicador.innerHTML = data.escribiendo.join(", ") + " está escribiendo...";
                        indicador.style.display = "block";
                    } else {
                        indicador.style.display = "none";
                    }
                })
                .catch(err => console.error("Error al verificar escribiendo:", err));
        }

        function actualizarUsuarios() {
            fetch("actualizar_usuarios.php")
                .then(res => res.text())
                .then(html => {
                    const usuariosLista = document.getElementById("usuarios-lista");
                    if (usuariosLista) {
                        usuariosLista.innerHTML = html;
                    }
                })
                .catch(err => console.error("Error al actualizar usuarios:", err));
        }

        setInterval(() => {
            actualizarUsuarios();
            if (mensajesBox) actualizarMensajes();
            verificarEscribiendo();
        }, 400);

        const themeToggleBtn = document.getElementById("theme-toggle-btn");
        const body = document.body;
        const themeIcon = themeToggleBtn.querySelector(".theme-icon");

        themeToggleBtn.addEventListener("click", () => {
            body.classList.toggle("light-theme");
            body.classList.toggle("dark-theme");
            const isDark = body.classList.contains("dark-theme");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            themeIcon.textContent = isDark ? "☀️" : "🌙";
        });

        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            body.classList.remove("light-theme");
            body.classList.add("dark-theme");
            themeIcon.textContent = "☀️";
        }
    });

    function mostrarGrupos() {
        document.getElementById("modal-grupos").style.display = "block";
    }

    function mostrarMiembros() {
        document.getElementById("modal-miembros").style.display = "block";
    }

    function cerrarModal(modalId) {
        document.getElementById(modalId).style.display = "none";theme-icon
    }

    window.onclick = function(event) {
        const modalGrupos = document.getElementById("modal-grupos");
        const modalMiembros = document.getElementById("modal-miembros");
        if (event.target == modalGrupos) {
            modalGrupos.style.display = "none";
        }
        if (modalMiembros && event.target == modalMiembros) {
            modalMiembros.style.display = "none";
        }
    };
    </script>
</body>
</html>