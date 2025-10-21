<?php
// Configurar manejo de errores personalizado
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Establecer header JSON de inmediato
header('Content-Type: application/json; charset=utf-8');

// Configurar manejador para fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        echo json_encode(['success' => false, 'msg' => 'Fatal Error: ' . $error['message']]);
        exit;
    }
});

// Manejador de errores
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $errstr]);
    exit;
});

require_once __DIR__ . '/conexionDB.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // listar familias
    $sql = "SELECT ID_Familia, nombre, descripcion FROM familia_productos";
    $result = $conn->query($sql);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode(['success' => true, 'familias' => $items]);
    exit;
}

if ($method === 'POST') {
    // crear o actualizar
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['success'=>false,'msg'=>'No input']); exit; }

    $action = isset($input['action']) ? $conn->real_escape_string($input['action']) : '';

    if ($action === 'create') {
        $nombre = $conn->real_escape_string($input['nombre'] ?? '');
        $descripcion = $conn->real_escape_string($input['descripcion'] ?? '');
        if (!$nombre) { echo json_encode(['success'=>false,'msg'=>'Nombre requerido']); exit; }
        $sql = "INSERT INTO familia_productos (nombre, descripcion) VALUES ('{$nombre}', '{$descripcion}')";
        if ($conn->query($sql)) {
            $id = $conn->insert_id;
            echo json_encode(['success'=>true,'id'=>$id]);
        } else {
            echo json_encode(['success'=>false,'msg'=>$conn->error]);
        }
        exit;
    }

    if ($action === 'update') {
        $id = intval($input['id'] ?? 0);
        $nombre = $conn->real_escape_string($input['nombre'] ?? '');
        $descripcion = $conn->real_escape_string($input['descripcion'] ?? '');
        if (!$id || !$nombre) { echo json_encode(['success'=>false,'msg'=>'Id y nombre requeridos']); exit; }
        $sql = "UPDATE familia_productos SET nombre='{$nombre}', descripcion='{$descripcion}' WHERE ID_Familia={$id}";
        if ($conn->query($sql)) {
            echo json_encode(['success'=>true]);
        } else {
            echo json_encode(['success'=>false,'msg'=>$conn->error]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        if (!$id) { echo json_encode(['success'=>false,'msg'=>'Id requerido']); exit; }
        $sql = "DELETE FROM familia_productos WHERE ID_Familia={$id}";
        if ($conn->query($sql)) {
            echo json_encode(['success'=>true]);
        } else {
            echo json_encode(['success'=>false,'msg'=>$conn->error]);
        }
        exit;
    }
}

echo json_encode(['success'=>false,'msg'=>'Método no soportado']);

?>