<?php require_once __DIR__ . '/../../config.php';
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['ID_Rol'] != 3) {
    header("Location: " . BASE_URL . "/vista/public/error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>/images/Logo.svg">
    <title>Pizzería Dominico - Usuarios</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/src/css/usuarios.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/src/css/slidebarAdmin.css">
</head>

<body>
    <?php include(__DIR__ . '/../components/slidebarAdmin.php'); ?>

    <h1>Gestión de Usuarios</h1>
<?php
    if (isset($_SESSION["acierto"])): ?>
        <div class="acierto-message">
            <?php echo $_SESSION["acierto"];
            unset($_SESSION["acierto"]); ?>
        </div>
    <?php endif; ?>
     
    <?php
    if (isset($_SESSION["error"])): ?>
        <div class="error-message">
            <?php echo $_SESSION["error"];
            unset($_SESSION["error"]); ?>
        </div>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php include(__DIR__ . '/../../controller/gestionUsuarios.php'); ?>
        </tbody>
    </table>
    
    <div id="usuario-modal" class="usuario-modal" style="display:none">
        <div class="usuario-modal-content">
            <h3>Editar usuario</h3>
            <form id="usuario-form" class="usuario-form">
                <input type="hidden" name="ID_Usuario" id="f_id">
                <div>
                    <label for="f_nombre">Nombre</label>
                    <input type="text" id="f_nombre" name="nombre" placeholder="Nombre" required>
                </div>
                <div class="form-row">
                    <div>
                        <label for="f_correo">Correo</label>
                        <input type="email" id="f_correo" name="correo" placeholder="correo@dominio.com" required>
                    </div>
                    <div>
                        <label for="f_telefono">Teléfono</label>
                        <input type="text" id="f_telefono" name="telefono" placeholder="Teléfono">
                    </div>
                </div>
                <div>
                    <label for="f_direccion">Dirección</label>
                    <input type="text" id="f_direccion" name="direccion" placeholder="Dirección">
                </div>
                <div class="usuario-modal-actions">
                    <button id="cerrar-modal" type="button" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function(){
        var baseUrl = '<?php echo BASE_URL; ?>';
        var modal = document.getElementById('usuario-modal');
        var form = document.getElementById('usuario-form');
        var cerrar = document.getElementById('cerrar-modal');

        function openModal() { modal.style.display = 'flex'; }
        function closeModal() { modal.style.display = 'none'; form.reset(); }
        cerrar.addEventListener('click', closeModal);
        window.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });

        // Delegación global: funciona aunque se haga click en el <img> dentro del botón
        document.addEventListener('click', function(e){
            var btn = e.target.closest('.btn-editar-icon');
            if (!btn) return;
            var id = btn.getAttribute('data-user-id');
            if (!id) return;
            fetch(baseUrl + '/controller/obtenerUsuario.php?id=' + encodeURIComponent(id))
                .then(function(r){ return r.json(); })
                .then(function(data){
                    if (!data || !data.success) { throw new Error(data && data.msg ? data.msg : 'Error'); }
                    var u = data.usuario;
                    document.getElementById('f_id').value = u.ID_Usuario;
                    document.getElementById('f_nombre').value = u.nombre || '';
                    document.getElementById('f_correo').value = u.correo || '';
                    document.getElementById('f_telefono').value = u.telefono || '';
                    document.getElementById('f_direccion').value = u.direccion || '';
                    openModal();
                })
                .catch(function(err){
                    alert(err && err.message ? err.message : 'No se pudo obtener la información del usuario');
                });
        });

        // Enlace directo: por si algún estilo/JS de terceros detuviera la delegación
        Array.prototype.forEach.call(document.querySelectorAll('.btn-editar-icon'), function(b){
            b.addEventListener('click', function(ev){
                ev.preventDefault();
                ev.stopPropagation();
                var id = b.getAttribute('data-user-id');
                if (!id) return;
                fetch(baseUrl + '/controller/obtenerUsuario.php?id=' + encodeURIComponent(id))
                    .then(function(r){ return r.json(); })
                    .then(function(data){
                        if (!data || !data.success) { throw new Error(data && data.msg ? data.msg : 'Error'); }
                        var u = data.usuario;
                        document.getElementById('f_id').value = u.ID_Usuario;
                        document.getElementById('f_nombre').value = u.nombre || '';
                        document.getElementById('f_correo').value = u.correo || '';
                        document.getElementById('f_telefono').value = u.telefono || '';
                        document.getElementById('f_direccion').value = u.direccion || '';
                        openModal();
                    })
                    .catch(function(err){
                        alert(err && err.message ? err.message : 'No se pudo obtener la información del usuario');
                    });
            });
        });

        form.addEventListener('submit', function(e){
            e.preventDefault();
            var payload = {
                ID_Usuario: document.getElementById('f_id').value,
                nombre: document.getElementById('f_nombre').value,
                correo: document.getElementById('f_correo').value,
                telefono: document.getElementById('f_telefono').value,
                direccion: document.getElementById('f_direccion').value
            };
            fetch(baseUrl + '/controller/actualizarUsuario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(function(r){ return r.json(); })
            .then(function(resp){
                if (!resp.success) throw new Error(resp.msg || 'No se pudo guardar');
                closeModal();
                // Refrescar la página para ver cambios en la tabla
                window.location.reload();
            })
            .catch(function(err){
                alert(err && err.message ? err.message : 'Error al guardar cambios');
            });
        });
    })();
    </script>

</body>

</html>