<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'ID inválido']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT ID_Usuario, nombre, correo, telefono, direccion, ID_Rol FROM Usuarios WHERE ID_Usuario = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'msg' => 'Usuario no encontrado']);
        exit;
    }
    $usuario = $res->fetch_assoc();
    echo json_encode(['success' => true, 'usuario' => $usuario]);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
    exit;
}
