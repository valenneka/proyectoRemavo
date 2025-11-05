<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación de administrador
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

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
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Eliminar productos del pedido (tabla contiene)
    $stmt = $conn->prepare("DELETE FROM contiene WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $stmt->close();
    
    // Eliminar el pedido
    $stmt2 = $conn->prepare("DELETE FROM pedidos WHERE ID_Pedido = ?");
    $stmt2->bind_param("i", $pedidoId);
    
    if ($stmt2->execute()) {
        $conn->commit();
        echo json_encode(['success' => true, 'msg' => 'Pedido eliminado correctamente']);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'msg' => 'Error al eliminar el pedido']);
    }
    
    $stmt2->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>

