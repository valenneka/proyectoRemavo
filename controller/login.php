<?php
require_once('../config.php');
include('conexionDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Validar los datos del formulario
    if (empty($correo) || empty($password)) {
        $_SESSION["error"] = "Por favor, completa todos los campos.";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    
    // Validar formato del correo y longitud
    $correo = trim($correo);
    if (strlen($correo) < 5 || strlen($correo) > 254) {
        $_SESSION["error"] = "El correo debe tener entre 5 y 254 caracteres.";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    // Verificar que tenga @
    if (strpos($correo, '@') === false) {
        $_SESSION["error"] = "El correo debe contener el símbolo @.";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    // Validar formato completo del email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Correo electrónico no válido. Debe tener un formato válido (ejemplo: usuario@gmail.com).";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    // Validar que tenga un dominio válido después del @
    $partes = explode('@', $correo);
    if (count($partes) !== 2) {
        $_SESSION["error"] = "El correo debe tener exactamente un símbolo @.";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    $dominio = trim($partes[1]);
    if (empty($dominio)) {
        $_SESSION["error"] = "El correo debe tener un dominio después del @ (ejemplo: gmail.com).";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    // Verificar que el dominio tenga al menos un punto (ej: gmail.com, hotmail.com)
    if (strpos($dominio, '.') === false) {
        $_SESSION["error"] = "El dominio debe tener un formato válido (ejemplo: usuario@gmail.com).";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
    }
    // Verificar que el dominio no termine en punto
    if (substr($dominio, -1) === '.') {
        $_SESSION["error"] = "El dominio no puede terminar en punto.";
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit;
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
            header("Location: " . BASE_URL . "/vista/public/perfil.php");
            exit;
        } else {
           $_SESSION["error"] = "Correo o contraseña incorrectos.";
        }
    } else {
        $_SESSION["error"] = "Correo o contraseña incorrectos.";
    }
    header("Location: " . BASE_URL . "/vista/public/login.php");
    
    $stmt->close();
    $conn->close();
}
