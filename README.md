# CinePlus

## Descripción
Sitio web de películas y series. Permite registrarse, iniciar sesión y ver el catálogo
(con géneros, duración o temporadas y sinopsis) en la página principal.
También hay una página que muestra las tablas de la base de datos.

Trabajo Práctico N°6 - Prácticas Profesionalizantes I (versión corta) - 1° B Tecnicatura en Analísta de Sistemas.

## Integrante
- Camila Barraza

## Tecnologías utilizadas
- HTML y CSS
- PHP (PDO, sesiones, `password_hash` / `password_verify`)
- MySQL / MariaDB
- Git

## Requisitos
- Servidor con PHP
- MySQL
- Extensión PDO MySQL habilitada

## Pasos para crear la base de datos
1. Abrir phpMyAdmin.
2. Importar el archivo `database/cineplus.sql`.
   - En local (XAMPP) se puede importar directo: crea la base `cineplus`.
   - En un hosting (InfinityFree): crear la base desde el panel, borrar las dos primeras
     líneas del `.sql` (`CREATE DATABASE` y `USE`), elegir esa base en phpMyAdmin e importar.

## Pasos para ejecutar el proyecto
1. Copiar la carpeta del proyecto a `htdocs` (XAMPP) o subirla al hosting.
2. Revisar los datos de `config/Conexion.php` (servidor, base, usuario y clave).
3. Abrir `index.php` desde el navegador.

## Usuarios de prueba
| Rol | Correo | Contraseña |
|-----|--------|------------|
| Administrador | admin@cineplus.com | admin123 |
| Usuario común | usuario@cineplus.com | usuario123 |

## Roles disponibles
- **usuario**: es el rol que se asigna al registrarse desde el formulario.
- **admin**: rol cargado en la base (en esta versión reducida no tiene funciones extra).
- **editor**: definido en la base para el rol especializado, sin uso en esta versión.

## Funcionalidades principales
- Registro de usuarios con validaciones (campos obligatorios, correo válido y único,
  confirmación de contraseña).
- Inicio de sesión y cierre de sesión con sesiones de PHP.
- Contraseñas guardadas con `password_hash`.
- Consultas preparadas con PDO.
- Catálogo de películas y series en la página principal.
- Página `datos.php` que muestra las tablas de la base de datos.
