<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';

$id_usuario = $_SESSION['id_usuario'];
$filtro_rol = isset($_GET['rol']) ? $_GET['rol'] : '';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

echo "<script>console.log('Valor de filtro_rol en recientes_ajax.php: " . json_encode($filtro_rol) . "');</script>";
echo "<script>console.log('Valor de busqueda en recientes_ajax.php: " . json_encode($busqueda) . "');</script>";

$condicion_rol = '';
if (!empty($filtro_rol) && $filtro_rol != 'Todos') {
    $condicion_rol = "AND u.rol = '$filtro_rol'";
}

$condicion_busqueda = '';
if (!empty($busqueda)) {
    $condicion_busqueda = "AND (u.nombre LIKE '%$busqueda%' OR u.apellido LIKE '%$busqueda%')";
}

// Consulta para conversaciones recientes
$sql = "
    SELECT u.id, u.nombre, u.apellido, u.foto_perfil, u.rol, m.mensaje, m.fecha_envio,
            (SELECT COUNT(*) FROM mensajes
             WHERE ((emisor_id = $id_usuario AND receptor_id = u.id)
                    OR (emisor_id = u.id AND receptor_id = $id_usuario))
               AND leido = 0 AND emisor_id != $id_usuario) as no_leidos
    FROM mensajes m
    JOIN usuarios u ON (
        CASE
            WHEN m.emisor_id = $id_usuario THEN m.receptor_id = u.id
            WHEN m.receptor_id = $id_usuario THEN m.emisor_id = u.id
        END
    )
    WHERE m.id IN (
        SELECT MAX(id)
        FROM mensajes
        WHERE emisor_id = $id_usuario OR receptor_id = $id_usuario
        GROUP BY LEAST(emisor_id, receptor_id), GREATEST(emisor_id, receptor_id)
    )
    $condicion_rol
    $condicion_busqueda
";

$sql .= " ORDER BY m.fecha_envio DESC";

echo "<script>console.log('Consulta SQL en recientes_ajax.php: " . json_encode($sql) . "');</script>";

$res = $conexion->query($sql);

while ($r = $res->fetch_assoc()) {
    $r_id     = $r['id'];
    $r_nombre = htmlspecialchars($r['nombre']);
    $r_apellido = htmlspecialchars($r['apellido'] ?? '');
    $r_foto_db = htmlspecialchars($r['foto_perfil'] ?: '');
    $r_foto = 'img/perfiles/user-default.png';
    if (!empty($r_foto_db)) {
        if (strpos($r_foto_db, 'img/') === 0) {
            $r_foto = $r_foto_db;
        } else {
            $r_foto = 'img/perfiles/' . $r_foto_db;
        }
    }
    $r_men   = htmlspecialchars($r['mensaje']);
    if (strlen($r_men) > 115) {
        $r_men = substr($r_men, 0, 115) . "...";
    }
    $r_fecha = date("H:i", strtotime($r['fecha_envio']));
    $rol_r   = htmlspecialchars($r['rol']);
    $badge   = ($r['no_leidos'] > 0) ? "<span class='badge'>{$r['no_leidos']}</span>" : "";
    $nombre_formateado = $r['no_leidos'] > 0 ? "<strong>$r_nombre $r_apellido</strong>" : "$r_nombre $r_apellido";

    echo "<li data-rol='$rol_r' data-nombres='" . strtolower($r_nombre . " " . $r_apellido) . "'>
                <a href='index.php?usuario=$r_id' class='chat-preview'>
                    <img src='$r_foto' alt='Perfil' class='avatar-mini'>
                    <div class='preview-info'>
                        $nombre_formateado
                        <span class='mensaje-preview'>" . $r_men . "</span>
                    </div>
                    <div class='preview-right'>
                        <span class='preview-date'>$r_fecha</span>
                        $badge
                    </div>
                </a>
            </li>";
}
?>