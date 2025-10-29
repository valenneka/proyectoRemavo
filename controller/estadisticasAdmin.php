<?php
// Estadísticas del panel de administración
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Configurar manejador para fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        echo json_encode(['success' => false, 'msg' => 'Fatal Error: ' . $error['message']]);
        exit;
    }
});

// Manejador de errores
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $errstr]);
    exit;
});

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

// Verificar que el usuario esté autenticado y sea administrador (rol 3)
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['ID_Rol'] != 3) {
    // Solo redirigir si estamos en un contexto web
    if (isset($_SERVER['HTTP_HOST'])) {
        header("Location: ../vista/public/error.php");
        exit;
    } else {
        // Si es desde línea de comandos, devolver error JSON
        echo json_encode(['success' => false, 'msg' => 'Acceso denegado. Solo administradores.']);
        exit;
    }
}

try {
    // Obtener el primer día del mes actual
    $primerDiaMes = date('Y-m-01');
    $ultimoDiaMes = date('Y-m-t');
    
    // Estadísticas de pedidos del mes actual
    $sqlPedidos = "SELECT COUNT(*) as total_pedidos, 
                          SUM(CASE WHEN estado_pedido = 'Completado' THEN 1 ELSE 0 END) as pedidos_completados
                   FROM pedidos 
                   WHERE fecha_pedido >= ? AND fecha_pedido <= ?";
    
    $stmtPedidos = $conn->prepare($sqlPedidos);
    $stmtPedidos->bind_param("ss", $primerDiaMes, $ultimoDiaMes);
    $stmtPedidos->execute();
    $resultPedidos = $stmtPedidos->get_result();
    $pedidosStats = $resultPedidos->fetch_assoc();
    
    // Estadísticas de ingresos del mes (desde tabla pagos)
    $sqlIngresos = "SELECT SUM(p.monto_total) as ingresos_totales
                    FROM pagos p
                    INNER JOIN pedidos ped ON p.ID_Pedido = ped.ID_Pedido
                    WHERE ped.fecha_pedido >= ? AND ped.fecha_pedido <= ? 
                    AND ped.estado_pedido = 'Completado'";
    
    $stmtIngresos = $conn->prepare($sqlIngresos);
    $stmtIngresos->bind_param("ss", $primerDiaMes, $ultimoDiaMes);
    $stmtIngresos->execute();
    $resultIngresos = $stmtIngresos->get_result();
    $ingresosStats = $resultIngresos->fetch_assoc();
    
    // Estadísticas de usuarios (total de usuarios registrados)
    $sqlUsuarios = "SELECT COUNT(*) as usuarios_registrados FROM usuarios";
    $resultUsuarios = $conn->query($sqlUsuarios);
    $usuariosStats = $resultUsuarios->fetch_assoc();
    
    $sqlTotalPedidos = "SELECT COUNT(*) as total_pedidos FROM pedidos";
    $resultTotalPedidos = $conn->query($sqlTotalPedidos);
    $totalPedidos = $resultTotalPedidos->fetch_assoc();
    
    // Preparar respuesta
    $stats = [
        'success' => true,
        'mes_actual' => date('F Y'), // Mes actual en español
        'pedidos_mes' => [
            'total' => intval($pedidosStats['total_pedidos']),
            'completados' => intval($pedidosStats['pedidos_completados']),
            'ingresos' => floatval($ingresosStats['ingresos_totales'] ?? 0)
        ],
        'usuarios_mes' => [
            'registrados' => intval($usuariosStats['usuarios_registrados']),
            'total' => intval($usuariosStats['usuarios_registrados'])
        ],
        'pedidos_totales' => intval($totalPedidos['total_pedidos'])
    ];
    
    echo json_encode($stats);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'msg' => 'Error obteniendo estadísticas: ' . $e->getMessage()]);
}
?>
