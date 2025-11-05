<?php
require_once(__DIR__ . '/../../config.php');
if (isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/src/vista/public/perfil.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
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

                <label for="correo" class="required">Correo</label>
                <input type="email" id="correo" name="correo" placeholder="usuario@gmail.com" minlength="5" maxlength="254" required>

                <label for="password" class="required">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="********" required>

                <button type="submit">Entrar</button>

                <div class="login-links">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <a href="<?= BASE_URL ?>/vista/public/registro.php">Regístrate aquí</a>
                </div>
            </form>
        </div>
    </div>
    <?php include(__DIR__ . '/../components/footer.php'); ?>
</body>

</html>