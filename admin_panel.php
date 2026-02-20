<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: HTML/login.html");
    exit();
}

require 'PHP/db.php';

$consulta = mysqli_query($conexion, "SELECT * FROM coches ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | CR MOTORS</title>
    <link rel="stylesheet" href="CSS/estilos.css">
    <style>
        :root { --rojo: #c40000; --negro: #1a1a1a; --gris: #f4f4f4; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--gris); margin: 0; padding: 20px; }
        
        .cabecera-admin { background: var(--negro); color: white; padding: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .cabecera-admin h1 { margin: 0; font-size: 1.5rem; }
        .btn-salir { color: var(--rojo); text-decoration: none; font-weight: bold; border: 1px solid var(--rojo); padding: 8px 15px; border-radius: 5px; }

        .contenedor-gestion { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .seccion-admin { background: white; padding: 20px; border-radius: 10px; shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .seccion-admin h2 { border-bottom: 2px solid var(--rojo); padding-bottom: 10px; margin-top: 0; }

        
        .form-alta input, .form-alta button { width: 100%; padding: 12px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-guardar { background: var(--rojo); color: white; border: none; font-weight: bold; cursor: pointer; text-transform: uppercase; }

        
        .item-admin { display: flex; align-items: center; gap: 15px; padding: 10px; border-bottom: 1px solid #eee; }
        .item-admin img { width: 60px; height: 45px; object-fit: cover; border-radius: 4px; }
        .item-admin-info { flex-grow: 1; }
        .item-admin-info h4 { margin: 0; font-size: 0.9rem; }
        .btn-accion { padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; font-size: 0.75rem; font-weight: bold; }
        .btn-vender { background: var(--negro); color: white; }
        .btn-restaurar { background: #28a745; color: white; }
        .btn-borrar { background: #666; color: white; margin-left: 5px; }
    </style>
</head>
<body>

    <header class="cabecera-admin">
        <h1>PANEL DE CONTROL: CR MOTORS</h1>
        <div>
            <span style="margin-right: 20px;">Hola, <strong><?php echo $_SESSION['nombre']; ?></strong></span>
            <a href="PHP/logout.php" class="btn-salir">CERRAR SESIÓN</a>
        </div>
    </header>

    <div class="contenedor-gestion">
        
        
        <div class="seccion-admin">
            <h2>Añadir Nuevo Vehículo</h2>
            <form action="PHP/api_gestion_coches.php" method="POST" class="form-alta">
                <input type="hidden" name="accion" value="crear">
                <input type="text" name="modelo" placeholder="Marca y Modelo (Ej: BMW Serie 3)" required>
                <input type="number" name="precio" placeholder="Precio en €" required>
                <input type="number" name="anio" placeholder="Año" required>
                <input type="text" name="kms" placeholder="Kilómetros (Ej: 45.000 KM)" required>
                <input type="text" name="motor" placeholder="Motor (Ej: DIÉSEL)" required>
                <input type="text" name="imagen" placeholder="URL de la Imagen" required>
                <button type="submit" class="btn-guardar">Registrar en Inventario</button>
            </form>
        </div>

        
        <div class="seccion-admin">
            <h2>Gestión de Estados</h2>
            
            <div id="lista-gestion">
                <?php while($c = mysqli_fetch_assoc($consulta)): ?>
                <div class="item-admin">
                    <img src="<?php echo $c['imagen']; ?>" alt="Coche">
                    <div class="item-admin-info">
                        <h4><?php echo $c['modelo']; ?></h4>
                        <small><?php echo ($c['estado'] == 0) ? "En Venta" : "Vendido"; ?></small>
                    </div>
                    <div class="botones">
                        <?php if($c['estado'] == 0): ?>
                            <button class="btn-accion btn-vender" onclick="cambiarEstado(<?php echo $c['id']; ?>, 1)">Marcar Vendido</button>
                        <?php else: ?>
                            <button class="btn-accion btn-restaurar" onclick="cambiarEstado(<?php echo $c['id']; ?>, 0)">Restaurar</button>
                        <?php endif; ?>
                        
                        <button class="btn-accion btn-borrar" onclick="borrarCoche(<?php echo $c['id']; ?>)">Eliminar</button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

    </div>

    <script>
        
        async function cambiarEstado(id, nuevoEstado) {
            const f = new FormData();
            f.append('accion', 'estado');
            f.append('id', id);
            f.append('estado', nuevoEstado);

            const r = await fetch('PHP/api_gestion_coches.php', { method: 'POST', body: f });
            if(r.ok) location.reload();
        }

        
        async function borrarCoche(id) {
            if(confirm('¿Estás seguro de que quieres eliminar este vehículo del sistema?')) {
                const f = new FormData();
                f.append('accion', 'borrar');
                f.append('id', id);
                const r = await fetch('PHP/api_gestion_coches.php', { method: 'POST', body: f });
                if(r.ok) location.reload();
            }
        }
    </script>
</body>
</html>