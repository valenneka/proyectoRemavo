<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/vista/public/login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
    <title>Pizzería Dominico - Perfil</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/profile.css">
</head>

<body>

    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="profile-page">
        <div class="profile-container">
            <h2>Bienvenido a tu Perfil <?php echo htmlspecialchars($usuario["nombre"]); ?></h2>
            
            <div class="profile-box">
                <!-- Información del Usuario -->
                <div class="user-info-section">
                    <div class="info-item">
                        <label>Correo electrónico:</label>
                        <span><?php echo htmlspecialchars($usuario["correo"]); ?></span>
                    </div>

                    <div class="info-item">
                        <label>Teléfono:</label>
                        <span><?php echo htmlspecialchars($usuario["telefono"]); ?></span>
                    </div>

                    <div class="info-item editable">
                        <label>Dirección:</label>
                        <div class="direccion-container">
                            <input type="text" 
                                   id="direccionInput" 
                                   value="<?php echo htmlspecialchars($usuario["direccion"]); ?>" 
                                   class="direccion-input"
                                   readonly>
                            <button type="button" class="edit-direccion-btn" onclick="editarDireccion()" title="Editar dirección">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="button" id="btnGuardarDireccion" class="btn-guardar-direccion" style="display: none;" onclick="guardarDireccion()">
                        Guardar Dirección
                    </button>
                </div>

                <!-- Último Pedido -->
                <div class="order-card" id="orderCard">
                    <h3 class="order-header">Último pedido:</h3>
                    <p class="loading-text">Cargando pedido...</p>
                </div>

                <!-- Botón Salir -->
                <a href="<?= BASE_URL ?>/controller/salirCuenta.php" class="salirCuenta">Salir de la cuenta</a>
            </div>
        </div>
    </div>

    <!-- Modal para Modificar Pedido -->
    <div class="modal-overlay" id="modalModificarPedido">
        <div class="modal-content-pedido">
            <button class="modal-close" onclick="cerrarModalPedido()">×</button>
            <h2 class="modal-title">Modificar Pedido</h2>

            <form id="formModificarPedido" onsubmit="guardarCambiosPedido(event)">
                <input type="hidden" id="pedidoId" name="pedido_id">

                <div class="form-group">
                    <label>Estado del Pedido:</label>
                    <input type="text" id="estadoPedido" class="form-control" readonly>
                    <small class="text-muted">Solo el administrador puede cambiar el estado</small>
                </div>

                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" 
                           id="direccionPedido" 
                           name="direccion" 
                           class="form-control" 
                           placeholder="Dirección de entrega">
                </div>

                <div class="pedido-items">
                    <h4>Productos del Pedido</h4>
                    <p class="text-muted">Puedes modificar las cantidades o eliminar productos</p>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Comida</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pedidoItemsTable">
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                    <button type="button" class="btn-agregar-producto" onclick="agregarProducto()">
                        + Agregar Producto
                    </button>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalPedido()">Cancelar</button>
                    <button type="submit" class="btn-guardar-pedido">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/footer.php'); ?>

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/src/js/profileHandler.js"></script>
</body>

</html>
