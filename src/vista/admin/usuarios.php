<?php require_once __DIR__ . '/../../../config.php';
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['ID_Rol'] != 3) {
    header("Location: " . BASE_URL . "/src/vista/public/error.php");
    exit;
}else {
    header("Location: " . BASE_URL . "/src/vista/admin/usuarios.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuarios</title>
</head>

<body>
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