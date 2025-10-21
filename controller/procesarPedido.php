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
    $notas = $conn->real_escape_string($input['notas'] ?? '');
    $metodoPago = $conn->real_escape_string($input['metodo_pago'] ?? 'Efectivo');

    // Validar datos requeridos
    if (empty($direccion) || empty($telefono)) {
        echo json_encode(['success' => false, 'msg' => 'Dirección y teléfono son requeridos']);
        exit;
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
        // Insertar pedido (solo con ID_Usuario, direccion_entrega, telefono_contacto, notas)
        $sqlPedido = "INSERT INTO pedidos (ID_Usuario, direccion_entrega, telefono_contacto, notas)
                      VALUES ({$userId}, '{$direccion}', '{$telefono}', '{$notas}')";

        if (!$conn->query($sqlPedido)) {
            throw new Exception('Error al crear el pedido: ' . $conn->error);
        }

        $pedidoId = $conn->insert_id;

        // Insertar detalles del pedido
        foreach ($orderItems as $item) {
            $sqlDetalle = "INSERT INTO detalle_pedido (ID_Pedido, ID_Producto, cantidad, precio_unitario, subtotal)
                           VALUES ({$pedidoId}, {$item['ID_Producto']}, {$item['cantidad']}, {$item['precio_unitario']}, {$item['subtotal']})";

            if (!$conn->query($sqlDetalle)) {
                throw new Exception('Error al agregar detalles del pedido');
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

        // Vaciar carrito
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
