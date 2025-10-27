<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación de administrador
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

$pedidoId = $_GET['id'] ?? null;

if (!$pedidoId) {
    echo json_encode(['success' => false, 'msg' => 'ID de pedido requerido']);
    exit;
}

try {
    // Debug: Log del pedido solicitado
    error_log("Solicitando detalles del pedido ID: " . $pedidoId);
    
    // Obtener información completa del pedido y del cliente
    $stmt = $conn->prepare("
        SELECT 
            p.ID_Pedido,
            p.fecha_pedido,
            p.estado_pedido,
            p.direccion_entrega,
            u.nombre as nombre_cliente,
            u.correo as correo_cliente,
            u.telefono as telefono_cliente,
            u.direccion as direccion_cliente
        FROM pedidos p
        INNER JOIN usuarios u ON p.ID_Usuario = u.ID_Usuario
        WHERE p.ID_Pedido = ?
    ");
    
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'msg' => 'Pedido no encontrado']);
        exit;
    }
    
    $pedido = $result->fetch_assoc();
    $stmt->close();
    
    // Obtener productos del pedido desde la tabla contiene
    $productos = [];
    $stmt2 = $conn->prepare("
        SELECT p.nombre_producto, c.cantidad 
        FROM contiene c 
        INNER JOIN productos p ON c.ID_Producto = p.ID_Producto 
        WHERE c.ID_Pedido = ?
    ");
    $stmt2->bind_param("i", $pedidoId);
    $stmt2->execute();
    $resultProductos = $stmt2->get_result();
    
    while ($row = $resultProductos->fetch_assoc()) {
        $productos[] = [
            'nombre_producto' => $row['nombre_producto'],
            'cantidad' => $row['cantidad']
        ];
    }
    $stmt2->close();
    
    $pedido['productos'] = $productos;
    
    error_log("Pedido encontrado: " . json_encode($pedido));
    error_log("Productos encontrados: " . count($productos));
    
    echo json_encode([
        'success' => true,
        'pedido' => $pedido
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
