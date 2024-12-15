CREATE DATABASE restaurante2_bbdd;
USE restaurante2_bbdd;

CREATE TABLE tbl_usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL,
    apellidos_usuario VARCHAR(50) NOT NULL,
    username VARCHAR(25) NOT NULL UNIQUE,
    password CHAR(60) NOT NULL,
    id_rol INT
);

CREATE TABLE tbl_roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

CREATE TABLE tbl_mesa (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_sala INT NOT NULL,
    numero_sillas_mesa INT NOT NULL
);

CREATE TABLE tbl_sillas (
    id_silla INT AUTO_INCREMENT PRIMARY KEY,
    id_mesa INT NOT NULL,
    estado_silla ENUM('disponible', 'rota') DEFAULT 'disponible'
);

CREATE TABLE tbl_sala ( 
    id_sala INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    ubicacion_sala VARCHAR(25) NOT NULL,
    imagen_sala VARCHAR(255) NULL
);

CREATE TABLE tbl_ocupacion (
    id_ocupacion INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_mesa INT NOT NULL,
    id_usuario INT NULL,
    fecha_inicio DATETIME,
    fecha_final DATETIME,
    estado_ocupacion VARCHAR(25)
);

CREATE TABLE tbl_reservas (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    id_mesa INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_reserva DATETIME NOT NULL,
    estado_reserva ENUM('Pendiente', 'Confirmada', 'Completada', 'Cancelada') DEFAULT 'Pendiente',
    id_usuario_modificacion INT NULL,
    fecha_modificacion TIMESTAMP NULL
);

-- Añadir FK
ALTER TABLE tbl_usuario
ADD CONSTRAINT FK_usuario_rol
FOREIGN KEY (id_rol) REFERENCES tbl_roles(id_rol);

ALTER TABLE tbl_sillas 
ADD CONSTRAINT FK_sillas_mesa
FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa);

ALTER TABLE tbl_mesa
ADD CONSTRAINT FK_mesa_sala
FOREIGN KEY(id_sala) REFERENCES tbl_sala(id_sala);

ALTER TABLE tbl_ocupacion 
ADD CONSTRAINT FK_ocupacion_mesa
FOREIGN KEY(id_mesa) REFERENCES tbl_mesa(id_mesa);

ALTER TABLE tbl_ocupacion
ADD CONSTRAINT FK_ocupacion_usuario
FOREIGN KEY(id_usuario) REFERENCES tbl_usuario(id_usuario);

ALTER TABLE tbl_reservas 
ADD CONSTRAINT FK_reservas_mesa
FOREIGN KEY(id_mesa) REFERENCES tbl_mesa(id_mesa);

ALTER TABLE tbl_reservas
ADD CONSTRAINT FK_reservas_usuario
FOREIGN KEY(id_usuario) REFERENCES tbl_usuario(id_usuario);

ALTER TABLE tbl_reservas
ADD CONSTRAINT FK_reservas_usuario_modificacion
FOREIGN KEY(id_usuario_modificacion) REFERENCES tbl_usuario(id_usuario);