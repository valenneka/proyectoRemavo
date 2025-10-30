<?php require_once __DIR__ . '/../../config.php';
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    header("Location: " . BASE_URL . "/vista/public/error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
    <title>Pizzería Dominico - Gestión Pedidos</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/panelAdmin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/slidebarAdmin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/gestionPedidos.css">
</head>

<body>
    <?php include(__DIR__ . '/../components/slidebarAdmin.php'); ?>
    
    <div class="pedidos-container">
        <div class="pedidos-header">
            <h1>Gestión de Pedidos</h1>
            <p>Administra el estado de los pedidos de los clientes</p>
        </div>
        
        <?php if (isset($_SESSION["acierto"])): ?>
            <div class="acierto-message">
                <?php echo $_SESSION["acierto"];
                unset($_SESSION["acierto"]); ?>
            </div>
        <?php endif; ?>
         
        <?php if (isset($_SESSION["error"])): ?>
            <div class="error-message">
                <?php echo $_SESSION["error"];
                unset($_SESSION["error"]); ?>
            </div>
        <?php endif; ?>
        
        <div class="pedidos-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="pedidosTable">
                    <!-- Los pedidos se cargarán aquí -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para ver/editar pedido -->
    <div id="modalPedido" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Detalles del Pedido</h2>
            <div id="modalContent">
                <!-- Contenido del modal se cargará aquí -->
            </div>
        </div>
    </div>

    <script>
        // Cargar pedidos al cargar la página
        document.addEventListener('DOMContentLoaded', cargarPedidos);
        
        async function cargarPedidos() {
            try {
                console.log('Cargando pedidos...');
                const response = await fetch('<?= BASE_URL ?>/controller/obtenerPedidos.php');
                const data = await response.json();
                
                console.log('Respuesta del servidor:', data);
                
                if (data.success) {
                    console.log('Pedidos cargados:', data.pedidos);
                    renderizarPedidos(data.pedidos);
                } else {
                    console.error('Error del servidor:', data.msg);
                    document.getElementById('pedidosTable').innerHTML = 
                        '<tr><td colspan="6" class="sin-pedidos">Error: ' + data.msg + '</td></tr>';
                }
            } catch (error) {
                console.error('Error cargando pedidos:', error);
                document.getElementById('pedidosTable').innerHTML = 
                    '<tr><td colspan="6" class="sin-pedidos">Error al cargar los pedidos: ' + error.message + '</td></tr>';
            }
        }
        
        function renderizarPedidos(pedidos) {
            const tbody = document.getElementById('pedidosTable');
            
            if (pedidos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="sin-pedidos">No hay pedidos disponibles</td></tr>';
                return;
            }
            
            tbody.innerHTML = pedidos.map(pedido => {
                const estado = pedido.estado_pedido || 'Sin estado';
                const estadoClass = estado === 'Sin estado' ? 'estado-sin-estado' : `estado-${estado.toLowerCase().replace(' ', '-')}`;
                
                return `
                <tr>
                    <td>${pedido.ID_Pedido}</td>
                    <td>${pedido.nombre_cliente || 'Sin nombre'}</td>
                    <td>${formatearFecha(pedido.fecha_pedido)}</td>
                    <td>
                        <form method="POST" action="<?= BASE_URL ?>/controller/actualizarEstadoPedido.php" onsubmit="return confirmarCambioEstado(this)">
                            <input type="hidden" name="pedido_id" value="${pedido.ID_Pedido}">
                            <select name="estado" onchange="this.form.submit()" class="estado-select">
                                <option value="Pendiente" ${estado === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="En proceso" ${estado === 'En proceso' ? 'selected' : ''}>En proceso</option>
                                <option value="En camino" ${estado === 'En camino' ? 'selected' : ''}>En camino</option>
                                <option value="Entregado" ${estado === 'Entregado' ? 'selected' : ''}>Entregado</option>
                                <option value="Cancelado" ${estado === 'Cancelado' ? 'selected' : ''}>Cancelado</option>
                            </select>
                        </form>
                    </td>
                    <td>${pedido.direccion_entrega || 'Sin dirección'}</td>
                    <td>
                        <button class="btn-accion btn-ver" onclick="verPedido(${pedido.ID_Pedido})">Ver</button>
                    </td>
                </tr>
            `;
            }).join('');
        }
        
        function formatearFecha(fecha) {
            if (!fecha || fecha === 'null' || fecha === null) return 'Sin fecha';
            try {
                return new Date(fecha).toLocaleDateString('es-ES');
            } catch (e) {
                return 'Fecha inválida';
            }
        }
        
        async function verPedido(id) {
            console.log('Intentando ver pedido ID:', id);
            try {
                const url = `<?= BASE_URL ?>/controller/obtenerDetallesPedido.php?id=${id}`;
                console.log('URL:', url);
                
                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Data recibida:', data);
                
                if (data.success) {
                    mostrarModalDetalles(data.pedido);
                } else {
                    alert('Error: ' + data.msg);
                }
            } catch (error) {
                console.error('Error completo:', error);
                alert('Error al cargar los detalles del pedido: ' + error.message);
            }
        }
        
        function confirmarCambioEstado(form) {
            const select = form.querySelector('select[name="estado"]');
            const nuevoEstado = select.value;
            const pedidoId = form.querySelector('input[name="pedido_id"]').value;
            
            return confirm(`¿Estás seguro de cambiar el estado del pedido #${pedidoId} a "${nuevoEstado}"?`);
        }
        
        function cerrarModal() {
            document.getElementById('modalPedido').style.display = 'none';
        }
        
        function mostrarModalDetalles(pedido) {
            const modal = document.getElementById('modalPedido');
            const modalContent = document.getElementById('modalContent');
            
            // Scroll automático al modal
            modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            modalContent.innerHTML = `
                <div class="pedido-detalles">
                    <h3>Detalles del Pedido #${pedido.ID_Pedido}</h3>
                    
                    <div class="detalles-section">
                        <h4>Información del Cliente</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Nombre:</label>
                                <span>${pedido.nombre_cliente}</span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span>${pedido.correo_cliente}</span>
                            </div>
                            <div class="info-item">
                                <label>Teléfono:</label>
                                <span>${pedido.telefono_cliente || 'No disponible'}</span>
                            </div>
                            <div class="info-item">
                                <label>Dirección:</label>
                                <span>${pedido.direccion_cliente || 'No disponible'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detalles-section">
                        <h4>Información del Pedido</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Estado:</label>
                                <span class="estado-badge estado-${pedido.estado_pedido.toLowerCase().replace(' ', '-')}">${pedido.estado_pedido}</span>
                            </div>
                            <div class="info-item">
                                <label>Fecha:</label>
                                <span>${formatearFecha(pedido.fecha_pedido)}</span>
                            </div>
                            <div class="info-item">
                                <label>Dirección de Entrega:</label>
                                <span>${pedido.direccion_entrega}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detalles-section">
                        <h4>Productos del Pedido</h4>
                        <table class="productos-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${(pedido.productos || []).map(prod => `
                                    <tr>
                                        <td>${prod.nombre_producto}</td>
                                        <td>${prod.cantidad}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="modal-actions">
                        <button class="btn-cerrar" onclick="cerrarModal()">Cerrar</button>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
            
            // Scroll adicional después de mostrar el contenido
            setTimeout(() => {
                modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    </script>
</body>
</html>
