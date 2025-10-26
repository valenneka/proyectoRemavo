<?php
/**
 * Endpoint que devuelve TODOS los datos necesarios para gestionMenus.php
 * Esto es más eficiente que hacer múltiples requests desde JavaScript
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        echo json_encode(['success' => false, 'msg' => 'Fatal Error: ' . $error['message']]);
        exit;
    }
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $errstr]);
    exit;
});

require_once __DIR__ . '/conexionDB.php';

try {
    // Obtener todas las familias
    $sqlFamilias = "SELECT ID_Familia, nombre, descripcion FROM familia_productos ORDER BY nombre";
    $resultFamilias = $conn->query($sqlFamilias);

    if (!$resultFamilias) {
        throw new Exception('Error obteniendo familias: ' . $conn->error);
    }

    $familias = [];
    while ($row = $resultFamilias->fetch_assoc()) {
        $familiaId = $row['ID_Familia'];

        // Obtener productos de esta familia
        $sqlProductos = "SELECT ID_Producto, nombre_producto, precio_unitario, ID_Familia, imagenURL
                        FROM productos
                        WHERE ID_Familia = {$familiaId}
                        ORDER BY nombre_producto";
        $resultProductos = $conn->query($sqlProductos);

        if (!$resultProductos) {
            throw new Exception('Error obteniendo productos: ' . $conn->error);
        }

        $productos = [];
        while ($prod = $resultProductos->fetch_assoc()) {
            // Corregir ruta de imagen en el servidor (no en JavaScript)
            $imgURL = $prod['imagenURL'] ?? '';
            if (strpos($imgURL, '/images/') === 0) {
                $imgURL = substr($imgURL, 1); // Remover la barra inicial
            }

            $productos[] = [
                'ID_Producto' => (int)$prod['ID_Producto'],
                'nombre_producto' => $prod['nombre_producto'],
                'precio_unitario' => $prod['precio_unitario'],
                'ID_Familia' => (int)$prod['ID_Familia'],
                'imagenURL' => $imgURL
            ];
        }

        $familias[] = [
            'ID_Familia' => (int)$familiaId,
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'productos' => $productos,
            'cantidad_productos' => count($productos)
        ];
    }

    // Obtener TODOS los productos (para el listado de selección)
    $sqlTodos = "SELECT ID_Producto, nombre_producto, precio_unitario, imagenURL FROM productos ORDER BY nombre_producto";
    $resultTodos = $conn->query($sqlTodos);

    if (!$resultTodos) {
        throw new Exception('Error obteniendo productos: ' . $conn->error);
    }

    $todosProductos = [];
    while ($prod = $resultTodos->fetch_assoc()) {
        $imgURL = $prod['imagenURL'] ?? '';
        if (strpos($imgURL, '/images/') === 0) {
            $imgURL = substr($imgURL, 1);
        }

        $todosProductos[] = [
            'ID_Producto' => (int)$prod['ID_Producto'],
            'nombre_producto' => $prod['nombre_producto'],
            'precio_unitario' => $prod['precio_unitario'],
            'imagenURL' => $imgURL
        ];
    }

    echo json_encode([
        'success' => true,
        'familias' => $familias,
        'todos_productos' => $todosProductos
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}

?>
