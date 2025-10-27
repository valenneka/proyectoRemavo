<?php 
require_once(__DIR__ . '/../../config.php'); 
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
    <title>Pizzería Dominico - Registro</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/login.css"> 
</head>

<body>

    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="login-page">
        <div class="login-container">
            <h2>Registro de Usuario</h2>
            <form class="login-box" action="<?= BASE_URL ?>/controller/register.php" method="post">
                <label for="username">Nombre</label>
                <input type="text" id="username" name="username" placeholder="(*)" required>

                <label for="telefono">Teléfono</label>
                <input type="number" id="telefono" name="telefono" placeholder="(*)" required>

                <label for="correo">Correo</label>
                <input type="text" id="correo" name="correo" placeholder="(*)" required>

                <label for="direccion">Direccion</label>
                <input type="text" id="direccion" name="direccion" placeholder="(*)" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="(*)" required>

                <label for="confirm_password">Repetir Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="(*)" required>

                <button type="submit">Registrarse</button>

                <div class="login-links">
                    <a href="<?= BASE_URL ?>/vista/public/login.php">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </form>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/footer.php'); ?>
</body>
</html>