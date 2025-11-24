<?php
// public/login.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

// Si ya estoy logueado,nos vamos al inicio
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = null;

// para procesar el formulario: (Básicamente funciona cuando le damos al Enter)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 1. Buscamos al usuario en la BD
    // Usamos sentencias preparadas (en este caso "prepare") para evitar inyecciones SQL
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    // 2. Verificamos la contraseña
    // password_verify lo que hace esq comprueba si la contraseña escrita coincide con el HASH de la BD
    if ($user && password_verify($password, $user['password'])) {
        // ¡Contraseña correcta!
        login_user($user); // Una vez que hizo login, ya esta logeado (función de auth.php)
        header('Location: index.php'); // Lo mandamos a la portada
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Decartón</title>
    <style>
        /* Estilos básicos para centrar el login */
        body { font-family: sans-serif; background: #eee; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #0077b6; color: white; border: none; cursor: pointer; }
        button:hover { background: #005f87; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; text-align: center; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 style="text-align: center">Iniciar Sesión</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Usuario:</label>
            <input type="text" name="username" required placeholder="Ej: admin">
            
            <label>Contraseña:</label>
            <input type="password" name="password" required placeholder="Ej: 1234">
            
            <button type="submit">Entrar</button>
        </form>
        
        <p style="font-size: 0.8em; text-align: center; color: #666;">
            (Para probar usa: <strong>admin</strong> / <strong>1234</strong>)
        </p>
    </div>

</body>
</html>