<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

// Verificar autenticación de administrador
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    $_SESSION['error'] = 'No autorizado';
    header("Location: " . BASE_URL . "/vista/admin/gestionPedidos.php");
    exit;
}

// Verificar que se enviaron los datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: " . BASE_URL . "/vista/admin/gestionPedidos.php");
    exit;
}

$pedidoId = $_POST['pedido_id'] ?? null;
$estado = $_POST['estado'] ?? null;

if (!$pedidoId || !$estado) {
    $_SESSION['error'] = 'Faltan campos obligatorios';
    header("Location: " . BASE_URL . "/vista/admin/gestionPedidos.php");
    exit;
}

// Validar estado
$estadosValidos = ['Pendiente', 'En proceso', 'En camino', 'Entregado', 'Cancelado'];
if (!in_array($estado, $estadosValidos)) {
    $_SESSION['error'] = 'Estado no válido';
    header("Location: " . BASE_URL . "/vista/admin/gestionPedidos.php");
    exit;
}

try {
    // Actualizar el estado del pedido
    $stmt = $conn->prepare("UPDATE pedidos SET estado_pedido = ? WHERE ID_Pedido = ?");
    $stmt->bind_param("si", $estado, $pedidoId);
    
    if ($stmt->execute()) {
        $_SESSION['acierto'] = 'Estado del pedido #' . $pedidoId . ' actualizado a "' . $estado . '" correctamente';
    } else {
        $_SESSION['error'] = 'Error al actualizar el estado del pedido';
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

$conn->close();

// Redirigir de vuelta a la página de gestión
header("Location: " . BASE_URL . "/vista/admin/gestionPedidos.php");
exit;
?>
