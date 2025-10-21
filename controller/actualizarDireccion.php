<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

$idUsuario = $_SESSION['usuario']['ID_Usuario'];

// Obtener datos JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['direccion'])) {
    echo json_encode(['success' => false, 'msg' => 'Dirección no proporcionada']);
    exit;
}

$nuevaDireccion = trim($data['direccion']);

if (empty($nuevaDireccion)) {
    echo json_encode(['success' => false, 'msg' => 'La dirección no puede estar vacía']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE usuarios SET direccion = ? WHERE ID_Usuario = ?");
    $stmt->bind_param("si", $nuevaDireccion, $idUsuario);
    
    if ($stmt->execute()) {
        // Actualizar la sesión
        $_SESSION['usuario']['direccion'] = $nuevaDireccion;
        
        echo json_encode(['success' => true, 'msg' => 'Dirección actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Error al actualizar la dirección']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>

