<?php
// app/pdo.php
// Este archivo se encarga únicamente de conectar a la base de datos usando PDO(PHP Data Objects).

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'decarton_db';
$user = 'root';     // usuario de MariaDB/MySQL
$pass = '';         // contraseña

try {
    // Data Source Name
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    // Opciones de PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa sentencias preparadas reales (seguridad)
    ];

    // Crear la instancia PDO
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    // Si falla, mostramos un mensaje genérico al usuario y guardamos el error real en un log
    // No hacer echo $e->getMessage() en producción por seguridad
    error_log($e->getMessage());
    die("Error crítico de conexión a la base de datos. Por favor, intente más tarde.");
}
?>