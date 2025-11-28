<?php
session_start();
include 'includes/bd.php';

if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $curso = $_POST['curso'] ?? '';
    $foto_perfil = '';

    if (!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($contraseña) && !empty($rol)) {
        if (strlen($contraseña) < 8) {
            $error = "La contraseña debe tener al menos 8 caracteres.";
        } else {
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['foto_perfil']['tmp_name'];
                $file_name = $_FILES['foto_perfil']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png'];

                if (in_array($file_ext, $allowed_ext)) {
                    $upload_dir = 'assets/img/perfiles/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $new_file_name = uniqid() . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        $foto_perfil = $new_file_name;
                    } else {
                        $error = "Error al subir la foto de perfil. Verifica los permisos del directorio.";
                    }
                } else {
                    $error = "Solo se permiten archivos JPG, JPEG o PNG.";
                }
            }

            if (!isset($error)) {
                $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
                $stmt->bind_param("s", $correo);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    $error = "El correo ya está registrado.";
                } else {
                    $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);
                    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, correo, telefono, contraseña, rol, curso, foto_perfil) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $nombre, $apellido, $correo, $telefono, $contraseña_encriptada, $rol, $curso, $foto_perfil);

                    if ($stmt->execute()) {
                        header('Location: login.php?success=registro_exitoso');
                        exit;
                    } else {
                        $error = "Error al registrar: " . $stmt->error;
                    }
                }
                $stmt->close();
            }
        }
    } else {
        $error = "Por favor, completa todos los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="css/registro_chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="contenedor">
        
        <div class="lado-derecho">
            <img src="img/ITFAM.png" alt="Imagen de registro" class="imagen-inicio-sesion">
            <h2>¿Ya tienes cuenta?</h2>
            <p>Inicia sesión para acceder al mundo ITFAM</p>
            <button id="botonIngresar">Iniciar sesión</button>
        </div>
    </div>
    <div class="lado-izquierdo">
            <h2>Registrarse</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="register.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nombre" placeholder="Nombre" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="apellido" placeholder="Apellido" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="correo" placeholder="Correo electrónico" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="telefono" placeholder="Teléfono">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="contraseña" placeholder="Contraseña" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-users"></i>
                    <select name="rol" id="rol" required>
                        <option value="" disabled selected>Rol</option>
                        <option value="Estudiante">Estudiante</option>
                        <option value="Profesor">Profesor</option>
                        <option value="Orientador">Orientador</option>
                    </select>
                </div>
                <div class="input-group" id="curso-container" style="display: none;">
                    <i class="fas fa-book"></i>
                    <select name="curso" id="curso">
                        <option value="" disabled selected>Curso</option>
                        <option value="4 Instalaciones Eléctricas">4 Instalaciones Eléctricas</option>
                        <option value="4 Mecatrónica">4 Mecatrónica</option>
                        <option value="4 Cuidados de Enfermería y Promoción de la Salud">4 Cuidados de Enfermería y Promoción de la Salud</option>
                        <option value="4 Electromecánica de Vehículos">4 Electromecánica de Vehículos</option>
                        <option value="4 Equipos Electrónicos">4 Equipos Electrónicos</option>
                        <option value="4A Desarrollo y Administración de Aplicaciones Informáticas">4A Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="4A Gestión Administrativa y Tributaria">4A Gestión Administrativa y Tributaria</option>
                        <option value="4B Desarrollo y Administración de Aplicaciones Informáticas">4B Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="4B Gestión Administrativa y Tributaria">4B Gestión Administrativa y Tributaria</option>
                        <option value="5 Electromecánica de Vehículos">5 Electromecánica de Vehículos</option>
                        <option value="5 Equipos Electrónicos">5 Equipos Electrónicos</option>
                        <option value="5 Instalaciones Eléctricas">5 Instalaciones Eléctricas</option>
                        <option value="5 Mecatrónica">5 Mecatrónica</option>
                        <option value="5A Cuidados de Enfermería y Promoción de la Salud">5A Cuidados de Enfermería y Promoción de la Salud</option>
                        <option value="5A Desarrollo y Administración de Aplicaciones Informáticas">5A Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="5A Gestión Administrativa y Tributaria">5A Gestión Administrativa y Tributaria</option>
                        <option value="5B Desarrollo y Administración de Aplicaciones Informáticas">5B Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="5B Gestión Administrativa y Tributaria">5B Gestión Administrativa y Tributaria</option>
                        <option value="5C Desarrollo y Administración de Aplicaciones Informáticas">5C Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="5C Gestión Administrativa y Tributaria">5C Gestión Administrativa y Tributaria</option>
                        <option value="6 Electromecánica de Vehículos">6 Electromecánica de Vehículos</option>
                        <option value="6 Equipos Electrónicos">6 Equipos Electrónicos</option>
                        <option value="6 Instalaciones Eléctricas">6 Instalaciones Eléctricas</option>
                        <option value="6 Mecatrónica">6 Mecatrónica</option>
                        <option value="6A Cuidados de Enfermería y Promoción de la Salud">6A Cuidados de Enfermería y Promoción de la Salud</option>
                        <option value="6B Cuidados de Enfermería y Promoción de la Salud">6B Cuidados de Enfermería y Promoción de la Salud</option>
                        <option value="6A Desarrollo y Administración de Aplicaciones Informáticas">6A Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="6A Gestión Administrativa y Tributaria">6A Gestión Administrativa y Tributaria</option>
                        <option value="6B Desarrollo y Administración de Aplicaciones Informáticas">6B Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="6B Gestión Administrativa y Tributaria">6B Gestión Administrativa y Tributaria</option>
                        <option value="6C Desarrollo y Administración de Aplicaciones Informáticas">6C Desarrollo y Administración de Aplicaciones Informáticas</option>
                        <option value="6C Gestión Administrativa y Tributaria">6C Gestión Administrativa y Tributaria</option>
                    </select>
                </div>
                <div class="input-group">
                    <i class="fas fa-camera"></i>
                    <input type="file" name="foto_perfil" id="foto_perfil" accept="image/jpeg,image/png">
                </div>
                <img id="foto-preview" src="#" alt="Previsualización de la foto">
                <button type="submit" name="guardar" id="botonRegistro">Registrarse</button>
            </form>
        </div>
    <script src="js/registro_chat.js"></script>
</body>
</html>