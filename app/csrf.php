<?php
// app/csrf.php
// Es un sistema de protección contra falsificación de solicitudes (CSRF)

// Asegurarnos de que la sesión está iniciada para guardar el token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generamos el token 
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        // Si no existe, creamos uno aleatorio y seguro
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificamos el token 
function check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Si recibimos un formulario POST, miramos si trae el token correcto
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // Si no coincide, detenemos todo.
            die("Error de Seguridad (CSRF): El formulario ha caducado o no es legítimo. Recarga la página e intenta de nuevo.");
        }
    }
}
?>