<?php
require_once('../config.php');
include('conexionDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Validar los datos del formulario
    if (empty($correo) || empty($password)) {
        $_SESSION["error"] = "Por favor, completa todos los campos.";
    }

    // Buscar usuario por correo
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        // Verificar contraseña
        if (password_verify($password, $usuario['contraseña'])) {
            unset($usuario['contraseña']);
            $_SESSION['usuario'] = $usuario;
            header("Location: " . BASE_URL . "/src/vista/public/perfil.php");
            exit;
        } else {
           $_SESSION["error"] = "Correo o contraseña incorrectos.";
        }
    } else {
        $_SESSION["error"] = "Correo o contraseña incorrectos.";
    }
    header("Location: " . BASE_URL . "/src/vista/public/login.php");
    
    $stmt->close();
    $conn->close();
}
