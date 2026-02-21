# Proyecto Concesionario - CR MOTORS

Este es un sistema para gestionar un concesionario de coches. Tiene una parte para clientes donde pueden ver el catálogo y otra para administradores donde se gestiona el inventario.

## Funcionalidades principales

- **Panel de administración**: Permite añadir coches nuevos, editar los datos de los que ya están, marcarlos como vendidos o borrarlos.
- **Catálogo**: Los clientes pueden ver los coches disponibles y los que ya se han vendido.
- **Usuarios**: Sistema de registro y login para clientes y admin. Las contraseñas van encriptadas.
- **API propia**: El frontend se comunica con el backend mediante JSON.

## Tecnologías

- PHP para la lógica del servidor.
- MySQL para la base de datos.
- JavaScript para el catálogo dinámico y las alertas.
- CSS para el diseño.
- SweetAlert2 para las ventanas de aviso.

## Cómo instalarlo

1. Copia la carpeta del proyecto en `htdocs` de XAMPP.
2. Abre phpMyAdmin y crea una base de datos llamada `concesionario_db`.
3. Importa el archivo `bbdd.sql` que está en la carpeta `BBDD`.
4. Mira en `PHP/db.php` que los datos de tu conexión sean correctos.
5. Abre en el navegador: `http://localhost/proyecto_cochesCBO/HTML/index.html`

## Estructura de carpetas

- `HTML`: Las páginas de la web.
- `CSS`: Los archivos de estilos.
- `JS`: Lógica de JavaScript.
- `PHP`: Scripts de servidor y API.
- `IMAGENES`: Fotos de los coches.
- `BBDD`: El script de la base de datos.
