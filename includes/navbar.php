<?php
if (!isset($_SESSION)) session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}
?>

<link rel="stylesheet" href="css/nav.css">
<nav class="navbar">
    <img src="img/logo.png" alt="Logo del ITFAM" class="navbar-logo">
    <ul class="nav-links">
        <li><a href="index.php">Inicio</a></li>
        <li><a href="#">Nosotros</a></li>
        <li><a href="notice.php">Noticias</a></li>
        <li><a href="#">Actividades</a></li>
        <li><a href="group.php">Grupo General <span class="badge badge-light"></span></a></li>
        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
    <div class="user-icon" onclick="togglePerfil()">
        <?php
        $foto_perfil = isset($_SESSION['foto']) && !empty($_SESSION['foto']) && file_exists('img/perfiles/' . $_SESSION['foto'])
            ? 'img/perfiles/' . $_SESSION['foto']
            : 'img/perfiles/user-default.png';
        ?>
        <img src="<?php echo $foto_perfil; ?>" alt="Usuario" class="user-img">
    </div>
</nav>

<div class="perfil-popup fadeOut" id="perfilPopup">
    <p><strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></strong></p>
    <p><?php echo htmlspecialchars($_SESSION['rol'] ?? 'Rol no definido'); ?></p>
    <a href="profile.php">Ver perfil</a>
</div>

<script>
function togglePerfil() {
    var popup = document.getElementById("perfilPopup");
    popup.classList.toggle("show");
    popup.classList.toggle("fadeOut");
}
</script>