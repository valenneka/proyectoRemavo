<?php require_once __DIR__ . '/../../../config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/navbar.css">
    <title>Navbar</title>
</head>

<body>
    <header class="navbar">
        <div class="navbar_container">
            <a href="<?= BASE_URL ?>/index.php"><img src="<?= BASE_URL ?>/images/Logo.svg" alt="Logo" class="navbar_logo"></a>
            <ul class="navbar_links">
                <li><a href="<?= BASE_URL ?>/index.php">Inicio</a></li>
                <li><a href="<?= BASE_URL ?>/vista/usuarios/usuarios.php">Tienda</a></li>
                <li><a href="<?= BASE_URL ?>/vista/roles/roles.php">Historia</a></li>
                <?php
                if (isset($_SESSION['usuario']) && ($_SESSION['usuario']['ID_Rol'] == 3 || $_SESSION['usuario']['ID_Rol'] == 2)): ?>
                    <li><a href="<?= BASE_URL ?>/src/vista/admin/panelAdmin.php">Panel Admin</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL ?>/src/vista/public/carrito.php"><img src="<?= BASE_URL ?>/images/carrito.svg" alt="Carrito" class="carrito"></a></li>
                <li><a href="<?= BASE_URL ?>/controller/comprobarLogin.php"><img src="<?= BASE_URL ?>/images/usuario.svg" alt="Usuario"></a></li>
            </ul>
        </div>
    </header>
</body>

</html>