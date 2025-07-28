<?php
require_once __DIR__ . '/../../../config.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Dominico - Perfil</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/login.css">
</head>

<body>

    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="login-page">
        <div class="login-container">
            <h2>Perfil de Usuario</h2>
            <form class="login-box" action="<?= BASE_URL ?>/controller/profile.php" method="post">
                <label for="username">Nombre</label>
                <input type="text" id="username" name="username" placeholder="(*)" required>

                <label for="telefono">Teléfono</label>
                <input type="number" id="telefono" name="telefono" placeholder="(*)" required>

                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" placeholder="(*)" required>

                <button type="submit">Actualizar Perfil</button>

                <div class="login-links">
                    <a href="<?= BASE_URL ?>/src/vista/public/login.php">Volver al inicio</a>
                </div>
            </form>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/footer.php'); ?>
</body>
</html>