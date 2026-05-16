<?php
session_start();
include('db.php');

// Si ya está logueado, va al perfil
if(isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if(empty($correo) || empty($password)) {
        $error = 'Por favor, introduce tu correo y contraseña.';
    } else {
        // Buscar usuario por correo
        $sql = "SELECT id, nombre, correo, password FROM usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($conexion, $sql);  // ✅ mysqli_ (con i)
        mysqli_stmt_bind_param($stmt, "s", $correo);  // ✅ mysqli_stmt_
        mysqli_stmt_execute($stmt);  // ✅ mysqli_stmt_
        $resultado = mysqli_stmt_get_result($stmt);  // ✅ mysqli_stmt_
        $usuario = mysqli_fetch_assoc($resultado);  // ✅ mysqli_ (sin stmt)

        // Verificar contraseña
        if ($usuario && password_verify($password, $usuario['password'])) {
            session_regenerate_id(true);  // ✅ session_regenerate_id (no session_register_id)
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];
            
            header('Location: perfil.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1877f2; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #166fe5; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }
        .link { text-align: center; margin-top: 15px; }
        a { color: #1877f2; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Iniciar Sesión</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>
    
    <div class="link">
        ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
</div>
</body>
</html>