<?php
require_once('../config.php');
include('conexionDB.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $rol = 1;

    // Validar campos requeridos
    if (empty($username) || empty($telefono) || empty($correo) || empty($password) || empty($confirm_password)) {
        die("Todos los campos son obligatorios.");
    }

    // Validar formato del correo y longitud
    if (strlen($correo) < 5 || strlen($correo) > 254) {
        die("El correo debe tener entre 5 y 254 caracteres.");
    }
    // Verificar que tenga @
    if (strpos($correo, '@') === false) {
        die("El correo debe contener el símbolo @.");
    }
    // Validar formato completo del email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico no válido. Debe tener un formato válido (ejemplo: usuario@gmail.com).");
    }
    // Validar que tenga un dominio válido después del @
    $partes = explode('@', $correo);
    if (count($partes) !== 2) {
        die("El correo debe tener exactamente un símbolo @.");
    }
    $dominio = trim($partes[1]);
    if (empty($dominio)) {
        die("El correo debe tener un dominio después del @ (ejemplo: gmail.com).");
    }
    // Verificar que el dominio tenga al menos un punto (ej: gmail.com, hotmail.com)
    if (strpos($dominio, '.') === false) {
        die("El dominio debe tener un formato válido (ejemplo: usuario@gmail.com).");
    }
    // Verificar que el dominio no termine en punto
    if (substr($dominio, -1) === '.') {
        die("El dominio no puede terminar en punto.");
    }

    // Validar que las contraseñas coincidan

    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden.");
    }

    // Verificar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT ID_Usuario FROM Usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("El correo ya está registrado.");
    }
    $stmt->close();

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insertar el nuevo usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO Usuarios (nombre, telefono, correo, direccion, contraseña, ID_Rol) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssi", $username, $telefono, $correo, $direccion, $hashed_password, $rol);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/vista/public/login.php");
        exit();
    } else {
        die("Error al registrar el usuario: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
