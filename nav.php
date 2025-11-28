<nav>
    <img src="img/Logo_New_by_SR-Slogan-removebg-preview.png" alt="Logo del ITFAM">
    <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="login/Inicio.html">Nosotros</a></li>
        <li><a href="notice.php">Noticias</a></li>
        <li><a href="#">Actvidades</a></li>
        <li><a href="Mensajeria/index.php">Mensajes<span class="badge badge-light">14</span></a></li>
        <li><a href="#">Chat</a></li>
    </ul>

    <div class="usuario-info">
        <?php if (isset($_SESSION['id_usuario'])): ?>
            <img src="img/acceso.png" alt="Usuario" id="usuario-imagen">
            <div class="usuario-detalles" id="usuario-detalles">
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                <p>Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
                <button id="boton-foto-perfil">Subir Foto de Perfil</button>
            </div>
        <?php else: ?>
            <a href="login.php">Iniciar Sesión</a> <?php endif; ?>
    </div>
</nav>
<script>
    document.getElementById('usuario-imagen').addEventListener('click', function() {
        document.getElementById('usuario-detalles').style.display = 'block';
    });

    document.getElementById('boton-foto-perfil').addEventListener('click', function() {
        // Aquí va el código para manejar la subida de la foto de perfil
        alert('Funcionalidad para subir foto de perfil aquí.');
    });
</script>