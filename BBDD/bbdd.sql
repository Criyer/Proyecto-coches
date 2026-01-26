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
VALUES ('Administrador', 'admin@crmotors.com', '$2y$10$7rW6pS9.F5b5uS.tN.Kj9uL4p5p6z1w2x3y4z5a6b7c8d9e0f1g2h', 1);

INSERT INTO coches (modelo, precio, anio, kms, motor, imagen, estado) 
VALUES 
('BMW Serie 3 M-Sport', 32900.00, 2021, '45.000 KM', 'DIÉSEL', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&q=80&w=600', 0),
('Audi A4 Avant', 27500.00, 2020, '60.000 KM', 'HÍBRIDO', 'https://images.unsplash.com/photo-1603584173870-7f3ca9128146?auto=format&fit=crop&q=80&w=600', 0),
('Mercedes Clase C', 24000.00, 2019, '85.000 KM', 'GASOLINA', 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&q=80&w=600', 1);