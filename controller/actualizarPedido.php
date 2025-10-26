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
$estadoPedido = $data['estado_pedido'] ?? null;
$notas = $data['notas'] ?? '';
$direccionEntrega = $data['direccion_entrega'] ?? null;

if (!$pedidoId || !$estadoPedido || !$direccionEntrega) {
    echo json_encode(['success' => false, 'msg' => 'Faltan campos obligatorios']);
    exit;
}

try {
    // Verificar que el pedido pertenece al usuario
    $stmt = $conn->prepare("SELECT ID_Usuario FROM pedidos WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'msg' => 'Pedido no encontrado']);
        exit;
    }
    
    $pedido = $result->fetch_assoc();
    if ($pedido['ID_Usuario'] != $idUsuario) {
        echo json_encode(['success' => false, 'msg' => 'No tienes permiso para modificar este pedido']);
        exit;
    }
    
    // Actualizar el pedido
    $stmt2 = $conn->prepare("
        UPDATE pedidos 
        SET estado_pedido = ?, 
            notas = ?, 
            direccion_entrega = ? 
        WHERE ID_Pedido = ?
    ");
    
    $stmt2->bind_param("sssi", $estadoPedido, $notas, $direccionEntrega, $pedidoId);
    
    if ($stmt2->execute()) {
        echo json_encode(['success' => true, 'msg' => 'Pedido actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Error al actualizar el pedido']);
    }
    
    $stmt->close();
    $stmt2->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>

