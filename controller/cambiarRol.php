<?php
require_once __DIR__ . '/../config.php';
include('./conexionDB.php');

if (isset($_POST['idUsuario'], $_POST['rol'])) {
    $idUsuario = intval($_POST['idUsuario']);
    $nuevoRol = intval($_POST['rol']);

    $stmt = $conn->prepare("UPDATE Usuarios SET ID_Rol = ? WHERE ID_Usuario = ?");
    $stmt->bind_param("ii", $nuevoRol, $idUsuario);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/src/vista/admin/usuarios.php");
        $_SESSION["acierto"] = "Rol actualizado correctamente.";
        exit;
    } else {
        $_SESSION["error"] = "Error al actualizar el rol.";
    }
} else {
    $_SESSION["error"] = "Datos incompletos.";
}