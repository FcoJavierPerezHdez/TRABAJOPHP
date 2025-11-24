-- Creamos la base de datos
CREATE DATABASE IF NOT EXISTS decarton_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE decarton_db;

-- Tabla de Usuarios (Para el futuro Login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Aquí guardaremos el HASH, no la contraseña plana
    role ENUM('admin', 'cliente') DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de Productos (Items)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255), -- Guardaremos la ruta de la imagen
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla para Auditoría (Requisito: Transacción en borrado con auditoría)
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_type VARCHAR(50) NOT NULL, -- Ej: 'DELETE_PRODUCT'
    description TEXT NOT NULL,
    user_id INT, -- Quién lo hizo (puede ser NULL si se borra el usuario)
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertamos un usuario de prueba (Password: '1234' hasheada con BCRYPT)
-- Esto nos servirá para probar la conexión
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@decarton.com', '$2y$10$abcdefghijk1234567890O...HASH_FALSO_PARA_EJEMPLO', 'admin');

-- Insertamos un par de productos de prueba
INSERT INTO products (name, description, price, stock) VALUES 
('Balón de Fútbol', 'Balón reglamentario talla 5', 19.99, 50),
('Raqueta de Tenis', 'Raqueta ligera de grafito', 45.50, 20);