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
$estadoPedido = $data['estado_pedido'] ?? null;
$direccionEntrega = $data['direccion_entrega'] ?? null;
$telefonoCliente = $data['telefono_cliente'] ?? null;
$direccionCliente = $data['direccion_cliente'] ?? null;

if (!$pedidoId) {
    echo json_encode(['success' => false, 'msg' => 'ID de pedido requerido']);
    exit;
}

try {
    // Verificar que el pedido existe
    $stmt = $conn->prepare("SELECT ID_Pedido FROM pedidos WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'msg' => 'Pedido no encontrado']);
        exit;
    }
    $stmt->close();
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    // Actualizar el pedido
    $sql = "UPDATE pedidos SET";
    $params = [];
    $types = "";
    
    if ($estadoPedido !== null) {
        $sql .= " estado_pedido = ?,";
        $params[] = $estadoPedido;
        $types .= "s";
    }
    
    if ($direccionEntrega !== null) {
        $sql .= " direccion_entrega = ?,";
        $params[] = $direccionEntrega;
        $types .= "s";
    }
    
    // Remover la última coma
    $sql = rtrim($sql, ',');
    $sql .= " WHERE ID_Pedido = ?";
    $params[] = $pedidoId;
    $types .= "i";
    
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param($types, ...$params);
    
    if ($stmt2->execute()) {
        // Si hay datos del cliente, actualizar también en la tabla usuarios
        if ($telefonoCliente !== null || $direccionCliente !== null) {
            // Obtener el ID del usuario del pedido
            $stmt3 = $conn->prepare("SELECT ID_Usuario FROM pedidos WHERE ID_Pedido = ?");
            $stmt3->bind_param("i", $pedidoId);
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            
            if ($result3->num_rows > 0) {
                $pedido = $result3->fetch_assoc();
                $usuarioId = $pedido['ID_Usuario'];
                
                $sqlUsuario = "UPDATE usuarios SET";
                $paramsUsuario = [];
                $typesUsuario = "";
                
                if ($telefonoCliente !== null) {
                    $sqlUsuario .= " telefono = ?,";
                    $paramsUsuario[] = $telefonoCliente;
                    $typesUsuario .= "s";
                }
                
                if ($direccionCliente !== null) {
                    $sqlUsuario .= " direccion = ?,";
                    $paramsUsuario[] = $direccionCliente;
                    $typesUsuario .= "s";
                }
                
                $sqlUsuario = rtrim($sqlUsuario, ',');
                $sqlUsuario .= " WHERE ID_Usuario = ?";
                $paramsUsuario[] = $usuarioId;
                $typesUsuario .= "i";
                
                $stmt4 = $conn->prepare($sqlUsuario);
                $stmt4->bind_param($typesUsuario, ...$paramsUsuario);
                $stmt4->execute();
                $stmt4->close();
            }
            $stmt3->close();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'msg' => 'Pedido actualizado correctamente']);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'msg' => 'Error al actualizar el pedido']);
    }
    
    $stmt2->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>

