<?php
// public/my_orders.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

require_login();

// Manejo de el vaciado del carrito
if (isset($_POST['empty_cart'])) {
    unset($_SESSION['cart']);
    header('Location: my_orders.php');
    exit;
}

$cart_items = [];
$total_price = 0;

// Si hay cosas en el carrito, recuperamos sus datos de la BD
if (!empty($_SESSION['cart'])) {
    // Obtenemos los IDs de los productos guardados en sesión
    $ids = array_keys($_SESSION['cart']);
    
    // Creamos una cadena de interrogantes (?,?,?) según la cantidad de IDs
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    // Cruzamos los datos de la BD con la cantidad que tenemos en sesión
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        
        $cart_items[] = [
            'name' => $p['name'],
            'price' => $p['price'],
            'image' => $p['image'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
        $total_price += $subtotal;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos - Decartón</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #0077b6; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background-color: #f8f9fa; color: #555; }
        .total-row td { font-weight: bold; font-size: 1.2em; color: #0077b6; border-top: 2px solid #0077b6; }
        
        .btn-empty { background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; float: right; }
        .btn-back { display: inline-block; margin-top: 20px; color: #555; text-decoration: none; }
        .thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; vertical-align: middle; margin-right: 10px; }
    </style>
</head>
<body>

<div class="container">
    <form method="POST" onsubmit="return confirm('¿Vaciar carrito?');">
        <button type="submit" name="empty_cart" class="btn-empty">🗑 Vaciar Cesta</button>
    </form>

    <h1> Mi Cesta de la Compra</h1>

    <?php if (empty($cart_items)): ?>
        <p style="text-align: center; margin: 50px 0; color: #777;">Tu cesta está vacía.</p>
        <p style="text-align: center;"><a href="catalog.php" style="color: #0077b6; font-weight: bold;">Ir a comprar algo</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cant.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td>
                        <?php if($item['image']): ?>
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="thumb">
                        <?php endif; ?>
                        <?php echo htmlspecialchars($item['name']); ?>
                    </td>
                    <td><?php echo number_format($item['price'], 2); ?> €</td>
                    <td>x <?php echo $item['qty']; ?></td>
                    <td><?php echo number_format($item['subtotal'], 2); ?> €</td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL A PAGAR:</td>
                    <td><?php echo number_format($total_price, 2); ?> €</td>
                </tr>
            </tbody>
        </table>
        
        <div style="text-align: right; margin-top: 30px;">
            <button onclick="alert('Funcionalidad de pago no implementada en este TFG')" style="background: #27ae60; color: white; border: none; padding: 12px 25px; font-size: 1.1em; border-radius: 5px; cursor: pointer;">
                Pagar Ahora
            </button>
        </div>
    <?php endif; ?>

    <a href="catalog.php" class="btn-back">⬅ Seguir comprando</a>
</div>

</body>
</html>