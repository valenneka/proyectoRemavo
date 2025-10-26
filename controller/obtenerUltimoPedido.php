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

try {
    // Obtener el último pedido del usuario con información de pago
    $stmt = $conn->prepare("
        SELECT 
            p.ID_Pedido,
            p.fecha_pedido,
            p.estado_pedido,
            p.direccion_entrega,
            p.telefono_contacto,
            p.notas,
            pg.metodo_pago,
            pg.monto_total as total
        FROM pedidos p
        LEFT JOIN pagos pg ON p.ID_Pedido = pg.ID_Pedido
        WHERE p.ID_Usuario = ?
        ORDER BY p.fecha_pedido DESC
        LIMIT 1
    ");
    
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => true,
            'hasPedido' => false,
            'msg' => 'No tienes pedidos aún'
        ]);
        exit;
    }
    
    $pedido = $result->fetch_assoc();
    
    // Obtener los productos del pedido
    $stmt2 = $conn->prepare("
        SELECT 
            dp.ID_Detalle,
            dp.cantidad,
            dp.precio_unitario,
            dp.subtotal,
            prod.nombre_producto,
            prod.ID_Producto
        FROM detalle_pedido dp
        INNER JOIN productos prod ON dp.ID_Producto = prod.ID_Producto
        WHERE dp.ID_Pedido = ?
    ");
    
    $stmt2->bind_param("i", $pedido['ID_Pedido']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    $productos = [];
    while ($row = $result2->fetch_assoc()) {
        $productos[] = $row;
    }
    
    $pedido['productos'] = $productos;
    
    // Si no hay total de la tabla pagos, calcularlo desde detalle_pedido
    if (!isset($pedido['total']) || $pedido['total'] === null) {
        $totalCalculado = 0;
        foreach ($productos as $prod) {
            $totalCalculado += $prod['subtotal'];
        }
        $pedido['total'] = $totalCalculado;
    }
    
    echo json_encode([
        'success' => true,
        'hasPedido' => true,
        'pedido' => $pedido
    ]);
    
    $stmt->close();
    $stmt2->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => 'Error al obtener el pedido: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

