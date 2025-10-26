<?php require_once __DIR__ . '/../../config.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/carrouseles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/modals.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <title>Pizzería Dominico - Tienda</title>
    <style>
        body {
            background: #f5f5f5;
        }
        /* Ocultar elementos de edición en vista pública */
        .editar-familia,
        .etiqueta-editar {
            display: none !important;
        }
        /* Estilos específicos para tienda pública */
        .contenido-tienda {
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
            padding: 30px 60px;
        }
        .titulo-tienda {
            text-align: center;
            margin: 30px 0;
            color: #ff6b35;
            font-size: 2em;
            font-weight: 700;
        }
        .sin-familias {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 1.1em;
        }
    </style>
</head>

<body>
    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="contenido-tienda">
        <h1 class="titulo-tienda">Nuestros Productos</h1>

        <!-- Contenedor para carrouseles de familias -->
        <div id="familiesContainer"></div>

        <!-- Mensaje si no hay familias -->
        <div id="sinFamilias" class="sin-familias" style="display: none;">
            No hay productos disponibles en este momento.
        </div>
    </div>

    <!-- Modal para detalles de la pizza (VIEW ONLY) -->
    <div class="modal" id="modalPizza">
        <div class="contenido-modal">
            <button class="cerrar-modal" onclick="cerrarModal()">×</button>
            <img class="imagen-modal" id="imagenModal" src="" alt="">
            <h2 id="nombreModal"></h2>
            <p class="descripcion-modal" id="descripcionModal"></p>
            <h3 id="precioModal"></h3>
            <button class="boton-comprar" onclick="agregarAlCarritoDesdeModal()">Agregar al Carrito</button>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/footer.php'); ?>

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/src/js/carritoHandler.js"></script>
    <script src="<?= BASE_URL ?>/src/js/tiendaCarrousel.js"></script>

</body>

</html>
