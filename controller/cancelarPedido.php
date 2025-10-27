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

if (!$data) {
    echo json_encode(['success' => false, 'msg' => 'Datos inválidos']);
    exit;
}

$pedidoId = $data['pedido_id'] ?? null;

if (!$pedidoId) {
    echo json_encode(['success' => false, 'msg' => 'ID de pedido requerido']);
    exit;
}

try {
    // Verificar que el pedido pertenece al usuario y está pendiente
    $stmt = $conn->prepare("SELECT ID_Usuario, estado_pedido FROM pedidos WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'msg' => 'Pedido no encontrado']);
        exit;
    }
    
    $pedido = $result->fetch_assoc();
    
    // Verificar que el pedido pertenece al usuario
    if ($pedido['ID_Usuario'] != $idUsuario) {
        echo json_encode(['success' => false, 'msg' => 'No tienes permiso para cancelar este pedido']);
        exit;
    }
    
    // Verificar que el pedido está pendiente
    if ($pedido['estado_pedido'] !== 'Pendiente') {
        echo json_encode(['success' => false, 'msg' => 'Solo se pueden cancelar pedidos pendientes']);
        exit;
    }
    
    // Actualizar el estado del pedido a "Cancelado"
    $stmt2 = $conn->prepare("UPDATE pedidos SET estado_pedido = 'Cancelado' WHERE ID_Pedido = ?");
    $stmt2->bind_param("i", $pedidoId);
    
    if ($stmt2->execute()) {
        echo json_encode(['success' => true, 'msg' => 'Pedido cancelado correctamente']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Error al cancelar el pedido']);
    }
    
    $stmt->close();
    $stmt2->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
