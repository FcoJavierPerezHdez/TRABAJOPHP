<?php
// public/index.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

// Leemos la preferencia del usuario que guardó preferencias.php. Si no existe, usamos 'light'.
$tema = $_COOKIE['tema_decarton'] ?? 'light';

// Definimos variables de color PHP según el tema elegido de los disponibles
if ($tema === 'dark') {
    // Colores para Tema Oscuro
    $bg_body = '#333333';       // Fondo de la página (Gris oscuro)
    $bg_main = '#444444';       // Fondo del cuadro principal (Gris más claro)
    $text_color = '#ffffff';    // Texto blanco
    $shadow = 'rgba(255,255,255,0.1)'; // Sombra clarita
} else {
    // Colores para Tema Claro (Original)
    $bg_body = '#f4f4f4';
    $bg_main = '#ffffff';
    $text_color = '#000000';
    $shadow = 'rgba(0,0,0,0.1)';
}

// Consulta de prueba de conexión (igual que antes)
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
    <style>
        /* Aquí "imprimimos" las variables de PHP dentro del CSS */
        body { 
            font-family: sans-serif; margin: 0; padding: 20px; 
            background-color: <?php echo $bg_body; ?>; 
            color: <?php echo $text_color; ?>;
            transition: background 0.3s, color 0.3s;
        }
        
        header { background: #0077b6; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        
        main { 
            max-width: 800px; margin: 0 auto; padding: 20px; 
            background: <?php echo $bg_main; ?>; 
            border-radius: 0 0 5px 5px; 
            box-shadow: 0 0 10px <?php echo $shadow; ?>; 
        }
        
        /* Ajustamos el color del status para que se lea bien en oscuro */
        .status { padding: 10px; background: #e0f7fa; border-left: 5px solid #00bcd4; margin-bottom: 20px; color: #333; }
        
        .nav { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc; }
        .nav a { text-decoration: none; color: #0077b6; margin-right: 15px; font-weight: bold; }
        
        .header-user { font-size: 0.9rem; margin-top: 5px; }
        .header-user a { color: white; text-decoration: underline; }
    </style>
</head>
<body>

    <header>
        <h1>Decartón</h1>
        <p>Lo mejor para el deporte (al mejor precio)</p>
        
        <div class="header-user">
            <?php if (is_logged_in()): ?>
                Hola, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                | <a href="logout.php">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h2>Bienvenido a nuestra tienda</h2>
        
        <div class="status">
            <strong>Estado del sistema:</strong> <?php echo $dbStatus; ?>
        </div>

        <p>Actualmente estamos construyendo el sitio. Próximamente podrás iniciar sesión y ver nuestros productos.</p>
        
        <?php if (is_logged_in()): ?>
            <p style="color: #27ae60; font-weight: bold;">¡Has iniciado sesión correctamente! (Rol: <?php echo $_SESSION['role']; ?>)</p>
        <?php endif; ?>

        <nav class="nav">
            <?php if (is_logged_in()): ?>
                <a href="#">Mis Pedidos</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="items_list.php">Gestionar Productos</a>
                <?php endif; ?>
                <a href="items_list.php">Ver Catálogo Completo</a>
            <?php else: ?>
                <a href="login.php">Iniciar Sesión</a>
            <?php endif; ?>
            
            <!-- --- ENLACE A PREFERENCIAS (NUEVO) --- -->
            <a href="preferencias.php">Cambiar Tema</a>
        </nav>
    </main>

    <footer>
        <p style="text-align: center; margin-top: 50px; color: #888;">&copy; <?php echo date('Y'); ?> Decartón S.L.</p>
    </footer>

</body>
</html>