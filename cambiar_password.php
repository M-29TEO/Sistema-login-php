<?php
// cambiar contraseña
session_start();
include('db.php');

// Verificar que el usuario haya iniciado sesión
if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensaje = '';
$error = '';

// Procesar el formulario cuando se envía
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $confirmar_password = $_POST['confirmar_password'];
    $usuario_id = $_SESSION['usuario_id'];

    // Validaciones
    if(empty($password_actual) || empty($password_nueva) || empty($confirmar_password)) {
        $error = 'Por favor, completa todos los campos.';
    } 
    elseif ($password_nueva !== $confirmar_password) {
        $error = 'La nueva contraseña y su confirmación no coinciden.';
    } 
    elseif (strlen($password_nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } 
    else {
        // Obtener la contraseña actual del usuario desde la BD
        $sql = "SELECT password FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $usuario = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);

        // Verificar que la contraseña actual sea correcta
        if($usuario && password_verify($password_actual, $usuario['password'])) {
            // 3. Contraseña actual correcta: hashear la nueva contraseña
            $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            
            // Actualizar la contraseña en la base de datos
            $sql_update = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conexion, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "si", $nuevo_hash, $usuario_id);
            
            if(mysqli_stmt_execute($stmt_update)) {
                $mensaje = '¡Contraseña actualizada con éxito!';
            } else {
                $error = 'Error al actualizar la contraseña. Intenta de nuevo.';
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $error = 'La contraseña actual es incorrecta.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f0f2f5; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            max-width: 500px; 
            margin: 50px auto; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            padding: 30px; 
        }
        h2 { 
            color: #333; 
            border-bottom: 2px solid #1877f2; 
            padding-bottom: 10px; 
            margin-bottom: 25px;
        }
        label { 
            display: block; 
            margin-top: 15px; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: #555;
        }
        input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box; 
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #1877f2;
            box-shadow: 0 0 5px rgba(24,119,242,0.3);
        }
        button { 
            background: #1877f2; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 4px; 
            cursor: pointer; 
            width: 100%; 
            margin-top: 25px;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover { 
            background: #166fe5; 
        }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 12px; 
            border-radius: 4px; 
            margin: 15px 0; 
        }
        .exito { 
            color: #155724; 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            padding: 12px; 
            border-radius: 4px; 
            margin: 15px 0; 
        }
        .volver { 
            display: inline-block; 
            margin-top: 20px; 
            text-decoration: none; 
            color: #1877f2; 
            text-align: center;
            width: 100%;
        }
        .volver:hover {
            text-decoration: underline;
        }
        .info-usuario {
            background: #e7f3ff;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            color: #1877f2;
        }
        .requisitos {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2> Cambiar Contraseña</h2>
    
    <div class="info-usuario">
        👤 Usuario: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
    </div>

    <?php if(!empty($mensaje)): ?>
        <div class="exito">✅ <?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>
    
    <?php if(!empty($error)): ?>
        <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="password_actual">Contraseña Actual:</label>
        <input type="password" id="password_actual" name="password_actual" required placeholder="Ingresa tu contraseña actual">

        <label for="password_nueva"> Nueva Contraseña:</label>
        <input type="password" id="password_nueva" name="password_nueva" required placeholder="Mínimo 6 caracteres">
        <div class="requisitos">* La contraseña debe tener al menos 6 caracteres</div>

        <label for="confirmar_password"> Confirmar Nueva Contraseña:</label>
        <input type="password" id="confirmar_password" name="confirmar_password" required placeholder="Repite la nueva contraseña">

        <button type="submit">Actualizar Contraseña</button>
    </form>

    <a href="perfil.php" class="volver">← Volver a Mi Perfil</a>
</div>
</body>
</html>