<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Dominico - Error</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/error.css">
</head>

<body>

    <div class="error-container">
        <img src="<?= BASE_URL ?>/images/Logo.svg" alt="Logo Pizzería">
        <h1>Acceso Denegado</h1>
        <p>No tienes permisos para acceder a esta página.</p>
        <a href="<?= BASE_URL ?>/index.php">Volver al inicio</a>
    </div>
</body>

</html>