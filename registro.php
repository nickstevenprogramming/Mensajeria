<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="styles/registro_chat.css">
</head>
<body>
    <div class="contenedor">
        <div class="lado-derecho">
            <h2>Registrarse</h2>
            <form action="registrar.php" method="POST">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
                <input type="email" name="correo" placeholder="Correo electrónico" required>
                <input type="tel" name="telefono" placeholder="Teléfono">
                <input type="password" name="contraseña" placeholder="Contraseña" required>

                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="estudiante">Estudiante</option>
                    <option value="profesor">Profesor</option>
                    <option value="orientador">Orientador</option>
                </select>

                <label for="curso">Curso (opcional, solo para estudiantes):</label>
                <input type="text" name="curso" id="curso" placeholder="Ej: 4A Desarrollo...">

                <button type="submit" id="botonRegistro">Registrarse</button>
            </form>
        </div>
        <div class="lado-izquierdo">
            <img src="img/ITFAM.png" alt="Imagen de registro" class="imagen-inicio-sesion">
            <h2>¿Ya tienes cuenta?</h2>
            <p>Inicia sesión para acceder al mundo ITFAM</p>
            <button id="botonIngresar">Iniciar sesión</button>
        </div>
    </div>
    <script src="javascript/registro_chat.js"></script>
</body>
</html>