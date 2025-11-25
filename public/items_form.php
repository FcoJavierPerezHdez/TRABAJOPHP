<?php
// public/items_form.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

// Solo los usuarios logueados pueden crear productos
require_login();

// Iniciamos variables para que el formulario no falle al cargar por primera vez
$name = '';
$description = '';
$price = '';
$stock = '';
$errors = [];

// Cuando le das a guardar en el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los datos 
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Validaciones necesarias
    if (empty($name)) {
        $errors[] = "El nombre es obligatorio.";
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = "El precio debe ser un número positivo.";
    }
    if (!is_numeric($stock) || $stock < 0) {
        $errors[] = "El stock debe ser un número entero positivo.";
    }

    // Si NO hay errores, guardamos en la base de datos
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock) VALUES (:name, :desc, :price, :stock)");
            $stmt->execute([
                ':name' => $name,
                ':desc' => $description,
                ':price' => $price,
                ':stock' => $stock
            ]);

            // Redirigimos al listado para evitar reenvíos al refrescar
            header('Location: items_list.php');
            exit;

        } catch (PDOException $e) {
            $errors[] = "Error al guardar en BD: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto - Decartón</title>
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
        <h1>Nuevo Producto</h1>

        <!-- Mostrar errores si los hay -->
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- El formulario envía los datos a esta misma página (action="") -->
        <form method="POST" action="">
            <label>Nombre del Producto:</label>
            <!-- value="..." sirve para repintar lo que el usuario escribió si hubo error (Sticky Form) -->
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label>Descripción:</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

            <label>Precio (€):</label>
            <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>" required>

            <label>Stock (Unidades):</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($stock); ?>" required>

            <button type="submit">Guardar Producto</button>
            <a href="items_list.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>

</body>
</html>