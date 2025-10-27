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
$direccionEntrega = $data['direccion_entrega'] ?? null;
$productos = $data['productos'] ?? [];

if (!$pedidoId || !$direccionEntrega) {
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
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Actualizar la dirección del pedido
    $stmt2 = $conn->prepare("UPDATE pedidos SET direccion_entrega = ? WHERE ID_Pedido = ?");
    $stmt2->bind_param("si", $direccionEntrega, $pedidoId);
    $stmt2->execute();
    $stmt2->close();
    
    // Eliminar productos actuales del pedido
    $stmt3 = $conn->prepare("DELETE FROM contiene WHERE ID_Pedido = ?");
    $stmt3->bind_param("i", $pedidoId);
    $stmt3->execute();
    $stmt3->close();
    
    // Insertar productos modificados
    if (!empty($productos)) {
        foreach ($productos as $producto) {
            // Obtener ID del producto por nombre
            $stmt4 = $conn->prepare("SELECT ID_Producto FROM productos WHERE nombre_producto = ?");
            $stmt4->bind_param("s", $producto['nombre_producto']);
            $stmt4->execute();
            $result4 = $stmt4->get_result();
            
            if ($row4 = $result4->fetch_assoc()) {
                $productoId = $row4['ID_Producto'];
                $cantidad = $producto['cantidad'];
                
                // Insertar en contiene
                $stmt5 = $conn->prepare("INSERT INTO contiene (ID_Pedido, ID_Producto, cantidad) VALUES (?, ?, ?)");
                $stmt5->bind_param("iii", $pedidoId, $productoId, $cantidad);
                $stmt5->execute();
                $stmt5->close();
            }
            $stmt4->close();
        }
    }
    
    // Commit transacción
    $conn->commit();
    echo json_encode(['success' => true, 'msg' => 'Pedido actualizado correctamente']);
    
    $stmt->close();
    $stmt2->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>

