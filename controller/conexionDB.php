<?php
$host = "localhost";
$user = "root"; // o el usuario que uses
$password = ""; // o tu contraseña
$database = "pizzeriaDominico";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>