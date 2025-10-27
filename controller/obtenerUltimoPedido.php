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
    // Consulta con prepared statement - obtener el último pedido del usuario
    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE ID_Usuario = ? ORDER BY fecha_pedido DESC LIMIT 1");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => true,
            'hasPedido' => false,
            'msg' => 'No tienes pedidos aún'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $pedido = $result->fetch_assoc();
    $stmt->close();
    
    // Obtener productos del último pedido desde la tabla contiene
    $productos = [];
    $stmt2 = $conn->prepare("
        SELECT p.nombre_producto, c.cantidad 
        FROM contiene c 
        INNER JOIN productos p ON c.ID_Producto = p.ID_Producto 
        WHERE c.ID_Pedido = ?
    ");
    $stmt2->bind_param("i", $pedido['ID_Pedido']);
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
    
    echo json_encode([
        'success' => true,
        'hasPedido' => true,
        'pedido' => $pedido
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => 'Error al obtener el pedido: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

