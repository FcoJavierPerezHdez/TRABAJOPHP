<?php
// public/preferencias.php
require_once __DIR__ . '/../app/auth.php';
// No es obligatorio estar logueado para cambiar el tema, así que no ponemos require_login()

$mensaje = "";

// PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema_elegido = $_POST['tema'] ?? 'light';
    
    // Guardamos la cookie por 30 días (86400 segundos * 30)
    // setcookie(nombre, valor, expiración, ruta)
    // La ruta "/" significa que la cookie vale para toda la web
    setcookie('tema_decarton', $tema_elegido, time() + (86400 * 30), "/");
    
    $mensaje = "¡Preferencia guardada! El tema se actualizará al navegar.";
    
    // Actualizamos la variable $_COOKIE manualmente para ver el cambio ya mismo sin recargar
    $_COOKIE['tema_decarton'] = $tema_elegido;
}

// Leemos la cookie actual (si no existe, por defecto es 'light')
$tema_actual = $_COOKIE['tema_decarton'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preferencias - Decartón</title>
    <style>
        /* Estilos dinámicos según el tema elegido */
        body { 
            font-family: sans-serif; padding: 20px; margin: 0;
            background-color: <?php echo $tema_actual === 'dark' ? '#333' : '#f4f4f4'; ?>;
            color: <?php echo $tema_actual === 'dark' ? '#fff' : '#333'; ?>;
        }
        .card { 
            background: <?php echo $tema_actual === 'dark' ? '#444' : 'white'; ?>; 
            padding: 30px; 
            max-width: 400px; 
            margin: 50px auto; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        button { padding: 10px 20px; background: #0077b6; color: white; border: none; cursor: pointer; border-radius: 4px; }
        button:hover { background: #005f87; }
        a { color: #0077b6; text-decoration: none; }
    </style>
</head>
<body>
    
    <div class="card">
        <h1>🛠 Preferencias</h1>
        <p>Personaliza tu experiencia en Decartón.</p>
        
        <?php if ($mensaje): ?>
            <p style="color: #27ae60; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form method="POST">
            <h3>Elige un tema:</h3>
            <label style="cursor: pointer;">
                <input type="radio" name="tema" value="light" <?php echo $tema_actual === 'light' ? 'checked' : ''; ?>> 
                Tema Claro (Original)
            </label>
            <br><br>
            <label style="cursor: pointer;">
                <input type="radio" name="tema" value="dark" <?php echo $tema_actual === 'dark' ? 'checked' : ''; ?>> 
                Tema Oscuro
            </label>
            <br><br>
            <button type="submit">Guardar Cambios</button>
        </form>
        
        <br>
        <hr style="border: 0; border-top: 1px solid #ccc;">
        <br>
        <a href="index.php">⬅ Volver al Inicio</a>
    </div>

</body>
</html>