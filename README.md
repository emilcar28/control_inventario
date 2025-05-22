#  Control de Inventario del Gabinete de Simulación

Este sistema de gestión de inventario fue desarrollado en PHP clásico, con base de datos MySQL y ejecutado en un entorno local XAMPP. Su propósito es facilitar el seguimiento, organización y control de los artículos del Gabinete de Simulación de la Facultad de Medicina - UNNE.

---

# Funcionalidades principales

- 🔐 **Inicio de sesión** con roles (admin y usuarios estándar).
- 🧾 **Gestión completa de artículos**:
  - Alta, edición y baja de artículos.
  - Asignación de imagen, categoría, ubicación física y cantidad (stock).
  - Generación automática de código QR por artículo.
- 📂 **Categorías de artículos** editables.
- 🔍 **Filtrado por categoría y tipo de movimiento**.
- 📈 **Movimientos de inventario**:
  - Entrada, salida, alta, baja, en préstamo, devolución, mantenimiento, traslado, reparado.
- 🗺️ **Ubicación física** del artículo (editable).
- 📸 **Carga de imagen** del artículo y almacenamiento local.
- 🧑‍💼 **Administración de usuarios**:
  - Usuario administrador por defecto (`admin` / `medicina2025`).
  - Gestión de roles y permisos.

---

#Tecnologías utilizadas

- PHP clásico (sin frameworks)
- MySQL
- HTML/CSS básico
- JavaScript
- [Endroid QR Code](https://github.com/endroid/qr-code) para generar QR
- Bootstrap (si lo aplicaste visualmente)
- Visual Studio Code
- XAMPP (Apache + MySQL)

---

# Requisitos

- PHP 7.4 o superior
- Composer instalado
- XAMPP o similar para entorno local
- Navegador moderno

---
