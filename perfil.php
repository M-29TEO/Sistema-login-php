<?php

session_start();
include('db.php');

// Verificar si el usuario no ha iniciado sesión
if(!isset($_SESSION['usuario_id'])) {
    // Si no hay sesión, lo mandamos al login
    header('Location: login.php');
    exit;
}

$mensaje_perfil = '';
$error_perfil = '';

//actualizar nombre y correo
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_perfil'])) {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_correo = trim($_POST['correo']);
    $usuario_id = $_SESSION['usuario_id'];

    // Validaciones
    if(empty($nuevo_nombre) || empty($nuevo_correo)) {
        $error_perfil = 'El nombre y el correo no pueden estar vacíos.';
    } elseif (!filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
        $error_perfil = 'El formato del correo no es válido.';
    } else {
        // Verificar si el nuevo correo ya está en uso por otro usuario
        $sql_check = "SELECT id FROM usuarios WHERE correo = ? AND id != ?";
        $stmt_check = mysqli_prepare($conexion, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "si", $nuevo_correo, $usuario_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if(mysqli_stmt_num_rows($stmt_check) > 0) {
            $error_perfil = 'El correo electrónico ya está en uso por otra cuenta.';
        } else {
            // Actualizar los datos
            $sql_update = "UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conexion, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "ssi", $nuevo_nombre, $nuevo_correo, $usuario_id);
            
            if(mysqli_stmt_execute($stmt_update)) {
                // Actualizar también los datos en la sesión
                $_SESSION['usuario_nombre'] = $nuevo_nombre;
                $_SESSION['usuario_correo'] = $nuevo_correo;
                $mensaje_perfil = '¡Perfil actualizado con éxito!';
            } else {
                $error_perfil = 'Error al actualizar. Intenta de nuevo.';
            }
            mysqli_stmt_close($stmt_update);
        }
        mysqli_stmt_close($stmt_check);
    }
}

// Obtener los datos más recientes del usuario
$usuario_id = $_SESSION['usuario_id'];
$sql_datos = "SELECT cedula, nombre, correo FROM usuarios WHERE id = ?";
$stmt_datos = mysqli_prepare($conexion, $sql_datos);
mysqli_stmt_bind_param($stmt_datos, "i", $usuario_id);
mysqli_stmt_execute($stmt_datos);
$resultado_datos = mysqli_stmt_get_result($stmt_datos);
$usuario_actual = mysqli_fetch_assoc($resultado_datos);
mysqli_stmt_close($stmt_datos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; }
        h2 { border-bottom: 1px solid #eee; padding-bottom: 10px; color: #333; }
        .info p { background: #f8f9fa; padding: 10px; border-radius: 4px; }
        label { display: block; margin-top: 15px; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #1877f2; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        button:hover { background: #166fe5; }
        .error { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin: 15px 0; }
        .exito { color: #155724; background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin: 15px 0; }
        .logout { background: #dc3545; margin-top: 20px; display: inline-block; text-decoration: none; color: white; padding: 10px 15px; border-radius: 4px; }
        .logout:hover { background: #c82333; }
        .menu { margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        .menu a { margin-left: 15px; text-decoration: none; color: #1877f2; }
        .menu a:hover { text-decoration: underline; }
        .btn-cambiar-pass { background: #6dd886; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; }
        .btn-cambiar-pass:hover { background: #61af72; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="menu">
        <strong> Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
        <div>
            <a href="cambiar_password.php" class="btn-cambiar-pass">Cambiar Contraseña</a>
            <a href="logout.php"> Cerrar Sesión</a>
        </div>
    </div>
    
    <h2> Mi Perfil</h2>
    
    <div class="info">
        <p><strong>Cédula:</strong> <?php echo htmlspecialchars($usuario_actual['cedula']); ?></p>
    </div>

    <?php if(!empty($mensaje_perfil)): ?>
        <div class="exito">✅ <?php echo htmlspecialchars($mensaje_perfil); ?></div>
    <?php endif; ?>
    <?php if(!empty($error_perfil)): ?>
        <div class="error">❌ <?php echo htmlspecialchars($error_perfil); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nombre"> Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario_actual['nombre']); ?>" required>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario_actual['correo']); ?>" required>

        <button type="submit" name="actualizar_perfil"> Actualizar Perfil</button>
    </form>
</div>
</body>
</html>