<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    header("Location: " . rtrim($base, '/') . "/HTML/login.html");
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
        :root { --rojo: #c40000; --negro: #1a1a1a; --gris: #f4f4f4; --verde: #28a745; --gris-oscuro: #555; }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--gris); margin: 0; padding: 20px; }

        /* cabecera */
        .cabecera-admin {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white; padding: 22px 28px; border-radius: 12px;
            margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .cabecera-admin-top {
            display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 12px;
        }
        .cabecera-admin h1 { margin: 0; font-size: 1.6rem; letter-spacing: 1px; font-weight: 700; }
        .cabecera-admin h1 span { color: var(--rojo); }
        .cabecera-admin-user { display: flex; align-items: center; gap: 16px; font-size: 0.9rem; color: #bbb; }
        .cabecera-admin-user strong { color: white; font-size: 1rem; }
        .btn-salir {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--rojo); color: white; text-decoration: none;
            font-weight: 700; font-size: 0.8rem; letter-spacing: 0.5px;
            padding: 9px 18px; border-radius: 6px;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            text-transform: uppercase;
        }
        .btn-salir:hover { background: #a00000; box-shadow: 0 4px 14px rgba(196,0,0,0.5); transform: translateY(-1px); }

        /* layout */
        .contenedor-gestion { display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }
        @media (max-width: 900px) { .contenedor-gestion { grid-template-columns: 1fr; } }
        .seccion-admin { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .seccion-admin h2 { border-bottom: 2px solid var(--rojo); padding-bottom: 10px; margin-top: 0; font-size: 1.1rem; }

        /* formulario alta */
        .form-alta input, .form-alta select { width: 100%; padding: 11px 14px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem; }
        .form-alta input:focus { outline: none; border-color: var(--rojo); }
        .btn-guardar {
            width: 100%; padding: 12px; background: var(--rojo); color: white; border: none;
            border-radius: 6px; font-weight: 700; cursor: pointer; text-transform: uppercase;
            letter-spacing: 0.5px; font-size: 0.9rem; transition: background 0.2s;
        }

        /* input file */
        .input-file-wrapper { position: relative; margin-bottom: 10px; }
        .input-file-wrapper input[type=file] {
            width: 100%; padding: 9px 14px;
            border: 1px dashed #ccc; border-radius: 6px;
            font-size: 0.85rem; cursor: pointer;
            background: #fafafa;
        }
        .input-file-wrapper input[type=file]:hover { border-color: var(--rojo); }
        .preview-img {
            width: 100%; height: 120px; object-fit: cover;
            border-radius: 6px; margin-top: 6px;
            display: none; border: 1px solid #eee;
        }
        .preview-img.visible { display: block; }

        /* lista de vehiculos */
        .item-admin {
            display: grid; grid-template-columns: 80px 1fr auto;
            align-items: center; gap: 14px; padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .item-admin:last-child { border-bottom: none; }
        .item-admin img { width: 80px; height: 55px; object-fit: cover; border-radius: 6px; }
        .item-admin-info h4 { margin: 0 0 2px; font-size: 0.95rem; }
        .item-admin-info .precio { font-size: 0.85rem; font-weight: 700; color: var(--rojo); }
        .item-admin-info .detalles { font-size: 0.75rem; color: #888; }
        .badge {
            display: inline-block; padding: 2px 8px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 3px;
        }
        .badge-venta { background: #e8f5e9; color: #28a745; }
        .badge-vendido { background: #fce4e4; color: var(--rojo); }
        .botones-item { display: flex; flex-direction: column; gap: 5px; }
        .btn-accion {
            padding: 5px 12px; border: none; border-radius: 4px; cursor: pointer;
            font-size: 0.72rem; font-weight: 700; white-space: nowrap; transition: opacity 0.15s;
        }
        .btn-accion:hover { opacity: 0.85; }
        .btn-editar   { background: #005cbf; color: white; }
        .btn-vender   { background: var(--negro); color: white; }
        .btn-restaurar{ background: var(--verde); color: white; }
        .btn-borrar   { background: #aaa; color: white; }
        .sin-coches   { text-align: center; color: #aaa; padding: 30px; font-style: italic; }

        /* modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 1000;
            justify-content: center; align-items: center;
        }
        .modal-overlay.activo { display: flex; }
        .modal {
            background: white; border-radius: 14px; padding: 30px;
            width: 100%; max-width: 480px; box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            animation: slideUp 0.2s ease;
        }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal h3 { margin-top: 0; font-size: 1.15rem; border-bottom: 2px solid var(--rojo); padding-bottom: 10px; }
        .modal input { width: 100%; padding: 10px 13px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem; }
        .modal input:focus { outline: none; border-color: var(--rojo); }
        .modal-botones { display: flex; gap: 10px; margin-top: 6px; }
        .modal-botones button { flex: 1; padding: 11px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 0.9rem; }
        .btn-modal-guardar { background: var(--rojo); color: white; }
        .btn-modal-cancelar { background: #eee; color: #555; }
    </style>

</head>
<body>

    <!-- CABECERA -->
    <header class="cabecera-admin">
        <div class="cabecera-admin-top">
            <h1>PANEL DE <span>CONTROL</span> · CR MOTORS</h1>
            <div class="cabecera-admin-user">
                <span>Sesión activa: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
                <a href="logout.php" class="btn-salir">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="contenedor-gestion">

        <!-- FORMULARIO ALTA -->
        <div class="seccion-admin">
            <h2>Añadir Vehículo</h2>
            <form action="PHP/api_gestion_coches.php" method="POST" class="form-alta" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear">
                <input type="text"   name="modelo" placeholder="Marca y Modelo (Ej: BMW Serie 3)" required>
                <input type="number" name="precio"  placeholder="Precio en €" step="0.01" required>
                <input type="number" name="anio"    placeholder="Año (Ej: 2022)" required>
                <input type="text"   name="kms"     placeholder="Kilómetros (Ej: 45.000 KM)" required>
                <input type="text"   name="motor"   placeholder="Combustible (DIÉSEL / GASOLINA...)" required>
                <div class="input-file-wrapper">
                    <input type="file" name="imagen" id="imgCrear" accept="image/*" required onchange="previewImg(this,'previewCrear')">
                    <img id="previewCrear" class="preview-img" alt="Vista previa">
                </div>
                <button type="submit" class="btn-guardar">Registrar en Inventario</button>
            </form>
        </div>

        <!-- LISTA DE VEHÍCULOS -->
        <div class="seccion-admin">
            <h2>Inventario (<?php echo mysqli_num_rows($consulta); ?> vehículos)</h2>
            <div id="lista-gestion">
                <?php if (mysqli_num_rows($consulta) == 0): ?>
                    <p class="sin-coches">No hay vehículos en el inventario. Añade el primero.</p>
                <?php else: ?>
                    <?php while($c = mysqli_fetch_assoc($consulta)): ?>
                    <div class="item-admin">
                        <img src="<?php echo htmlspecialchars($c['imagen']); ?>" alt="Coche" onerror="this.src='https://via.placeholder.com/80x55?text=Sin+imagen'">
                        <div class="item-admin-info">
                            <h4><?php echo htmlspecialchars($c['modelo']); ?></h4>
                            <div class="precio"><?php echo number_format($c['precio'], 0, ',', '.'); ?> €</div>
                            <div class="detalles"><?php echo $c['anio']; ?> · <?php echo htmlspecialchars($c['kms']); ?> · <?php echo htmlspecialchars($c['motor']); ?></div>
                            <span class="badge <?php echo $c['estado'] == 0 ? 'badge-venta' : 'badge-vendido'; ?>">
                                <?php echo $c['estado'] == 0 ? 'En Venta' : 'Vendido'; ?>
                            </span>
                        </div>
                        <div class="botones-item">
                            <button class="btn-accion btn-editar" onclick="abrirModal(<?php echo htmlspecialchars(json_encode($c)); ?>)">Editar</button>
                            <?php if($c['estado'] == 0): ?>
                                <button class="btn-accion btn-vender"    onclick="cambiarEstado(<?php echo $c['id']; ?>, 1)">Marcar Vendido</button>
                            <?php else: ?>
                                <button class="btn-accion btn-restaurar" onclick="cambiarEstado(<?php echo $c['id']; ?>, 0)">Restaurar</button>
                            <?php endif; ?>
                            <button class="btn-accion btn-borrar" onclick="borrarCoche(<?php echo $c['id']; ?>)">Eliminar</button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- MODAL EDICION -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <h3>Editar Vehículo</h3>
            <input type="hidden" id="edit-id">
            <input type="hidden" id="edit-imagen-actual">
            <input type="text"   id="edit-modelo" placeholder="Marca y Modelo">
            <input type="number" id="edit-precio"  placeholder="Precio en €" step="0.01">
            <input type="number" id="edit-anio"    placeholder="Año">
            <input type="text"   id="edit-kms"     placeholder="Kilómetros">
            <input type="text"   id="edit-motor"   placeholder="Combustible">
            <div class="input-file-wrapper">
                <p style="margin:0 0 4px;font-size:0.8rem;color:#888;">Cambiar imagen (Si no seleccionas ninguna, se conserva la actual)</p>
                <input type="file" id="edit-imagen" accept="image/*" onchange="previewImg(this,'previewEdit')">
                <img id="previewEdit" class="preview-img" alt="Vista previa">
            </div>
            <div class="modal-botones">
                <button class="btn-modal-cancelar" onclick="cerrarModal()">Cancelar</button>
                <button class="btn-modal-guardar"  onclick="guardarEdicion()">Guardar cambios</button>
            </div>
        </div>
    </div>

    <script>
        function previewImg(input, targetId) {
            var img = document.getElementById(targetId);
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { img.src = e.target.result; img.classList.add('visible'); };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function abrirModal(coche) {
            document.getElementById('edit-id').value            = coche.id;
            document.getElementById('edit-imagen-actual').value = coche.imagen;
            document.getElementById('edit-modelo').value        = coche.modelo;
            document.getElementById('edit-precio').value        = coche.precio;
            document.getElementById('edit-anio').value          = coche.anio;
            document.getElementById('edit-kms').value           = coche.kms;
            document.getElementById('edit-motor').value         = coche.motor;
            document.getElementById('edit-imagen').value = '';
            var prev = document.getElementById('previewEdit');
            prev.src = coche.imagen;
            prev.classList.add('visible');
            document.getElementById('modalOverlay').classList.add('activo');
        }

        function cerrarModal() {
            document.getElementById('modalOverlay').classList.remove('activo');
        }

        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });

        async function guardarEdicion() {
            var f = new FormData();
            f.append('accion',        'editar');
            f.append('id',            document.getElementById('edit-id').value);
            f.append('modelo',        document.getElementById('edit-modelo').value);
            f.append('precio',        document.getElementById('edit-precio').value);
            f.append('anio',          document.getElementById('edit-anio').value);
            f.append('kms',           document.getElementById('edit-kms').value);
            f.append('motor',         document.getElementById('edit-motor').value);
            f.append('imagen_actual', document.getElementById('edit-imagen-actual').value);
            var fileInput = document.getElementById('edit-imagen');
            if (fileInput.files.length > 0) {
                f.append('imagen', fileInput.files[0]);
            }
            var r    = await fetch('PHP/api_gestion_coches.php', { method: 'POST', body: f });
            var data = await r.json();
            if (data.success) { cerrarModal(); location.reload(); }
            else alert('Error al guardar los cambios.');
        }

        async function cambiarEstado(id, nuevoEstado) {
            var f = new FormData();
            f.append('accion', 'estado');
            f.append('id', id);
            f.append('estado', nuevoEstado);
            var r = await fetch('PHP/api_gestion_coches.php', { method: 'POST', body: f });
            var d = await r.json();
            if (d.success) location.reload();
        }

        async function borrarCoche(id) {
            if (!confirm('¿Eliminar este vehículo del sistema? Esta acción no se puede deshacer.')) return;
            var f = new FormData();
            f.append('accion', 'borrar');
            f.append('id', id);
            var r = await fetch('PHP/api_gestion_coches.php', { method: 'POST', body: f });
            var d = await r.json();
            if (d.success) location.reload();
        }
    </script>
</body>
</html>