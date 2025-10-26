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
    <title>Pizzería Dominico - Carrito</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/carrito.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 0 20px;
        }

        .cart-panel {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ff6b35;
        }

        .cart-title {
            font-size: 1.8em;
            color: #333;
            margin: 0;
        }

        .back-arrow {
            font-size: 1.5em;
            cursor: pointer;
            color: #ff6b35;
        }

        .items-count {
            background: #ff6b35;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            gap: 10px;
            padding: 15px;
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .table-header span {
            display: flex;
            align-items: center;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            gap: 10px;
            padding: 15px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .product-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .product-name {
            color: #333;
            font-weight: 500;
            font-size: 15px;
        }

        .product-price {
            text-align: center;
            color: #333;
            font-weight: 500;
            font-size: 15px;
        }

        .product-quantity input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .product-subtotal {
            text-align: center;
            font-weight: 600;
            color: #ff6b35;
            font-size: 16px;
        }

        .product-actions {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-eliminar {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-eliminar img {
            width: 20px;
            height: 20px;
        }

        .summary-panel {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-size: 1.4em;
            margin: 0 0 20px 0;
            color: #333;
            border-bottom: 2px solid #ff6b35;
            padding-bottom: 12px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            color: #333;
            font-size: 15px;
            border-bottom: 1px solid #eee;
        }

        .summary-item span:first-child {
            font-weight: 500;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 20px 0 15px 0;
            font-weight: 700;
            font-size: 1.3em;
            color: #333;
            border-top: 2px solid #ff6b35;
            margin-top: 15px;
        }

        .price {
            color: #ff6b35;
            font-weight: 600;
        }

        .continue-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
            transition: background 0.3s;
        }

        .continue-btn:hover {
            background: #e55a24;
        }

        .btn-vaciar-carrito {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-vaciar-carrito:hover {
            background: #da190b;
        }

        /* ========== MODALS DE CHECKOUT ========== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: slideUp 0.3s ease;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-title {
            font-size: 1.8em;
            color: #333;
            margin: 0 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #ff6b35;
        }

        .checkout-section {
            margin-bottom: 25px;
        }

        .checkout-section h3 {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .resumen-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        .resumen-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .resumen-item-name {
            color: #666;
        }

        .resumen-item-cantidad {
            font-size: 0.9em;
            color: #999;
        }

        .checkout-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: 600;
            font-size: 1.2em;
            border-top: 2px solid #ff6b35;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff6b35;
        }

        .payment-method {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option.selected {
            border-color: #ff6b35;
            background: #fff8f5;
        }

        .payment-option svg {
            flex-shrink: 0;
        }

        .payment-option strong {
            display: block;
            color: #333;
            margin-bottom: 4px;
        }

        .payment-option p {
            color: #666;
            font-size: 0.9em;
            margin: 0;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-cancelar,
        .btn-confirmar {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cancelar {
            background: #f5f5f5;
            color: #666;
        }

        .btn-cancelar:hover {
            background: #e5e5e5;
        }

        .btn-confirmar {
            background: linear-gradient(135deg, #ff6b35 0%, #e55a24 100%);
            color: white;
        }

        .btn-confirmar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
        }

        /* Modal de Confirmación */
        .modal-success {
            text-align: center;
            max-width: 450px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: bold;
        }

        .modal-success h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .modal-success p {
            color: #666;
            margin-bottom: 10px;
        }

        .order-number {
            font-size: 1.1em;
            color: #ff6b35;
        }

        .delivery-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }

        /* Responsive para pantallas medianas */
        @media (max-width: 1366px) {
            .container {
                max-width: 100%;
                grid-template-columns: 1.5fr 1fr;
                gap: 15px;
                padding: 0 15px;
                margin: 15px auto;
            }

            .cart-panel,
            .summary-panel {
                padding: 20px;
            }

            .cart-title {
                font-size: 1.5em;
            }

            .summary-title {
                font-size: 1.2em;
            }
        }

        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }

            .summary-panel {
                position: static;
            }

            .table-header {
                grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            }

            .cart-item {
                grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                padding: 0 10px;
            }

            .summary-panel {
                position: static;
            }

            .table-header,
            .cart-item {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .table-header {
                display: none;
            }

            .cart-item {
                border: 1px solid #eee;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 10px;
            }

            .product-info {
                flex-direction: column;
                text-align: center;
            }

            .product-price,
            .product-quantity,
            .product-subtotal,
            .product-actions {
                text-align: left;
                padding: 5px 0;
            }

            .modal-content {
                padding: 20px;
                width: 95%;
            }

            .modal-title {
                font-size: 1.5em;
            }

            .modal-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .cart-header {
                flex-wrap: wrap;
            }

            .cart-title {
                font-size: 1.3em;
            }

            .items-count {
                width: 100%;
                text-align: center;
                margin-top: 10px;
            }

            .cart-panel,
            .summary-panel {
                padding: 15px;
            }
        }
    </style>
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
                        <label for="telefono">Teléfono de Contacto *</label>
                        <input type="tel" id="telefono" name="telefono" required
                               placeholder="+54 9 11 1234-5678">
                    </div>

                    <div class="form-group">
                        <label for="notas">Notas adicionales (Opcional)</label>
                        <textarea id="notas" name="notas" rows="2"
                                  placeholder="Ej: Timbre roto, llamar por teléfono"></textarea>
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