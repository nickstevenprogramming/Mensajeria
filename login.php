<?php
session_start();
include 'includes/bd.php';

if (isset($_POST['guardar'])) {
    $correo = $_POST['correo'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    if (!empty($correo) && !empty($contraseña)) {
        $stmt = $conexion->prepare("SELECT id, nombre, contraseña, rol FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];
                header('Location: index.php');
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "El correo no está registrado.";
        }
        $stmt->close();
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/inicio_chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedor">
        <div class="lado-derecho">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="contraseña" placeholder="Contraseña" required>
                </div>
                <button type="submit" name="guardar" id="botonIngresar">Iniciar Sesión</button>
            </form>
        </div>
        <div class="lado-izquierdo">
            <img src="img/ITFAM.png" alt="Imagen de inicio" class="imagen-inicio-sesion">
            <h2>NO TIENES CUENTA?</h2>
            <p>Regístrate para unirte al mundo ITFAM</p>
            <button id="botonRegistro">Registrarse</button>
        </div>
    </div>
    <script src="js/inicio_chat.js"></script>
</body>
</html>