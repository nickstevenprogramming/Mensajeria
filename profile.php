<?php
require_once 'includes/auth.php';
require_once 'includes/bd.php';
include 'includes/navbar_group_perfil.php';

$id = $_SESSION['id_usuario'];
$msg = '';

// Obtener datos actuales del usuario
$res = $conexion->query("SELECT * FROM usuarios WHERE id = $id");
$usuario = $res->fetch_assoc();

// Inicializar foto_nombre con el valor de la base de datos o null
$foto_nombre = $usuario['foto_perfil'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $curso = $_POST['curso'] ?? '';
    $foto = $_FILES['foto'] ?? null;

    // Subir nueva foto si se seleccionó
    if ($foto && !empty($foto['name'])) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $foto_nombre = uniqid() . '.' . $ext;
        $upload_path = "img/perfiles/" . $foto_nombre;
        if (!move_uploaded_file($foto['tmp_name'], $upload_path)) {
            $msg = "Error al subir la foto.";
            $foto_nombre = $usuario['foto_perfil'] ?? null; // Mantener la foto actual en caso de error
        }
    }

    // Actualizar datos en la base de datos
    $sql = "UPDATE usuarios SET nombre=?, apellido=?, telefono=?, curso=?, foto_perfil=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $apellido, $telefono, $curso, $foto_nombre, $id);

    if ($stmt->execute()) {
        $_SESSION['nombre'] = $nombre;
        $_SESSION['curso'] = $curso;
        $_SESSION['foto'] = $foto_nombre; // Actualizar la sesión con la nueva foto
        $msg = "Datos actualizados correctamente.";
    } else {
        $msg = "Error al actualizar.";
    }

    // Refrescar datos del usuario después de la actualización
    $res = $conexion->query("SELECT * FROM usuarios WHERE id = $id");
    $usuario = $res->fetch_assoc();
}

// Lista de cursos (la misma que usaste en el registro)
$cursos = [
    "4 Instalaciones Electricas", "4 Mecatrónica", "4 Cuidados de Enfermería y Promoción de la Salud",
    "4 Electromecánica de Vehículos", "4 Equipos Electrónicos", "4A Desarrollo y Administración de Aplicaciones Informáticas",
    "4A Gestión Administrativa y Tributaria", "4B Desarrollo y Administración de Aplicaciones Informáticas",
    "4B Gestión Administrativa y Tributaria", "5 Electromecánica de Vehículos", "5 Equipos Electrónicos",
    "5 Instalaciones Electricas", "5 Mecatrónica", "5A Cuidados de Enfermería y Promoción de la Salud",
    "5A Desarrollo y Administración de Aplicaciones Informáticas", "5A Gestión Administrativa y Tributaria",
    "5B Desarrollo y Administración de Aplicaciones Informáticas", "5B Gestión Administrativa y Tributaria",
    "5C Desarrollo y Administración de Aplicaciones Informáticas", "5C Gestión Administrativa y Tributaria",
    "6A Cuidados de Enfermería y Promoción de la Salud", "6B Cuidados de Enfermería y Promoción de la Salud",
    "6 Electromecánica de Vehículos", "6 Equipos Electrónicos", "6 Instalaciones Electricas", "6 Mecatrónica",
    "6A Desarrollo y Administración de Aplicaciones Informáticas", "6A Gestión Administrativa y Tributaria",
    "6B Desarrollo y Administración de Aplicaciones Informáticas", "6B Gestión Administrativa y Tributaria",
    "6C Desarrollo y Administración de Aplicaciones Informáticas", "6C Gestión Administrativa y Tributaria"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - ITFAM</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar_group_perfil.css">
    <style>
        body {
            overflow: auto;
        }
    </style>
</head>
<body class="light-theme">dark-theme
        
    <div class="form-container">
        <div class="theme-toggle">
            <button id="theme-toggle-btn" title="Cambiar tema">
                <span class="theme-icon">🌙</span>
            </button>
        </div>
        <h2>Mi Perfil</h2>

        <?php if ($msg): ?>
            <div class="alert"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <img src="img/perfiles/<?= htmlspecialchars($usuario['foto_perfil'] ?: 'user-default.png') ?>" width="150" height="150" style="border-radius: 10%; margin-bottom: 1rem; left: 145px; position: relative"><br>

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" required>

            <label>Correo:</label>
            <input type="email" value="<?= htmlspecialchars($usuario['correo']) ?>" disabled>

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">

            <?php if ($usuario['rol'] === 'Estudiante'): ?>
                <label>Curso:</label>
                <select name="curso">
                    <option value="">Selecciona tu curso</option>
                    <?php foreach ($cursos as $curso_opcion): ?>
                        <option value="<?= htmlspecialchars($curso_opcion) ?>" <?= ($usuario['curso'] === $curso_opcion) ? 'selected' : '' ?>><?= htmlspecialchars($curso_opcion) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <label>Rol:</label>
            <input type="text" value="<?= htmlspecialchars($usuario['rol']) ?>" disabled>

            <label>Cambiar foto:</label>
            <input type="file" name="foto" accept="image/*">

            <button type="submit">Guardar cambios</button>
        </form>
    </div>

    <script>
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
    </script>
</body>
</html>