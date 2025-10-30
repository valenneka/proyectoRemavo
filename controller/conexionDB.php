<?php
$host = "localhost";
$user = "root"; // o el usuario que uses
$password = ""; // o tu contraseña
$database = "pizzeriaDominico";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    $headers = headers_list();
    $hasJsonHeader = false;
    foreach ($headers as $header) {
        if (stripos($header, 'Content-Type:') === 0 && stripos($header, 'application/json') !== false) {
            $hasJsonHeader = true;
            break;
        }
    }
    // Si no tiene header JSON, establecerlo
    if (!$hasJsonHeader) {
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode(['success' => false, 'msg' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit;
}
?>