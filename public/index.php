<?php
// public/index.php
// Punto de partida inicial. Por ahora solo nos muestra la bienvenida y prueba la bbdd.

require_once __DIR__ . '/../app/pdo.php'; // Importamos la conexión de la bbdd
require_once __DIR__ . '/../app/auth.php'; // Importamos la autenticación

// Hacemos una consulta de prueba para ver si conecta bien
// Esto lo borraremos más adelante, es solo para hacer primer commit
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $count = $stmt->fetchColumn();
    $dbStatus = "Conexión exitosa. Hay $count productos en la base de datos.";
} catch (Exception $e) {
    $dbStatus = "Error al conectar: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decartón - Tu tienda de deportes</title>
    <!-- Un estilo muy básico para que no se vea tan feo, sin usar CSS externo aún -->
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        header { background: #0077b6; color: white; padding: 10px; text-align: center; }
        main { max-width: 800px; margin: 20px auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; background: #e0f7fa; border-left: 5px solid #00bcd4; margin-bottom: 20px; }
        .nav { margin-top: 20px; }
        .nav a { text-decoration: none; color: #0077b6; margin-right: 15px; font-weight: bold; }
        /* Agrego un pequeño estilo para el mensaje de bienvenida en la cabecera */
        .header-user { font-size: 0.8rem; margin-top: 5px; opacity: 0.9; }
        .header-user a { color: white; text-decoration: underline; }
    </style>
</head>
<body>

    <header>
        <h1>Decartón</h1>
        <p>Lo mejor para el deporte (al mejor precio)</p>
        
        <!-- AÑADIDO: Información del usuario si está logueado -->
        <div class="header-user">
            <?php if (is_logged_in()): ?>
                Hola, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                | <a href="logout.php">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h2>Bienvenido a nuestra tienda</h2>
        
        <!-- Mensaje de estado de la base de datos -->
        <div class="status">
            <strong>Estado del sistema:</strong> <?php echo $dbStatus; ?>
        </div>

        <p>Actualmente estamos construyendo el sitio. Próximamente podrás iniciar sesión y ver nuestros productos.</p>
        
        <!-- AÑADIDO: Mensaje condicional -->
        <?php if (is_logged_in()): ?>
            <p style="color: green; font-weight: bold;">¡Has iniciado sesión correctamente! (Rol: <?php echo $_SESSION['role']; ?>)</p>
        <?php endif; ?>

        <nav class="nav">
            <!-- MODIFICADO: Enlaces dinámicos según si estás logueado o no -->
            <?php if (is_logged_in()): ?>
                <!-- Si el usuario TIENE sesión iniciada -->
                <a href="#">Mis Pedidos</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="#">Gestionar Productos</a>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Si el usuario NO tiene sesión iniciada -->
                <a href="login.php">Iniciar Sesión</a>
            <?php endif; ?>

            <a href="#">Ver Catálogo</a>
        </nav>
    </main>

    <footer>
        <p style="text-align: center; margin-top: 50px; color: #777;">&copy; <?php echo date('Y'); ?> Decartón S.L.</p>
    </footer>

</body>
</html>