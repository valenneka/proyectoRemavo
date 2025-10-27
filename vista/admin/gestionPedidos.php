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
    <style>
        .pedidos-container {
            margin-left: 250px;
            padding: 20px;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        .pedidos-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .pedidos-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .estado-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-en-proceso { background: #d1ecf1; color: #0c5460; }
        .estado-en-camino { background: #d4edda; color: #155724; }
        .estado-entregado { background: #d1ecf1; color: #0c5460; }
        .estado-cancelado { background: #f8d7da; color: #721c24; }
        .estado-sin-estado { background: #f8f9fa; color: #6c757d; }
        
        .btn-accion {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
        }
        
        .btn-ver { background: #007bff; color: white; }
        .btn-editar { background: #28a745; color: white; }
        .btn-eliminar { background: #dc3545; color: white; }
        
        .btn-accion:hover {
            opacity: 0.8;
        }
        
        .estado-select {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 12px;
            cursor: pointer;
        }
        
        .estado-select:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .sin-pedidos {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .acierto-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin: 16px 0;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin: 16px 0;
        }
        
        /* Estilos para modal de detalles */
        .pedido-detalles {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .detalles-section {
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .detalles-section h4 {
            margin: 0 0 10px 0;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-item label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .info-item span {
            color: #333;
            font-size: 16px;
        }
        
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .productos-table th,
        .productos-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .productos-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .modal-actions {
            text-align: center;
            margin-top: 15px;
        }
        
        .btn-cerrar {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-cerrar:hover {
            background: #5a6268;
        }
        
        /* Modal positioning */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            position: relative;
            background-color: white;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #000;
        }
    </style>
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
