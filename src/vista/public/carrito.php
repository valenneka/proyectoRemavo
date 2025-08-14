<?php
require_once __DIR__ . '/../../../config.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Dominico - Login</title>

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
                <span class="back-arrow">‹</span>
                <h1 class="cart-title">Carrito de Compras</h1>
                <span class="items-count">1 Item</span>
            </div>

            <div class="cart-table">
                <div class="table-header">
                    <span>Producto</span>
                    <span>Precio</span>
                    <span>Acciones</span>
                </div>

                <div class="cart-item">
                    <div class="product-info">
                        <img src="/placeholder.svg?height=60&width=60" alt="Margarita" class="product-image">
                        <span class="product-name">Margarita</span>
                    </div>
                    <div class="product-price">$ 350</div>
                    <div class="product-actions">
                        <a><img src="<?= BASE_URL ?>/images/trash.svg" alt="Basura"></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Order Summary -->
        <div class="summary-panel">
            <h2 class="summary-title">Resumen del Pedido</h2>

            <div class="summary-item">
                <span>Margarita x1</span>
                <span class="price">$ 350</span>
            </div>

            <div class="summary-total">
                <span>Coste Total</span>
                <span class="price">$ 365</span>
            </div>


            <?php if (!isset($_SESSION['usuario'])) {
                echo '<a href="' . BASE_URL . '/src/vista/public/login.php" class="continue-btn">Inicia sesión para continuar</a>';
            } else {
                echo '<a href="' . BASE_URL . '/src/vista/public/profile.php" class="continue-btn">Continuar</a>'; 
            } ?>
        </div>
    </div>



    <?php include(__DIR__ . '/../components/footer.php'); ?>
</body>

</html>