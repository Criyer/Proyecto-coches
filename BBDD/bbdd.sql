CREATE DATABASE IF NOT EXISTS concesionario_db;
USE concesionario_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol INT DEFAULT 0 
);

CREATE TABLE IF NOT EXISTS coches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    anio INT NOT NULL,
    kms VARCHAR(50) NOT NULL,
    motor VARCHAR(50) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    estado INT DEFAULT 0 
);

INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'admin@crmotors.com', '$2y$10$NzUjJ2qsUvn9ThT3eFhheeb3Pz.Ho72V6Qsb/f2QXY22YfrCqW4E6', 1);

//CONTRASEÑA DEL ADMIN: admin123
