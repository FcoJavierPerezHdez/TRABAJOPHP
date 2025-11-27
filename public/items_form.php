<?php
// public/items_form.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

require_login();

// Variables iniciales (por defecto están vacías para "Crear nuevo producto")
$id = '';
$name = '';
$description = '';
$price = '';
$stock = '';
$errors = [];
$is_edit = false; // Bandera para saber si estamos editando

// --- LOGICA DE CARGA (Si venimos de darle clic a "Editar") ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Buscamos el producto en la bbdd
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if ($product) {
        // Si existe, rellenamos las variables con sus datos
        $is_edit = true;
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $stock = $product['stock'];
    } else {
        // Si ponen un ID inventado, redirigimos
        header('Location: items_list.php');
        exit;
    }
}

// --- LOGICA DE GUARDADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos datos
    $id = $_POST['id'] ?? ''; // Recogemos el ID oculto si existe
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Validaciones
    if (empty($name)) $errors[] = "El nombre es obligatorio.";
    if (!is_numeric($price) || $price < 0) $errors[] = "El precio debe ser positivo.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "El stock debe ser entero positivo.";

    if (empty($errors)) {
        try {
            if ($id) {
                // --- MODO EDICIÓN (UPDATE) ---
                $sql = "UPDATE products SET name=:name, description=:desc, price=:price, stock=:stock WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name' => $name,
                    ':desc' => $description,
                    ':price' => $price,
                    ':stock' => $stock,
                    ':id'   => $id
                ]);
            } else {
                // --- MODO CREACIÓN (INSERT) ---
                $sql = "INSERT INTO products (name, description, price, stock) VALUES (:name, :desc, :price, :stock)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name' => $name,
                    ':desc' => $description,
                    ':price' => $price,
                    ':stock' => $stock
                ]);
            }

            // Volvemos al listado
            header('Location: items_list.php');
            exit;

        } catch (PDOException $e) {
            $errors[] = "Error BD: " . $e->getMessage();
        }
    } else {
        // Si hubo errores en el POST, mantenemos $is_edit a true si había ID
        if ($id) $is_edit = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $is_edit ? 'Editar' : 'Nuevo'; ?> Producto</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f9f9f9; }
        .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #0077b6; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { margin-top: 20px; padding: 12px 20px; background: #27ae60; color: white; border: none; font-size: 1.1em; cursor: pointer; width: 100%; border-radius: 4px; }
        button:hover { background: #219150; }
        .btn-cancel { background: #7f8c8d; margin-top: 10px; display: block; text-align: center; text-decoration: none; padding: 10px; border-radius: 4px; color: white;}
        .error-box { background: #ffdddd; color: #d8000c; padding: 10px; margin-bottom: 20px; border-left: 5px solid #d8000c; }
    </style>
</head>
<body>

    <div class="form-container">
        <h1><?php echo $is_edit ? 'Editar Producto' : 'Nuevo Producto'; ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- El action se deja vacío para que se envíe a la misma URL (conservando el ?id=... si existe) -->
        <form method="POST" action="">
            <!-- TRUCO: Campo oculto con el ID. Así sabemos qué producto actualizar -->
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>

            <label>Nombre:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label>Descripción:</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

            <label>Precio (€):</label>
            <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($stock); ?>" required>

            <button type="submit">
                <?php echo $is_edit ? 'Actualizar Cambios' : 'Guardar Producto'; ?>
            </button>
            <a href="items_list.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>

</body>
</html>