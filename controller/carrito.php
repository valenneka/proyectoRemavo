<?php
// Carrito Backend - Gestión de carrito de compras por usuario
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

// Configurar manejador de errores personalizado
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $errstr]);
    exit;
});

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

// Verificar que el usuario esté autenticado
// Solo se requiere autenticación para las operaciones GET/POST del carrito
// La sesión debe estar activa (config.php la inicia)
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

$userId = intval($_SESSION['usuario']['ID_Usuario']);
$method = $_SERVER['REQUEST_METHOD'];

// GET: obtener carrito del usuario
if ($method === 'GET') {
    // Obtener items del carrito (guardados en sesión)
    $cart = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

    // Enriquecer con información de productos
    $cartItems = [];
    if (!empty($cart)) {
        $productIds = implode(',', array_keys($cart));
        $sql = "SELECT ID_Producto, nombre_producto, precio_unitario, imagenURL FROM productos WHERE ID_Producto IN ({$productIds})";
        $result = $conn->query($sql);

        if ($result) {
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[$row['ID_Producto']] = $row;
            }

            foreach ($cart as $productId => $cantidad) {
                if (isset($products[$productId])) {
                    $product = $products[$productId];
                    $cartItems[] = [
                        'ID_Producto' => $productId,
                        'nombre_producto' => $product['nombre_producto'],
                        'precio_unitario' => $product['precio_unitario'],
                        'imagenURL' => $product['imagenURL'],
                        'cantidad' => $cantidad,
                        'subtotal' => $product['precio_unitario'] * $cantidad
                    ];
                }
            }
        }
    }

    // Calcular total
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['subtotal'];
    }

    echo json_encode([
        'success' => true,
        'items' => $cartItems,
        'total' => $total,
        'cantidad_items' => count($cartItems)
    ]);
    exit;
}

// POST: agregar/actualizar item en carrito
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'msg' => 'No input']);
        exit;
    }

    $action = isset($input['action']) ? $conn->real_escape_string($input['action']) : '';

    // Inicializar carrito en sesión si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar producto al carrito
    if ($action === 'add') {
        $productId = intval($input['ID_Producto'] ?? 0);
        $cantidad = intval($input['cantidad'] ?? 1);

        if ($productId <= 0 || $cantidad <= 0) {
            echo json_encode(['success' => false, 'msg' => 'Producto ID y cantidad inválidos']);
            exit;
        }

        // Verificar que el producto existe
        $sqlVerify = "SELECT ID_Producto FROM productos WHERE ID_Producto = {$productId}";
        $resultVerify = $conn->query($sqlVerify);

        if (!$resultVerify || $resultVerify->num_rows === 0) {
            echo json_encode(['success' => false, 'msg' => 'Producto no existe']);
            exit;
        }

        // Agregar o actualizar en carrito
        if (isset($_SESSION['carrito'][$productId])) {
            $_SESSION['carrito'][$productId] += $cantidad;
        } else {
            $_SESSION['carrito'][$productId] = $cantidad;
        }

        echo json_encode([
            'success' => true,
            'msg' => 'Producto agregado al carrito',
            'cantidad_items' => count($_SESSION['carrito'])
        ]);
        exit;
    }

    // Remover producto del carrito
    if ($action === 'remove') {
        $productId = intval($input['ID_Producto'] ?? 0);

        if ($productId <= 0) {
            echo json_encode(['success' => false, 'msg' => 'Producto ID inválido']);
            exit;
        }

        if (isset($_SESSION['carrito'][$productId])) {
            unset($_SESSION['carrito'][$productId]);
        }

        echo json_encode([
            'success' => true,
            'msg' => 'Producto removido del carrito',
            'cantidad_items' => count($_SESSION['carrito'])
        ]);
        exit;
    }

    // Actualizar cantidad de producto
    if ($action === 'update') {
        $productId = intval($input['ID_Producto'] ?? 0);
        $cantidad = intval($input['cantidad'] ?? 0);

        if ($productId <= 0) {
            echo json_encode(['success' => false, 'msg' => 'Producto ID inválido']);
            exit;
        }

        if ($cantidad <= 0) {
            // Si cantidad es 0, remover
            if (isset($_SESSION['carrito'][$productId])) {
                unset($_SESSION['carrito'][$productId]);
            }
        } else {
            $_SESSION['carrito'][$productId] = $cantidad;
        }

        echo json_encode([
            'success' => true,
            'msg' => 'Cantidad actualizada',
            'cantidad_items' => count($_SESSION['carrito'])
        ]);
        exit;
    }

    // Vaciar carrito
    if ($action === 'clear') {
        $_SESSION['carrito'] = [];
        echo json_encode([
            'success' => true,
            'msg' => 'Carrito vaciado',
            'cantidad_items' => 0
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'msg' => 'Acción no soportada']);
    exit;
}

echo json_encode(['success' => false, 'msg' => 'Método no soportado']);
?>
