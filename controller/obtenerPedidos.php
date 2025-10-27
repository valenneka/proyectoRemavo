<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación de administrador
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

try {
    // Obtener todos los pedidos con información del cliente
    $sql = "SELECT 
                p.ID_Pedido,
                p.fecha_pedido,
                p.estado_pedido,
                p.direccion_entrega,
                u.nombre as nombre_cliente,
                u.correo as correo_cliente
            FROM pedidos p
            INNER JOIN usuarios u ON p.ID_Usuario = u.ID_Usuario
            ORDER BY p.fecha_pedido DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . $conn->error);
    }
    
    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
