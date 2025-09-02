<?php require_once __DIR__ . '/../../../config.php';

if (
    !isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 2 && $_SESSION['usuario']['ID_Rol'] != 3)
) {
    header("Location: " . BASE_URL . "/src/vista/public/error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/slidebarAdmin.css">
</head>

<body>
    <div class="sidebar">
        <div class="logo-section">
            <div class="logo">
                <a href="<?= BASE_URL ?>/index.php">
                    <img src="<?= BASE_URL ?>/images/Logo.svg" alt="Logo" class="section_logo">
                </a>
            </div>
        </div>


        <nav class="nav-menu">
            <ul class="menu-list">
                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/src/vista/admin/panelAdmin.php" class="menu-link">
                        <span class="menu-text">Inicio</span>
                    </a>
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['ID_Rol'] == 3): ?>
                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/src/vista/admin/usuarios.php" class="menu-link">
                        <span class="menu-text">Gestión Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>
            </li>
            <li class="menu-item">
                <a href="<?= BASE_URL ?>/src/vista/admin/gestionPedidos.php" class="menu-link">
                    <span class="menu-text">Gestión Pedidos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="<?= BASE_URL ?>/src/vista/admin/gestionMenus.php" class="menu-link">
                    <span class="menu-text">Gestión de menús</span>
                </a>
            </li>
            </ul>
        </nav>
    </div>
</body>

</html>