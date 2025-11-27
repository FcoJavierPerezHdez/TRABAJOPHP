<?php
// public/items_list.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

require_login();

// --- CONFIGURACIÓN DE PAGINACIÓN ---
$registros_por_pagina = 10; // Cambiamos esto si queremos ver más o menos productos
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

// --- CONFIGURACIÓN DE BÚSQUEDA ---
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    // CONTAR TOTAL DE RESULTADOS
    // Necesitamos saber cuántos hay en total para calcular las páginas
    $sql_count = "SELECT COUNT(*) FROM products WHERE name LIKE :search";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute([':search' => "%$busqueda%"]);
    $total_registros = $stmt_count->fetchColumn();

    // Cálculos matemáticos necesarios 
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $offset = ($pagina_actual - 1) * $registros_por_pagina;

    // TRAEMOS LOS PRODUCTOS DE ESTA PÁGINA
    // Usamos LIMIT y OFFSET. Los metemos como enteros para evitar problemas con PDO.
    $sql_data = "SELECT * FROM products WHERE name LIKE :search LIMIT " . (int)$registros_por_pagina . " OFFSET " . (int)$offset;
    $stmt = $pdo->prepare($sql_data);
    $stmt->execute([':search' => "%$busqueda%"]);
    $products = $stmt->fetchAll();

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
        
        /* Estilos Tabla */
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #0077b6; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        /* Botones de acción */
        .actions a { text-decoration: none; margin-right: 5px; padding: 5px 10px; border-radius: 4px; color: white; font-size: 0.85em; }
        .btn-edit { background-color: #e67e22; }
        .btn-delete { background-color: #e74c3c; }
        
        /* Barra Superior */
        .top-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .btn-back { background: #555; color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        .btn-new { background: #27ae60; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; font-weight: bold;}
        
        /* Buscador */
        .search-box { display: flex; gap: 5px; }
        .search-box input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .search-box button { padding: 8px 15px; background: #0077b6; color: white; border: none; border-radius: 4px; cursor: pointer; }
        
        /* Paginación */
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a, .pagination span { display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; background: white; text-decoration: none; color: #333; border-radius: 4px; }
        .pagination a.active { background: #0077b6; color: white; border-color: #0077b6; }
        .pagination a:hover:not(.active) { background: #ddd; }
    </style>
</head>
<body>

    <div class="top-bar">
        <div>
            <a href="index.php" class="btn-back">⬅ Inicio</a>
            <h1 style="display:inline; margin-left:15px; vertical-align:middle;">Catálogo</h1>
        </div>
        
        <!-- FORMULARIO BUSCADOR -->
        <form class="search-box" method="GET" action="items_list.php">
            <input type="text" name="q" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">🔍 Buscar</button>
            <?php if ($busqueda): ?>
                <a href="items_list.php" style="padding: 8px; color: #666; text-decoration: none;">(Limpiar)</a>
            <?php endif; ?>
        </form>

        <a href="items_form.php" class="btn-new">+ Nuevo Producto</a>
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
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo number_format($product['price'], 2); ?> €</td>
                    <td><?php echo $product['stock']; ?> uds.</td>
                    <td class="actions">
                        <a href="items_form.php?id=<?php echo $product['id']; ?>" class="btn-edit">Editar</a>
                        <a href="items_delete.php?id=<?php echo $product['id']; ?>" class="btn-delete">Borrar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- BARRA DE PAGINACIÓN -->
        <div class="pagination">
            <?php 
                // Mantenemos el término de búsqueda en los enlaces de paginación
                $link_extra = $busqueda ? "&q=" . urlencode($busqueda) : ""; 
            ?>
            
            <!-- Botón Anterior -->
            <?php if ($pagina_actual > 1): ?>
                <a href="?page=<?php echo $pagina_actual - 1; ?><?php echo $link_extra; ?>">« Anterior</a>
            <?php endif; ?>

            <!-- Números de página -->
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $link_extra; ?>" 
                   class="<?php echo $i === $pagina_actual ? 'active' : ''; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Botón Siguiente -->
            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?page=<?php echo $pagina_actual + 1; ?><?php echo $link_extra; ?>">Siguiente »</a>
            <?php endif; ?>
        </div>
        
        <p style="text-align: center; color: #777; font-size: 0.9em;">
            Mostrando página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?> (Total: <?php echo $total_registros; ?> productos)
        </p>

    <?php else: ?>
        <p style="text-align: center; margin-top: 40px; font-size: 1.2em;">
            No se encontraron productos con esa búsqueda. <br>
            <a href="items_list.php">Ver todos</a>
        </p>
    <?php endif; ?>

</body>
</html>