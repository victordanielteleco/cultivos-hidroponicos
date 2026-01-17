# Cultivos Hidropónicos

> **Crédito (Coautor): Jose Eduardo R. B.**  
> **Autor principal:** Victor Daniel  

Sistema web y base de datos para la **gestión de una granja hidropónica de interior**, orientado a la administración de **operarios**, **bandejas**, **especies**, **plantas** y **operaciones** (tareas), con generación de **informes PDF**.

---

## 📌 Descripción del proyecto
Este proyecto implementa una aplicación web (“Cultivos”) conectada a una base de datos para facilitar la administración de un cultivo hidropónico.  
El sistema está pensado para una granja interior donde las plantas se cultivan en bandejas apiladas en torres, con iluminación artificial y parámetros controlados.

---

## 🎯 Objetivos

### Objetivo general
Centralizar la información del cultivo hidropónico en una base de datos y ofrecer una interfaz web que permita gestionar los datos de forma estructurada.

### Objetivos específicos
- Gestionar **Operarios** (alta, edición, eliminación y listado).
- Gestionar **Especies** (alta, edición, eliminación y listado).
- Gestionar **Bandejas** (alta, edición, eliminación y listado).
- Gestionar información asociada a bandejas:
  - **Plantas** por bandeja
  - **Operaciones** (tareas) por bandeja
- Generar **informes PDF** de bandejas con datos relevantes del cultivo.

---

## 🛠️ Tecnologías utilizadas
- **PHP** (lógica del servidor)
- **HTML5**
- **CSS3**
- **JavaScript**
- **MySQL / MariaDB**
- **Bootstrap** (en la portada/plantilla principal)
- **FPDF** (generación de informes en PDF)

---

## 🧩 Modelo de datos (resumen académico)

### Tablas principales
- **operarios**: trabajadores y datos personales.
- **operaciones**: diario de tareas realizadas/planificadas sobre la explotación.
- **bandejas**: estado y posición de cada bandeja, y su especie asociada.
- **plantas**: plantas por bandeja y su porte/tamaño actual.
- **especies**: parámetros óptimos de crecimiento (luz, humedad, nutrientes, pH, EC, etc.).

### Tablas de soporte
- **operarios_baja**: registro de operarios dados de baja (soporte a borrados/recuperación).
- **control_bandejas**: histórico de cambios realizados en bandejas (valores antiguos vs nuevos).
- **tabla_mensajes**: mensajes generados por triggers (avisos internos).

### Relaciones principales (visión general)
- Un **operario** puede tener muchas **operaciones** (1 → ∞).
- Una **bandeja** puede tener muchas **operaciones** (1 → ∞).
- Una **bandeja** puede tener varias **plantas** (1 → ∞).
- Una **especie** puede estar en varias **bandejas** (1 → ∞), pero una bandeja solo alberga una especie.

---

## ⚡ Triggers y procedimientos (implementación BD)

### Triggers (ejemplos destacados)
- **BEFORE INSERT en plantas**: restringe el id numérico de planta para que una bandeja solo pueda tener plantas con id de 1 a 6.
- **AFTER INSERT en operaciones**: si una operación se inserta con `costo_materiales = NULL`, se registra un mensaje en `tabla_mensajes`.
- **BEFORE/AFTER UPDATE en bandejas**: almacena cambios en `control_bandejas`.
- **BEFORE/AFTER DELETE en operarios**: permite almacenar operarios eliminados en `operarios_baja` y recuperar información.

### Procedimientos almacenados
- Actualización del **porte_actual** en plantas.
- Actualización del **estado de tarea** en operaciones (incluyendo control de fechas de inicio/final).
- Actualización del **estado de bandeja** (incluyendo fechas asociadas a plantado/cosecha).

---

## ✅ Funcionalidades principales (aplicación web)
- Listados y formularios CRUD para:
  - **Operarios**
  - **Especies**
  - **Bandejas**
- Edición de **bandejas** con vista ampliada:
  - Datos principales de bandeja
  - Listado de plantas de la bandeja
  - Listado de operaciones asociadas
- **Informe PDF** por bandeja con datos completos (bandeja + plantas + operaciones).
- Validaciones destacadas en formularios:
  - DNI con formato controlado.
  - Teléfono con formato espaciado.
  - Id de especie con formato específico.
  - Reglas de edición guiadas en operaciones (estado Por_hacer / Haciendo / Hecha, autocompletado de tiempos, etc.).

---

## 📂 Estructura del proyecto
```txt
cultivos-hidroponicos/
│
├── Documentacion/ # Presentación y dossier del proyecto (PDF)
├── bandejas/ # Módulo bandejas (CRUD + informe)
├── especies/ # Módulo especies (CRUD)
├── operarios/ # Módulo operarios (CRUD)
├── scripts SQL creación e inserción/ # Scripts SQL (creación/inserción)
├── fpdf17/ # Librería para generación de PDFs
├── css/ # Hojas de estilo
├── js/ # Scripts JavaScript
├── images/ # Recursos gráficos
│
├── bd_funciones.inc
├── cultivos.css
├── cultivos_cabecera.inc
├── index.php
└── indexviejo.php
```

## ⚙️ Instalación y ejecución (local)

### 1️⃣ Requisitos
- Apache (XAMPP/WAMP/LAMP recomendado)
- PHP
- MySQL / MariaDB

### 2️⃣ Base de datos
En `scripts SQL creación e inserción/` tienes:
- **SQL Cultivos- creación BD.sql**
- **SQL Cultivos- insercion datos.sql**

Pasos recomendados:
1. Crear base de datos en MySQL/MariaDB.
2. Ejecutar el script de creación.
3. (Opcional) Ejecutar el script de inserción para datos iniciales.

### 3️⃣ Configuración de conexión
Revisa el archivo:
- `bd_funciones.inc`

(Ajusta host, usuario, contraseña y nombre de BD según tu entorno).

### 4️⃣ Ejecutar
Coloca el proyecto en el directorio del servidor:
- XAMPP → `htdocs/`

Y abre:
http://localhost/cultivos-hidroponicos/


---

## 📚 Documentación del proyecto
En `Documentacion/` se incluyen:
- Presentación del sistema
- Dossier técnico (modelo de datos, triggers, procedimientos y explicación de la app)

---

## ⚠️ Limitación conocida
Se documenta un problema relacionado con el input `datetime-local` en algunos navegadores, donde el valor **00** en segundos puede no capturarse correctamente, afectando la edición de fechas con segundos en operaciones.

---

## 👥 Autores
- **Victor Daniel** — Autor principal  
- **Jose Eduardo R. B.** — Coautor  

---

## 📜 Licencia / uso
Proyecto desarrollado con fines **educativos/académicos**.
