<?php
// public/items_delete.php
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';

require_login();

// Validamos que tenga un ID
if (!isset($_GET['id'])) {
    header('Location: items_list.php');
    exit;
}

$id = $_GET['id'];
$error = null;

// Buscamos el producto para mostrar su nombre en la confirmación
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Producto no encontrado.");
}

// --- LOGICA DE BORRADO (Solo cuando se confirma mediante POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // INICIO DE LA TRANSACCIÓN 
        // A partir de aquí, nada es definitivo hasta que hagamos commit
        $pdo->beginTransaction();

        // Ejecutamos el borrado
        $stmtDelete = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmtDelete->execute([':id' => $id]);

        // Guardamos registro en Auditoría 
        $userId = $_SESSION['user_id']; // El ID del admin que está borrando
        $userName = $_SESSION['username'];
        $prodName = $product['name'];
        
        $action = "DELETE_PRODUCT";
        $description = "El usuario '$userName' (ID: $userId) ha eliminado el producto '$prodName' (ID original: $id).";

        $stmtAudit = $pdo->prepare("INSERT INTO audit_log (action_type, description, user_id) VALUES (:action, :desc, :user)");
        $stmtAudit->execute([
            ':action' => $action,
            ':desc' => $description,
            ':user' => $userId
        ]);

        // CONFIRMAMOS LA TRANSACCIÓN (COMMIT)
        // Si llegamos aquí, ambas consultas funcionaron. Guardamos cambios.
        $pdo->commit();

        // Volvemos al listado
        header('Location: items_list.php');
        exit;

    } catch (Exception $e) {
        // SI ALGO FALLA (ROLLBACK)
        // Si falla el borrado o la auditoría, la base de datos vuelve a como estaba antes
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Error crítico al borrar (se ha hecho rollback): " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Borrar Producto</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        h2 { color: #d32f2f; }
        .btn-confirm { background: #d32f2f; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .btn-cancel { background: #7f8c8d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-left: 10px; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeeba; border-radius: 4px; margin: 20px 0; }
    </style>
</head>
<body>

    <div class="card">
        <h2>¿Estás seguro?</h2>
        
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <p>Estás a punto de eliminar el producto:</p>
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        
        <div class="warning">
            Esta acción es irreversible y quedará registrada en la auditoría del sistema.
        </div>

        <form method="POST" action="">
            <button type="submit" class="btn-confirm">Sí, Eliminar Definitivamente</button>
            <a href="items_list.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>

</body>
</html>