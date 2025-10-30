<?php
require_once __DIR__ . '/../../config.php';

// Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/vista/public/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
    <title>Pizzería Dominico - Carrito</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/carrito.css">
</head>

<body>

    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="container">
        <!-- Left Panel - Shopping Cart -->
        <div class="cart-panel">
            <div class="cart-header">
                <span class="back-arrow" onclick="window.history.back()">‹</span>
                <h1 class="cart-title">Mi Carrito</h1>
                <span class="items-count" id="cartItemsCount">0 Items</span>
            </div>

            <div class="cart-table">
                <!-- Los items del carrito se cargarán aquí mediante JavaScript -->
            </div>
        </div>

        <!-- Right Panel - Order Summary -->
        <div class="summary-panel">
            <!-- El resumen se cargarán aquí mediante JavaScript -->
        </div>
    </div>

    <!-- Modal de Checkout -->
    <div class="modal-overlay" id="checkoutModal">
        <div class="modal-content">
            <button class="modal-close" onclick="cerrarCheckout()">×</button>
            <h2 class="modal-title">Finalizar Pedido</h2>

            <form id="checkoutForm" onsubmit="procesarPedido(event)">
                <!-- Resumen del pedido -->
                <div class="checkout-section">
                    <h3>Resumen del Pedido</h3>
                    <div id="resumenPedido" class="resumen-items"></div>
                    <div class="checkout-total">
                        <span>Total:</span>
                        <span id="totalCheckout" class="price">$0</span>
                    </div>
                </div>

                <!-- Información de entrega -->
                <div class="checkout-section">
                    <h3>Información de Entrega</h3>

                    <div class="form-group">
                        <label for="direccion">Dirección de Entrega *</label>
                        <textarea id="direccion" name="direccion" rows="3" required
                                  placeholder="Calle, número, piso, departamento"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono de Contacto</label>
                        <input type="tel" id="telefono" name="telefono"
                               placeholder="+54 9 11 1234-5678">
                    </div>
                </div>

                <!-- Método de pago -->
                <div class="checkout-section">
                    <h3>Método de Pago</h3>
                    <div class="payment-method">
                        <div class="payment-option selected" onclick="seleccionarMetodoPago('efectivo', this)">
                            <input type="radio" name="metodo_pago" value="Efectivo" checked style="display: none;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                            </svg>
                            <div>
                                <strong>Efectivo</strong>
                                <p>Pago contra entrega en efectivo</p>
                            </div>
                        </div>

                        <div class="payment-option" onclick="seleccionarMetodoPago('pos', this)">
                            <input type="radio" name="metodo_pago" value="POS (Tarjeta)" style="display: none;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                            </svg>
                            <div>
                                <strong>POS (Tarjeta)</strong>
                                <p>El repartidor lleva el POS para pagar con tarjeta</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="modal-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarCheckout()">Cancelar</button>
                    <button type="submit" class="btn-confirmar">Confirmar Pedido</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal-overlay" id="confirmacionModal">
        <div class="modal-content modal-success">
            <div class="success-icon">✓</div>
            <h2>¡Pedido Realizado!</h2>
            <p>Tu pedido ha sido procesado exitosamente.</p>
            <p class="order-number">Número de pedido: <strong id="numeroPedido"></strong></p>
            <p class="delivery-info">Recibirás tu pedido en aproximadamente 30-45 minutos.</p>
            <button class="btn-confirmar" onclick="cerrarConfirmacion()">Entendido</button>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/footer.php'); ?>

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script>window.usuarioData = <?= json_encode($_SESSION['usuario'] ?? []) ?>;</script>
    <script src="<?= BASE_URL ?>/src/js/carritoHandler.js"></script>
    <script src="<?= BASE_URL ?>/src/js/checkoutHandler.js"></script>

    <script>
        // Actualizar el contador de items cuando carga la página
        async function actualizarContador() {
            const cart = await obtenerCarrito();
            if (cart) {
                const count = cart.cantidad_items;
                const text = count === 1 ? '1 Item' : count + ' Items';
                document.getElementById('cartItemsCount').textContent = text;
            }
        }

        document.addEventListener('DOMContentLoaded', actualizarContador);
    </script>
</body>

</html>