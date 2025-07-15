<?php require_once __DIR__ . '/../../../config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/footer.css">
</head>
<body>
    <footer class="footer">
        <div class="footer_container">
            <div class="footer_branding">
                <img src="<?= BASE_URL ?>/images/Logo.svg" alt="Logo Pizzería">
                <span>&copy; 2025 Pizzería Dominico</span>
            </div>
            <div class="footer_social">
                <a href="#"><img src="<?= BASE_URL ?>/images/instagram.svg" alt="Instagram"></a>
                <a href="#"><img src="<?= BASE_URL ?>/images/facebook.svg" alt="Facebook"></a>
            </div>
        </div>
    </footer>
</body>
</html>