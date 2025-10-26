<?php require_once __DIR__ . '/../../config.php';
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    header("Location: " . BASE_URL . "/vista/public/error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Dominico - Panel Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/panelAdmin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/slidebarAdmin.css">
</head>

<body>
    <?php include(__DIR__ . '/../components/slidebarAdmin.php'); ?>
</body>
</html>