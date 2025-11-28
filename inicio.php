<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="styles/inicio_chat.css">
</head>
<body>
    <div class="contenedor">
        <div class="lado-izquierdo">
            <img src="img/ITFAM.png" alt="Imagen de inicio de sesión" class="imagen-inicio-sesion">
            <h2>¿Aún no tienes cuenta?</h2>
            <p>Regístrate para unirte a la familia ITFAM</p>
            <button id="botonRegistro" class="animacion-boton">Registrarse</button>
        </div>
        <div class="lado-derecho">
            <h2>Iniciar sesión</h2>
            <!-- Formulario para iniciar sesión -->
            <form action="formulario.php" method="POST">
                <input type="email" name="correo" placeholder="Correo electrónico" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <button type="submit" name="guardar">Ingresar</button>
            </form>
        </div>
    </div>
    <script src="javascript/inicio_chat.js"></script>
</body>
</html>