<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';
require_once 'includes/funciones.php';
include 'includes/navbar_chat.php';

$id_usuario   = $_SESSION['id_usuario'];
$nombre       = $_SESSION['nombre'];
$rol          = $_SESSION['rol'];
// Si se selecciona un usuario para chatear, se obtiene en GET
$chat_con_id = isset($_GET['usuario']) ? intval($_GET['usuario']) : null;

// Obtener lista de usuarios con los que ya hay conversación
$conversaciones_existentes = [];
$sqlRecientes = "SELECT DISTINCT
                    CASE
                        WHEN emisor_id = $id_usuario THEN receptor_id
                        ELSE emisor_id
                    END AS otro_usuario_id
                FROM mensajes
                WHERE emisor_id = $id_usuario OR receptor_id = $id_usuario";
$resRecientes = $conexion->query($sqlRecientes);
while ($row = $resRecientes->fetch_assoc()) {
    $conversaciones_existentes[] = $row['otro_usuario_id'];
}
$lista_ids_existentes = implode(',', $conversaciones_existentes);
if (empty($lista_ids_existentes)) {
    $lista_ids_existentes = '0'; // Para evitar errores si no hay conversaciones
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat ITFAM</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body class="light-theme">
<div class="chat-layout">
    <aside class="sidebar">
        <div class="theme-toggle">
            <button id="theme-toggle-btn" title="Cambiar tema">
                <span class="theme-icon">🌙</span>
            </button>
        </div>
        <h3>Usuarios</h3>
        <select id="filtroSidebar" class="filtro-rol" onchange="cambiarRol(this.value)">
            <option value="">Todos</option>
            <option value="Estudiante">Estudiantes</option>
            <option value="Profesor">Profesores</option>
            <option value="Orientador">Orientadores</option>
        </select>
        <select id="filtroCurso" class="filtro-curso hidden" onchange="filtrarAmbos(document.getElementById('filtroSidebar').value)">
            <option value="">Todos los Cursos</option>
            <?php
            $cursos = [
                "4 Instalaciones Electricas", "4 Mecatrónica", "4 Cuidados de Enfermería y Promoción de la Salud",
                "4 Electromecánica de Vehículos", "4 Equipos Electrónicos", "4A Desarrollo y Administración de Aplicaciones Informáticas",
                "4A Gestión Administrativa y Tributaria", "48 Desarrollo y Administración de Aplicaciones Informáticas",
                "4B Gestión Administrativa y Tributaria", "5 Electromecánica de Vehículos", "5 Equipos Electrónicos",
                "5 Instalaciones Electricas", "5 Mecatrónica", "5A Cuidados de Enfermería y Promoción de la Salud",
                "5A Desarrollo y Administración de Aplicaciones Informáticas", "5A Gestión Administrativa y Tributaria",
                "5B Desarrollo y Administración de Aplicaciones Informáticas", "5B Gestión Administrativa y Tributaria",
                "5C Desarrollo y Administración de Aplicaciones Informáticas", "5C Gestión Administrativa y Tributaria",
                "6 A Cuidados de Enfermería y Promoción de la Salud", "6 B Cuidados de Enfermería y Promoción de la Salud",
                "6 Electromecánica de Vehículos", "6 Equipos Electrónicos", "6 Instalaciones Electricas", "6 Mecatrónica",
                "6A Desarrollo y Administración de Aplicaciones Informáticas", "6A Gestión Administrativa y Tributaria",
                "68 Desarrollo y Administración de Aplicaciones Informáticas", "6B Gestión Administrativa y Tributaria",
                "6C Desarrollo y Administración de Aplicaciones Informáticas", "6C Gestión Administrativa y Tributaria"
            ];
            foreach ($cursos as $curso) {
                echo "<option value='".htmlspecialchars($curso)."'>".htmlspecialchars($curso)."</option>";
            }
            ?>
        </select>
        <div class="busqueda">
            <input type="text" id="buscarUsuarioSidebar" placeholder="Buscar por nombre o apellido...">
        </div>
        <div class="lista-scroll">
            <ul class="listaUsuarios" id="sidebarUsers">
                <?php
                $sqlSide = "SELECT id, nombre, apellido, rol, curso, foto_perfil FROM usuarios WHERE id != $id_usuario ORDER BY nombre ASC";
                $resSide = $conexion->query($sqlSide);
                while ($u = $resSide->fetch_assoc()) {
                    $u_nom = htmlspecialchars($u['nombre']);
                    $u_ape = isset($u['apellido']) ? htmlspecialchars($u['apellido']) : "";
                    $u_rol = htmlspecialchars($u['rol']);
                    $u_curso = htmlspecialchars($u['curso'] ?? "");
                    $dataNombres = strtolower($u_nom . " " . $u_ape);
                    $u_foto_db = htmlspecialchars($u['foto_perfil']);
                    $u_foto = 'img/perfiles/user-default.png';
                    if (!empty($u_foto_db)) {
                        if (strpos($u_foto_db, 'img/') === 0) {
                            $u_foto = $u_foto_db;
                        } else {
                            $u_foto = 'img/perfiles/' . $u_foto_db;
                        }
                    }
                    echo "<li data-rol='$u_rol' data-curso='$u_curso' data-nombres='$dataNombres'>
                            <a href='index.php?usuario={$u['id']}' class='chat-preview'>
                                <img src='$u_foto' alt='Perfil' class='avatar-mini'>
                                <div class='preview-info'>
                                    <strong>$u_nom $u_ape</strong>
                                    <span class='rol-usuario-sidebar'>(" . $u_rol . ")</span>
                                </div>
                            </a>
                        </li>";
                }
                ?>
            </ul>
        </div>
    </aside>

    <section class="chat-section">
        <?php if ($chat_con_id): ?>
            <?php
            $info = $conexion->query("SELECT nombre, apellido FROM usuarios WHERE id = $chat_con_id");
            $fila = $info->fetch_assoc();
            $nombre_chat   = $fila['nombre'] ?? 'Desconocido';
            $apellido_chat = $fila['apellido'] ?? '';
            $conexion->query("UPDATE mensajes SET leido = 1 WHERE receptor_id = $id_usuario AND emisor_id = $chat_con_id");
            ?>
            <div class="header-chat">
                <button onclick="salirDelChat()" class="btn-salir">← Volver</button>
                <h2>Chat con <?= htmlspecialchars($nombre_chat . " " . $apellido_chat) ?></h2>
            </div>
            <div class="chat-box" id="mensajes">
                <?php
                $consulta = "
                    SELECT m.*, u.nombre, u.apellido, m.fecha_envio
                    FROM mensajes m
                    JOIN usuarios u ON m.emisor_id = u.id
                    WHERE
                        (m.emisor_id = $id_usuario AND m.receptor_id = $chat_con_id)
                        OR
                        (m.emisor_id = $chat_con_id AND m.receptor_id = $id_usuario)
                    ORDER BY m.fecha_envio ASC
                ";
                $resMsg = $conexion->query($consulta);
                while ($msg = $resMsg->fetch_assoc()) {
                    $tipo    = $msg['emisor_id'] == $id_usuario ? 'out' : 'in';
                    $mensaje = htmlspecialchars($msg['mensaje']);
                    $hora    = date("H:i", strtotime($msg['fecha_envio']));
                    $estado  = "";
                    if ($tipo === 'out') {
                        $estado = $msg['leido'] ? "<span class='cotejo cotejo-leido'>✓✓</span>" : "<span class='cotejo'>✓</span>";
                    }
                    echo "<div class='mensaje $tipo'>
                            $mensaje
                            <div class='info-mensaje'><span class='hora'>$hora</span> $estado</div>
                        </div>";
                }
                ?>
            </div>
            <form id="form-chat" class="form-chat">
                <input type="hidden" name="receptor_id" value="<?= $chat_con_id ?>">
                <input type="text" name="mensaje" id="mensaje" placeholder="Escribe tu mensaje..." autocomplete="off" required>
                <button type="submit">Enviar</button>
            </form>
        <?php else: ?>
            <div id="seleccion-container" class="usuarios-container">
                <h2>Selecciona un usuario para chatear</h2>
                <div class="filtros">
                    <button class="btn-filtro" onclick="filtrarAmbos('')">Todos</button>
                    <button class="btn-filtro" onclick="filtrarAmbos('Estudiante')">Estudiantes</button>
                    <button class="btn-filtro" onclick="filtrarAmbos('Profesor')">Profesores</button>
                    <button class="btn-filtro" onclick="filtrarAmbos('Orientador')">Orientadores</button>
                </div>
                <div class="lista-scroll" id="recientesBox" style="max-height: 400px;">
                    <ul class="listaUsuarios" id="listaRecientes">
                        <?php
                        $sqlRecientesList = "SELECT DISTINCT
                                                CASE
                                                    WHEN m.emisor_id = $id_usuario THEN m.receptor_id
                                                    ELSE m.emisor_id
                                                END AS otro_usuario_id,
                                                u.nombre, u.apellido, u.rol, u.foto_perfil,
                                                MAX(m.fecha_envio) AS ultimo_mensaje,
                                                (SELECT mensaje FROM mensajes
                                                WHERE (emisor_id = m.emisor_id AND receptor_id = m.receptor_id)
                                                    OR (emisor_id = m.receptor_id AND receptor_id = m.emisor_id)
                                                ORDER BY fecha_envio DESC
                                                LIMIT 1
                                                ) AS mensaje_preview
                                            FROM mensajes m
                                            JOIN usuarios u ON
                                                (m.emisor_id = u.id AND m.receptor_id = $id_usuario) OR
                                                (m.receptor_id = u.id AND m.emisor_id = $id_usuario)
                                            WHERE m.emisor_id = $id_usuario OR m.receptor_id = $id_usuario
                                            GROUP BY otro_usuario_id
                                            ORDER BY ultimo_mensaje DESC
                                            LIMIT 5";
                        $resRecientesList = $conexion->query($sqlRecientesList);
                        while ($r = $resRecientesList->fetch_assoc()) {
                            $r_nom = htmlspecialchars($r['nombre']);
                            $r_ape = htmlspecialchars($r['apellido'] ?? '');
                            $r_rol = htmlspecialchars($r['rol']);
                            $r_foto_db = htmlspecialchars($r['foto_perfil']);
                            $r_foto = 'img/perfiles/user-default.png';
                            if (!empty($r_foto_db)) {
                                if (strpos($r_foto_db, 'img/') === 0) {
                                    $r_foto = $r_foto_db;
                                } else {
                                    $r_foto = 'img/perfiles/' . $r_foto_db;
                                }
                            }
                            $dataNombresRecientes = strtolower($r_nom . " " . $r_ape);
                            echo "<li data-rol='$r_rol' data-nombres='$dataNombresRecientes'>
                                    <a href='index.php?usuario={$r['otro_usuario_id']}' class='chat-preview'>
                                        <img src='$r_foto' alt='Perfil' class='avatar-mini'>
                                        <div class='preview-info'>
                                            <strong>$r_nom $r_ape</strong>
                                        </div>
                                    </a>
                                </li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<script>
/* ========== Filtro por rol y curso ========== */
function cambiarRol(rol) {
    const filtroCurso = document.getElementById('filtroCurso');
    if (rol === 'Estudiante') {
        filtroCurso.classList.remove('hidden');
    } else {
        filtroCurso.classList.add('hidden');
    }
    filtrarAmbos(rol);
}

function filtrarCurso(curso) {
    document.querySelectorAll('#sidebarUsers li').forEach(li => {
        const userCurso = li.dataset.curso || "";
        li.style.display = (!curso || userCurso === curso) ? "block" : "none";
    });
    filtrarAmbos(document.getElementById('filtroSidebar').value);
}

/* ========== Filtro en ambas listas ========== */
function filtrarAmbos(rol) {
    const listasUsuarios = document.querySelectorAll('.listaUsuarios');
    listasUsuarios.forEach(lista => {
        lista.querySelectorAll('li').forEach(li => {
            const userRol = li.dataset.rol;
            const rolCoincide = !rol || userRol === rol;
            li.style.display = rolCoincide ? 'block' : 'none';
            if (lista.id === 'sidebarUsers') {
                const rolSpan = li.querySelector('a .rol-usuario-sidebar');
                if (rolSpan) {
                    rolSpan.style.display = (rol === '' || rol === 'Todos') ? 'inline' : 'none';
                }
            }
        });
    });
    const filtroSelect = document.getElementById("filtroSidebar");
    if (filtroSelect) { filtroSelect.value = rol; }
    filtrarRecientes(rol, document.getElementById("buscarUsuarioSidebar").value.trim().toLowerCase());
}

/* ========== Salir del Chat ========== */
function salirDelChat() {
    window.location.href = "index.php";
}

document.addEventListener('DOMContentLoaded', () => {
    /* ========== Búsqueda por nombre/apellido ========== */
    document.getElementById("buscarUsuarioSidebar")?.addEventListener("input", function() {
        const busqueda = this.value.trim().toLowerCase();
        filtrarUsuarios(busqueda);
        filtrarRecientes(document.getElementById("filtroSidebar").value, busqueda);
    });

    document.getElementById("buscarUsuarioSeleccion")?.addEventListener("input", function() {
        const busqueda = this.value.trim().toLowerCase();
        filtrarUsuarios(busqueda);
    });

    function filtrarUsuarios(busqueda) {
        const listasUsuarios = document.querySelectorAll('.listaUsuarios');
        listasUsuarios.forEach(lista => {
            lista.querySelectorAll('li').forEach(li => {
                const nombres = li.dataset.nombres || li.querySelector('.preview-info strong').textContent.toLowerCase();
                li.style.display = nombres.includes(busqueda) ? "block" : "none";
            });
        });
    }

    /* ========== Envío de mensaje vía AJAX y actualización ========== */
    const form = document.getElementById("form-chat");
    const inputMsg = document.getElementById("mensaje");
    const chatBox = document.getElementById("mensajes");
    const recientesBox = document.getElementById("listaRecientes");

    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            if (inputMsg.value.trim() === "") return;
            const datos = new FormData(form);
            fetch("enviar_mensaje.php", { method: "POST", body: datos })
                .then(res => res.text())
                .then(res => {
                    inputMsg.value = "";
                    actualizarChat();
                })
                .catch(err => console.error("Error al enviar:", err));
        });
    }

    function actualizarChat() {
        fetch("mensajes_ajax.php?usuario=<?= $chat_con_id ?>")
            .then(res => res.text())
            .then(html => {
                if (chatBox) {
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
    }

    let filtroRecientes = '';
    let busquedaRecientes = '';

    function filtrarRecientes(rol = '', busqueda = '') {
        let url = "recientes_ajax.php";
        const params = [];
        if (rol) {
            params.push(`rol=${rol}`);
        }
        if (busqueda) {
            params.push(`busqueda=${busqueda}`);
        }
        if (params.length > 0) {
            url += "?" + params.join("&");
        }

        fetch(url)
            .then(res => res.text())
            .then(html => {
                if (recientesBox) {
                    recientesBox.innerHTML = html;
                }
            });
    }

    function actualizarRecientes() {
        filtrarRecientes(document.getElementById("filtroSidebar").value, document.getElementById("buscarUsuarioSidebar").value.trim().toLowerCase());
    }

    /* ========== Cambio de Tema Claro/Oscuro ========== */
    const themeToggleBtn = document.getElementById("theme-toggle-btn");
    const body = document.body;
    const themeIcon = themeToggleBtn.querySelector(".theme-icon");

    if (themeToggleBtn) {
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
    }

    // Actualización automática cada 400ms
    setInterval(() => {
        if (chatBox) actualizarChat();
        if (recientesBox && (!document.activeElement || document.activeElement.id !== 'buscarUsuarioSidebar' && document.activeElement.id !== 'buscarUsuarioSeleccion')) {
            actualizarRecientes();
        }
    }, 400);

    // Cargar lista de recientes al cargar la página
    actualizarRecientes();
});
</script>
</body>
</html>