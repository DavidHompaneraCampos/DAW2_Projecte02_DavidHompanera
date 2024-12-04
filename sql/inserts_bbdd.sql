INSERT INTO tbl_sala (ubicacion_sala) VALUES
('Sala'),
('Terraza Exterior'),
('Sala Privada');


INSERT INTO tbl_camarero (nombre_camarero, apellidos_camarero, username, password) VALUES
('Juan', 'García López', 'juang', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W'),
('María', 'Pérez Fernández', 'mariap', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W'),
('Carlos', 'Rodríguez Sánchez', 'carlosr', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W'),
('Laura', 'Martínez Gómez', 'lauram', '$2y$10$GMV4gk/G7uCYrrRf/4YnmukMLnLwYcTElPR3ssLZuS8cZ4D8JBI8W');

-- Insertar mesas asociadas a cada sala
INSERT INTO tbl_mesa (id_sala, numero_sillas_mesa) VALUES
(2, 6),
(1, 8),  
(1, 8),  
(1, 8),  
(1, 8),  
(2, 6),
(2, 6), 
(1, 6),
(1, 6),
(1, 6),
(1, 6),
(2, 6), 
(3, 4), 
(1, 8),  
(1, 8),  
(1, 8),  
(1, 8),  
(3, 4), 
(3, 4), 
(2, 2), 
(2, 4), 
(2, 4), 
(2, 2), 
(3, 4); 

INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_inicio, fecha_final, estado_ocupacion) VALUES
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
(24, 4, NULL, NULL, 'Disponible'),
(9, 1, NULL, NULL, 'Disponible'),
(2, 2, NULL, NULL, 'Disponible');