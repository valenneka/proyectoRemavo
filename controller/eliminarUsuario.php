<?php
require_once __DIR__ . '/../config.php';
include('conexionDB.php');

// Verificar que se haya enviado el ID
if (isset($_POST['idUsuario']) && is_numeric($_POST['idUsuario'])) {
    $id = intval($_POST['idUsuario']);

    // Preparar la consulta para evitar inyecciones SQL
    $stmt = $conn->prepare("DELETE FROM Usuarios WHERE ID_Usuario = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Si se elimina correctamente, redirigir a la lista de usuarios
        header("Location: " . BASE_URL . "/vista/admin/usuarios.php");
        exit();
    } else {
        $_SESSION["error"] = "Error al eliminar el usuario: " .  $conn->error;
    }

    $stmt->close();
} else {
    $_SESSION["error"] = "ID inválido.";
}

$conn->close();
