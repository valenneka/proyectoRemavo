<?php
require_once __DIR__ . '/../../../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/src/vista/public/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Dominico - Perfil</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/profile.css">
</head>

<body>

    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <div class="profile-page">
        <div class="profile-container">
            <h2>Bienvenido a tu perfil <?php echo $_SESSION["usuario"]["nombre"]; ?></h2>
            <form class="profile-box" action="<?= BASE_URL ?>/controller/profile.php" method="post">
                <label for="correo">Correo electrónico: <?php echo $_SESSION["usuario"]["correo"]; ?></label>

                <label for="telefono">Teléfono: <?php echo $_SESSION["usuario"]["telefono"]; ?></label>

                <label for="telefono">Dirección: <?php echo $_SESSION["usuario"]["direccion"]; ?></label>

                <div class="order-card">
                    <h1 class="order-header">Último pedido:</h1>

                    <div class="order-section">
                        <div class="section-header">
                                <span class="section-title">Comida</span>
                                <span class="status-badge">En proceso</span>
                                <svg class="edit-svg" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                </svg>
                        </div>

                        <ul class="menu-items">
                            <li class="menu-item">Milanesa Napolitana</li>
                            <li class="menu-item">Milanesa Napolitana</li>
                        </ul>
                    </div>
                </div>

                <div class="">
                    <a href="<?= BASE_URL ?>/src/vista/public/login.php">Volver al inicio</a>
                </div>
            </form>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/footer.php'); ?>
</body>

</html>