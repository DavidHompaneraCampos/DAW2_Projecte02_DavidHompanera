-- Creación de la base de datos
CREATE DATABASE restaurante_bbdd;
USE restaurante_bbdd;

-- Creación de las tablas
CREATE TABLE tbl_camarero (
    id_camarero INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_camarero VARCHAR(50) NOT NULL,
    apellidos_camarero VARCHAR(50) NOT NULL,
    username VARCHAR(25) NOT NULL UNIQUE,
    password CHAR(60) NOT NULL
);

CREATE TABLE tbl_mesa (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_sala INT NOT NULL,
    numero_sillas_mesa INT NOT NULL
);

CREATE TABLE tbl_sala ( 
    id_sala INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    ubicacion_sala VARCHAR(25) NOT NULL
);

CREATE TABLE tbl_ocupacion (
    id_ocupacion INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_mesa INT NOT NULL,
    id_camarero INT NULL,
    fecha_inicio DATETIME,
    fecha_final DATETIME,
    estado_ocupacion VARCHAR(25)
);

-- Añadir FK
ALTER TABLE tbl_mesa 
ADD CONSTRAINT FK_mesa_sala
FOREIGN KEY(id_sala) REFERENCES tbl_sala(id_sala);

ALTER TABLE tbl_ocupacion 
ADD CONSTRAINT FK_ocupacion_mesa
FOREIGN KEY(id_mesa) REFERENCES tbl_mesa(id_mesa);

ALTER TABLE tbl_ocupacion
ADD CONSTRAINT FK_ocupacion_camarero
FOREIGN KEY(id_camarero) REFERENCES tbl_camarero(id_camarero);