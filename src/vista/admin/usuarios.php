<?php require_once __DIR__ . '/../../../config.php';
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['ID_Rol'] != 3) {
    header("Location: " . BASE_URL . "/src/vista/public/error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuarios</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/src/css/usuarios.css">
</head>

<body>
    <?php include(__DIR__ . '/../components/slidebarAdmin.php'); ?>

    <h1>Gestión de Usuarios</h1>
<?php
    if (isset($_SESSION["acierto"])): ?>
        <div class="acierto-message">
            <?php echo $_SESSION["acierto"];
            unset($_SESSION["acierto"]); ?>
        </div>
    <?php endif; ?>
     
    <?php
    if (isset($_SESSION["error"])): ?>
        <div class="error-message">
            <?php echo $_SESSION["error"];
            unset($_SESSION["error"]); ?>
        </div>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php include(__DIR__ . '/../../../controller/gestionUsuarios.php'); ?>
        </tbody>
    </table>
</body>

</html>