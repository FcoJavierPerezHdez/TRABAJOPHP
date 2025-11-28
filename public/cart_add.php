<?php
// public/cart_add.php
require_once __DIR__ . '/../app/auth.php'; // Iniciamos sesión

// Si no existe el carrito, lo creamos como un array vacío
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    
    // Lógica del carrito:
    // El carrito será un array donde: Clave = ID Producto, Valor = Cantidad
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++; // Si ya está, sumamos 1
    } else {
        $_SESSION['cart'][$id] = 1; // Si es nuevo, ponemos 1
    }
}

// Volvemos al catálogo automáticamente
header('Location: catalog.php');
exit;
?>