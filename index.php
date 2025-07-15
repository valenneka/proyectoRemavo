<?php require_once __DIR__ . '/config.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Dominico</title>
    <link rel="stylesheet" href="src/css/footer.css">
    <link rel="stylesheet" href="src/css/navbar.css">
    <link rel="stylesheet" href="src/css/landing.css">
</head>

<body>
    <?php include('src/vista/components/navbar.php'); ?>

    <section class="promo" id="inicio">
        <div class="container">
            <h1 class="titulo">Caseras,<br> Con una pizca extra<br> De <span class="amor">Amor</span></h1>
            <p>Perfección de sabor en una corteza crujiente: una sinfonía entre el queso y los aderezos.</p>
            <a href="" class="ordenarBtn">Ordenar ahora</a>
        </div>
        <div>
            <div class="imagen-pizza">
                <div class="circulo-naranja"></div>
                <img src="images/Pizza.svg" alt="Pizza">
            </div>
    </section>

    <section class="pizza-banner">
        <div class="pizza-img">
            <img src="images/Pizza2.svg" alt="Pizza deliciosa">
        </div>
        <div class="pizza-texto">
            <h2><strong>Frescas y siempre sabrosas</strong></h2>
            <p>Disfrute de la deliciosa armonía de sabores con nuestras pizzas de ingredientes frescos y sabrosos.</p>
        </div>
    </section>

    <?php include('src/vista/components/footer.php'); ?>
</body>

</html>