<?php
// Controlador para procesar pedidos
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

// Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

$userId = intval($_SESSION['usuario']['ID_Usuario']);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'msg' => 'No input']);
        exit;
    }

    // Obtener datos del formulario
    $direccion = $conn->real_escape_string($input['direccion'] ?? '');
    $telefono = $conn->real_escape_string($input['telefono'] ?? '');
    $metodoPago = $conn->real_escape_string($input['metodo_pago'] ?? 'Efectivo');

    // Validar datos requeridos
    if (empty($direccion)) {
        echo json_encode(['success' => false, 'msg' => 'Dirección es requerida']);
        exit;
    }

    // Si no se proporciona teléfono, usar el del usuario de la sesión
    if (empty($telefono)) {
        $telefono = $_SESSION['usuario']['telefono'] ?? '';
    }

    // Obtener carrito de la sesión
    $cart = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

    if (empty($cart)) {
        echo json_encode(['success' => false, 'msg' => 'El carrito está vacío']);
        exit;
    }

    // Obtener información de productos y calcular total
    $productIds = implode(',', array_keys($cart));
    $sql = "SELECT ID_Producto, nombre_producto, precio_unitario FROM productos WHERE ID_Producto IN ({$productIds})";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(['success' => false, 'msg' => 'Error al obtener productos']);
        exit;
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[$row['ID_Producto']] = $row;
    }

    // Calcular total
    $total = 0;
    $orderItems = [];

    foreach ($cart as $productId => $cantidad) {
        if (isset($products[$productId])) {
            $product = $products[$productId];
            $subtotal = $product['precio_unitario'] * $cantidad;
            $total += $subtotal;

            $orderItems[] = [
                'ID_Producto' => $productId,
                'cantidad' => $cantidad,
                'precio_unitario' => $product['precio_unitario'],
                'subtotal' => $subtotal
            ];
        }
    }

    if (empty($orderItems)) {
        echo json_encode(['success' => false, 'msg' => 'No hay productos válidos en el carrito']);
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Insertar pedido con valores por defecto
        $sqlPedido = "INSERT INTO pedidos (ID_Usuario, direccion_entrega, estado_pedido, fecha_pedido)
                      VALUES ({$userId}, '{$direccion}', 'Pendiente', NOW())";

        if (!$conn->query($sqlPedido)) {
            throw new Exception('Error al crear el pedido: ' . $conn->error);
        }

        $pedidoId = $conn->insert_id;

        // Guardar productos del pedido en la tabla contiene
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $productId => $cantidad) {
                $stmtContiene = $conn->prepare("INSERT INTO contiene (ID_Pedido, ID_Producto, cantidad) VALUES (?, ?, ?)");
                $stmtContiene->bind_param("iii", $pedidoId, $productId, $cantidad);
                $stmtContiene->execute();
                $stmtContiene->close();
            }
        }

        // Insertar pago en la tabla pagos
        $sqlPago = "INSERT INTO pagos (ID_Pedido, metodo_pago, monto_total)
                    VALUES ({$pedidoId}, '{$metodoPago}', {$total})";

        if (!$conn->query($sqlPago)) {
            throw new Exception('Error al registrar el pago: ' . $conn->error);
        }

        // Commit transaction
        $conn->commit();

        // Limpiar carrito después de procesar el pedido
        $_SESSION['carrito'] = [];

        echo json_encode([
            'success' => true,
            'msg' => 'Pedido realizado exitosamente',
            'ID_Pedido' => $pedidoId,
            'total' => $total
        ]);

    } catch (Exception $e) {
        // Rollback en caso de error
        $conn->rollback();
        echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
    }

    exit;
}

echo json_encode(['success' => false, 'msg' => 'Método no soportado']);
?>
