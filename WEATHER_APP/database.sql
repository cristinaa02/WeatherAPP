CREATE DATABASE IF NOT EXISTS weather_db;
USE weather_db;

CREATE TABLE IF NOT EXISTS consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciudad VARCHAR(100) NOT NULL,
    latitud DECIMAL(10, 8) NOT NULL,
    longitud DECIMAL(11, 8) NOT NULL,
    temperatura DECIMAL(5, 2) NOT NULL,
    descripcion VARCHAR(100),
    icon VARCHAR(20),
    fecha_consulta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);