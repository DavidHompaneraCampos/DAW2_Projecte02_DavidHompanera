# 🍽️ Sistema de Reserva y Gestión de Mesas en un Restaurante 🍽️

## 📋 Proyecto PJ 02: TRANSVERSAL

### 🎯 Objetivo
Este proyecto tiene como objetivo **mejorar** y **ampliar** las funcionalidades desarrolladas en el **PJ 01**. 
Ahora, el sistema permite reservar mesas de manera anticipada, gestionar recursos como salas, mesas y sillas, y realizar una administración completa de usuarios y recursos.

### ⏳ Duración
Duración estimada del proyecto: **35 horas**

---

## 🏛️ Estructura del Restaurante
El restaurante está organizado en diferentes espacios:
- **3 terrazas**
- **2 comedores**
- **4 salas privadas**

Cada espacio tiene mesas, y cada mesa puede tener un número específico de sillas.

---

## ⚙️ Funcionamiento General de la Aplicación

1. **Gestión de Mesas y Sillas:**
   - Los **camareros** pueden visualizar las mesas disponibles y ocupadas en tiempo real.
   - Se permite gestionar las **sillas** asociadas a cada mesa.

2. **Reservas Anticipadas:**
   - Posibilidad de **reservar mesas** en un día y franja horaria específicos.
   - Visualización de **histórico de reservas** para un mejor control.

3. **Administración de Recursos y Usuarios:**
   - El **administrador** tiene acceso a CRUDs para:
     - **Usuarios** (camareros, gerentes, mantenimiento, etc.)
     - **Recursos** (salas, mesas, sillas)

4. **Asignación de Imágenes:**
   - Posibilidad de **asignar imágenes** a cada sala para una mejor representación visual.

---

## 👤 Roles del Sistema

1. **Administrador:**
   - Realiza el mantenimiento de usuarios y recursos (CRUDs).
   - Gestiona la asignación de imágenes a las salas.

2. **Camarero:**
   - Realiza reservas de mesas.
   - Gestiona mesas y sillas, marcando su ocupación y liberación.

---

## 🔑 Usuarios de Prueba

| Rol            | Usuario       | Contraseña  |
|----------------|---------------|-------------|
| Administrador  | admin         | Admin1234   |
| Camarero       | camarero01    | Camarero123 |
| Mantenimiento  | mantenimiento | Mantenimiento123 |

---

## 🏗️ Estructura de la Aplicación

### **1. Inicio de Sesión (Login)**
   - Validación en **cliente (JavaScript)** y en **servidor (PHP)**.
   - Acceso solo si el usuario y la contraseña son correctos.

### **2. Gestión de Recursos (Administrador)**
   - CRUD completo de:
     - Salas
     - Mesas
     - Sillas
   - Asignación y actualización de imágenes para cada sala.

### **3. Reserva de Mesas (Camarero)**
   - Visualización de salas y mesas disponibles.
   - Opción de **reservar** una mesa.
   - Estado de ocupación en tiempo real.

### **4. Histórico de Reservas**
   - Visualización detallada y filtrada de todas las reservas.

### **5. Gestión de Sillas**
   - Visualización de las sillas asociadas a cada mesa.
   - Opción de **editar** o **eliminar** una silla.

---

## 💾 Base de Datos

La base de datos utilizada es **MySQL** y cuenta con las siguientes tablas principales:
1. `tbl_usuario` → Almacena los usuarios.
2. `tbl_roles` → Roles del sistema (Administrador, Camarero, etc.).
3. `tbl_sala` → Información de las salas.
4. `tbl_mesa` → Información de las mesas.
5. `tbl_sillas` → Sillas asociadas a mesas.
6. `tbl_reservas` → Información de reservas.
7. `tbl_ocupacion` → Estado de ocupación de mesas.

### 📂 Esquema de la Base de Datos
```sql
CREATE TABLE tbl_sala ( 
    id_sala INT PRIMARY KEY AUTO_INCREMENT,
    ubicacion_sala VARCHAR(25) NOT NULL,
    imagen_sala VARCHAR(255)
);

CREATE TABLE tbl_mesa (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT,
    id_sala INT NOT NULL,
    numero_sillas_mesa INT NOT NULL,
    FOREIGN KEY (id_sala) REFERENCES tbl_sala(id_sala)
);

CREATE TABLE tbl_sillas (
    id_silla INT PRIMARY KEY AUTO_INCREMENT,
    id_mesa INT NOT NULL,
    estado_silla ENUM('disponible', 'rota') DEFAULT 'disponible',
    FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa)
);

- **👨‍💻David Hompanera**