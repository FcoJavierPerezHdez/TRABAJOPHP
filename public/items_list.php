<?php
// public/items_list.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

// Verificamos que el usuario esté logueado. 
// Si no lo está, lo mandamos al login.
require_login();

try {
    // Creacion de consulta SQL simple para traer todos los productos
    // Más adelante aquí añadiremos el "LIMIT" para la paginación y el "WHERE" para el buscador
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(); // Recuperamos todas las filas en un array

} catch (PDOException $e) {
    die("Error al cargar productos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - Decartón</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { color: #0077b6; }
        
        /* Estilos básicos para la tabla */
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #0077b6; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .actions a { text-decoration: none; margin-right: 10px; padding: 5px 10px; border-radius: 4px; color: white; font-size: 0.9em; }
        .btn-edit { background-color: #e67e22; }
        .btn-delete { background-color: #e74c3c; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
        .btn-back { background: #555; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        .btn-new { background: #27ae60; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: bold;}
    </style>
</head>
<body>

    <div class="top-bar">
        <h1>Catálogo de Productos</h1>
        <div>
            <a href="index.php" class="btn-back">Volver al Inicio</a>
            <!--Ponemos el enlace real -->
            <a href="items_form.php" class="btn-new">+ Nuevo Producto</a>
        </div>
    </div>

    <?php if (count($products) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí empieza el bucle: Repetimos esto por cada producto -->
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo number_format($product['price'], 2); ?> €</td>
                    <td><?php echo $product['stock']; ?> uds.</td>
                    <td class="actions">
                        <!-- Enlaces vacíos por ahora, los rellenaremos en próximos pasos -->
                        <a href="#" class="btn-edit">Editar</a>
                        <a href="#" class="btn-delete">Borrar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay productos registrados todavía.</p>
    <?php endif; ?>

</body>
</html>