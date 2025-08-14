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
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/login.css">
</head>

<body>

    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="login-page">
        <div class="login-container">
            <h2>Inicio de Sesión</h2>
            <form class="login-box" action="<?= BASE_URL ?>/controller/login.php" method="post">

                <?php
                if (isset($_SESSION["error"])): ?>
                    <div class="error-message">
                        <?php echo $_SESSION["error"];
                        unset($_SESSION["error"]); ?>
                    </div>
                <?php endif; ?>

                <label for="correo">Correo</label>
                <input type="text" id="correo" name="correo" placeholder="(*)" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="(*)" required>

                <button type="submit">Entrar</button>

                <div class="login-links">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <a href="<?= BASE_URL ?>/src/vista/public/registro.php">Regístrate aquí</a>
                </div>
            </form>
        </div>
    </div>
    <?php include(__DIR__ . '/../components/footer.php'); ?>
</body>

</html>