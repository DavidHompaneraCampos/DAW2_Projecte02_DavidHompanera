-- Inserciones para tbl_roles
INSERT INTO tbl_roles (nombre_rol) VALUES
('Administrador'),
('Camarero'),
('Gerente'),
('Personal Mantenimiento');

-- Inserciones para tbl_usuario (Incluye camareros y administrador)
INSERT INTO tbl_usuario (nombre_usuario, apellidos_usuario, username, password, id_rol) VALUES
('David', 'Hompanera Campos', 'hompa', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W', 2),
('Juan', 'García López', 'juang', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W', 2),
('María', 'Pérez Fernández', 'mariap', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W', 2),
('Carlos', 'Rodríguez Sánchez', 'carlosr', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W', 2),
('Laura', 'Martínez Gómez', 'lauram', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W', 2),
('Admin', 'Principal', 'admin', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W', 1);

-- Inserciones para tbl_sala
INSERT INTO tbl_sala (ubicacion_sala) VALUES
('Sala 1'),
('Sala 2'),
('Terraza Exterior 1'),
('Terraza Exterior 2'),
('Terraza Exterior 3'),
('Sala Privada 1'),
('Sala Privada 2'),
('Sala Privada 3'),
('Sala Privada 4');

-- Inserciones para tbl_mesa
INSERT INTO tbl_mesa (id_sala, numero_sillas_mesa) VALUES
(3, 6),
(1, 8),  
(1, 8),  
(2, 8),  
(2, 8),  
(4, 6),
(3, 6), 
(1, 6),
(1, 6),
(2, 6),
(2, 6),
(4, 6), 
(6, 4), 
(1, 8),  
(1, 8),  
(2, 8),  
(2, 8),  
(9, 4), 
(7, 4), 
(5, 2), 
(5, 4), 
(5, 4), 
(5, 2), 
(8, 4);

-- Generar sillas automáticamente basadas en el numero_sillas_mesa
INSERT INTO tbl_sillas (id_mesa, estado_silla)
SELECT m.id_mesa, 'disponible'
FROM tbl_mesa m
CROSS JOIN (SELECT 1 AS num UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
            UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8) AS n
WHERE n.num <= m.numero_sillas_mesa;

-- Inserciones para tbl_ocupacion (Reemplazando id_camarero con id_usuario)
INSERT INTO tbl_ocupacion (id_mesa, id_usuario, fecha_inicio, fecha_final, estado_ocupacion) VALUES
(1, 1, NULL, NULL, 'Disponible'),
(2, 2, NULL, NULL, 'Disponible'),
(3, 3, NULL, NULL, 'Disponible'),
(4, 4, NULL, NULL, 'Disponible'),
(5, 1, NULL, NULL, 'Disponible'),
(6, 2, NULL, NULL, 'Disponible'),
(7, 3, NULL, NULL, 'Disponible'),
(8, 4, NULL, NULL, 'Disponible'),
(9, 1, NULL, NULL, 'Disponible'),
(10, 2, NULL, NULL, 'Disponible'),
(11, 3, NULL, NULL, 'Disponible'),
(12, 4, NULL, NULL, 'Disponible'),
(13, 1, NULL, NULL, 'Disponible'),
(14, 2, NULL, NULL, 'Disponible'),
(15, 3, NULL, NULL, 'Disponible'),
(16, 4, NULL, NULL, 'Disponible'),
(17, 1, NULL, NULL, 'Disponible'),
(18, 2, NULL, NULL, 'Disponible'),
(19, 3, NULL, NULL, 'Disponible'),
(20, 4, NULL, NULL, 'Disponible'),
(21, 1, NULL, NULL, 'Disponible'),
(22, 2, NULL, NULL, 'Disponible'),
(23, 3, NULL, NULL, 'Disponible'),
(24, 4, NULL, NULL, 'Disponible'),
(9, 1, NULL, NULL, 'Disponible'),
(2, 2, NULL, NULL, 'Disponible');

-- Inserciones para tbl_reservas
INSERT INTO tbl_reservas (id_mesa, id_usuario, fecha_reserva, estado_reserva, id_usuario_modificacion, fecha_modificacion) VALUES
(1, 2, '2024-01-15 14:30:00', 'Completada', 1, '2024-01-15 16:31:00'),
(3, 3, '2024-02-20 15:00:00', 'Cancelada', 1, '2024-02-19 10:00:00'),
(4, 5, CURRENT_TIMESTAMP + INTERVAL 3 DAY, 'Confirmada', 1, CURRENT_TIMESTAMP);