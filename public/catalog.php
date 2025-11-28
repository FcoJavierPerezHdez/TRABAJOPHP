<?php
// public/catalog.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

require_login();

// --- LÓGICA DE DATOS ---
$registros_por_pagina = 6; 
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $sql_count = "SELECT COUNT(*) FROM products WHERE name LIKE :search";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute([':search' => "%$busqueda%"]);
    $total_registros = $stmt_count->fetchColumn();

    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $offset = ($pagina_actual - 1) * $registros_por_pagina;

    $sql_data = "SELECT * FROM products WHERE name LIKE :search LIMIT " . (int)$registros_por_pagina . " OFFSET " . (int)$offset;
    $stmt = $pdo->prepare($sql_data);
    $stmt->execute([':search' => "%$busqueda%"]);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Calculamos cuántos items hay en el carrito para mostrar el numero
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda - Decartón</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-back { background: #555; color: white; text-decoration: none; padding: 10px 15px; border-radius: 4px; }
        
        .search-form { display: flex; gap: 5px; }
        .search-form input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .search-form button { background: #0077b6; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }

        /* GRID */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        .card-img-container { width: 100%; height: 200px; background-color: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .card-img { width: 100%; height: 100%; object-fit: cover; }
        .no-img { color: #aaa; font-size: 0.9em; }

        .card-body { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-title { margin: 0 0 10px; font-size: 1.1em; color: #333; }
        .card-desc { font-size: 0.9em; color: #666; flex-grow: 1; margin-bottom: 15px; }
        .card-price { font-size: 1.2em; font-weight: bold; color: #0077b6; margin-bottom: 10px; }
        
        .card-footer { margin-top: auto; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; color: #777; font-size: 0.8em; }
        
        /* Botón comprar ahora es un botón real */
        .btn-buy { background-color: #e67e22; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .btn-buy:hover { background-color: #d35400; }

        .pagination { margin-top: 40px; text-align: center; }
        .pagination a { display: inline-block; padding: 8px 12px; margin: 2px; background: white; border: 1px solid #ddd; text-decoration: none; color: #333; border-radius: 4px; }
        .pagination a.active { background: #0077b6; color: white; border-color: #0077b6; }
        
        /* Badge del carrito */
        .cart-badge { background: #27ae60; color: white; padding: 5px 10px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 0.9em;}
    </style>
</head>
<body>

    <div class="top-bar">
        <div>
            <a href="index.php" class="btn-back">⬅ Volver al Inicio</a>
            <h1 style="display:inline; margin-left: 15px; color: #0077b6;">Escaparate</h1>
        </div>
        
        <div style="display: flex; gap: 15px; align-items: center;">
            <form class="search-form" method="GET">
                <input type="text" name="q" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit">Buscar</button>
            </form>
            
            <!-- Botón flotante para ver el carrito -->
            <a href="my_orders.php" class="cart-badge">
            Cesta: <?php echo $cart_count; ?>
            </a>
        </div>
    </div>

    <?php if (count($products) > 0): ?>
        
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <div class="card-img-container">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?php echo htmlspecialchars($p['image']); ?>" class="card-img" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        <?php else: ?>
                            <span class="no-img">Sin imagen</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <p class="card-desc"><?php echo htmlspecialchars(substr($p['description'], 0, 80)) . '...'; ?></p>
                        <div class="card-price"><?php echo number_format($p['price'], 2); ?> €</div>
                        
                        <div class="card-footer">
                            <span>Stock: <?php echo $p['stock']; ?></span>
                            
                            <!-- FORMULARIO PARA AÑADIR AL CARRITO -->
                            <form method="POST" action="cart_add.php">
                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn-buy">Añadir 🛒</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?page=<?php echo $i; ?>&q=<?php echo urlencode($busqueda); ?>" class="<?php echo $i === $pagina_actual ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>

    <?php else: ?>
        <p style="text-align: center; font-size: 1.2em; color: #666; margin-top: 50px;">No encontramos productos.</p>
    <?php endif; ?>

</body>
</html>