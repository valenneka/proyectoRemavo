<?php
require_once('../config.php');
include('conexionDB.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $rol = 1;

    // Validar campos requeridos
    if (empty($username) || empty($telefono) || empty($correo) || empty($password) || empty($confirm_password)) {
        die("Todos los campos son obligatorios.");
    }

    // Validar formato del correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico no válido.");
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
        header("Location: " . BASE_URL . "/src/vista/public/login.php");
        exit();
    } else {
        die("Error al registrar el usuario: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
