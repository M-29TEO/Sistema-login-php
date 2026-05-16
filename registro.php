<?php
// registro.php, aqui tenemos el codigo de como funcionara la pagina de registro
session_start(); // Iniciamos la sesión por si queremos mostrar mensajes
include('db.php'); // Incluimos nuestra conexión a la BD

$mensaje = ''; // Variable para almacenar mensajes de éxito o error

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //  Recoger los datos del formulario
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];

    // Validación que no haya campos vacíos
    if (empty($cedula) || empty($nombre) || empty($correo) || empty($password)) {
        $mensaje = 'Por favor, completa todos los campos.';
    } 
    // Validar que el correo tenga un formato válido
    elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El formato del correo electrónico no es válido.';
    }
    // Validar que la contraseña y su confirmación coincidan
    elseif ($password !== $confirmar_password) {
        $mensaje = 'Las contraseñas no coinciden.';
    }
    else {
        // Verificar si el correo ya existe en la BD 
        $sql = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $correo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $mensaje = 'El correo electrónico ya está registrado.';
        } else {
            // Hashear la contraseña y guardar el usuario
            // password_hash crea un hash seguro de la contraseña
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $fecha_registro = date('Y-m-d H:i:s'); // Fecha y hora actual

            // Consulta SQL para insertar el nuevo usuario
            $sql_insert = "INSERT INTO usuarios (cedula, nombre, correo, password, fecha_registro) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conexion, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "sssss", $cedula, $nombre, $correo, $password_hashed, $fecha_registro);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                $mensaje = '¡Registro exitoso! Ya puedes iniciar sesión.';
                // Limpiar el formulario o redirigir a la pagina de inicio después de unos segundos
                // Aquí lo dejamos para que el usuario vea el mensaje y luego haga clic en un enlace.
            } else {
                $mensaje = 'Hubo un error al registrar el usuario. Por favor, intenta de nuevo.';
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); width: 400px; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; margin-top: 20px; padding: 10px; background: #1877f2; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #166fe5; }
        .mensaje { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .link { text-align: center; margin-top: 15px; }
        a { color: #1877f2; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Crear una cuenta</h2>

    <?php if(!empty($mensaje)): ?>
        <div class="mensaje <?php echo (strpos($mensaje, 'exitoso') !== false || strpos($mensaje, 'éxito') !== false) ? 'exito' : 'error'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="cedula">Cédula:</label>
        <input type="text" id="cedula" name="cedula" required>

        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirmar_password">Confirmar contraseña:</label>
        <input type="password" id="confirmar_password" name="confirmar_password" required>

        <button type="submit">Registrarse</button>
    </form>
    <div class="link">
        ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
    </div>
</div>
</body>
</html>