<?php
// Asegurarse de que los errores se capturen y devuelvan como JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Establecer header JSON de inmediato
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

require_once __DIR__ . '/conexionDB.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // listar productos - solo las columnas que existen
    $sql = "SELECT ID_Producto, nombre_producto, precio_unitario, ID_Familia, imagenURL FROM productos";
    $result = $conn->query($sql);
    if (!$result) {
        http_response_code(500);
        echo json_encode(['success' => false, 'msg' => 'Error en consulta: ' . $conn->error]);
        exit;
    }
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode(['success' => true, 'productos' => $items]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success'=>false,'msg'=>'No input']);
        exit;
    }

    $action = isset($input['action']) ? $conn->real_escape_string($input['action']) : '';

    if ($action === 'create') {
        $nombre = $conn->real_escape_string($input['nombre_producto'] ?? '');
        $precio = $conn->real_escape_string($input['precio_unitario'] ?? '');
        $familia_id = (isset($input['ID_Familia']) && $input['ID_Familia']) ? intval($input['ID_Familia']) : 'NULL';
        $imagen = $conn->real_escape_string($input['imagenURL'] ?? '');

        if (!$nombre || !$precio) {
            echo json_encode(['success'=>false,'msg'=>'Nombre y precio requeridos']);
            exit;
        }

        // Manejar base64 si se envía imagenBase64
        if (isset($input['imagenBase64']) && !empty($input['imagenBase64'])) {
            $base64 = $input['imagenBase64'];
            // Decodificar y guardar imagen
            if (strpos($base64, 'data:image') === 0) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
            }
            $imageData = base64_decode($base64, true);
            if ($imageData === false) {
                echo json_encode(['success'=>false,'msg'=>'Imagen base64 inválida']);
                exit;
            }

            $filename = 'producto_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
            $filepath = __DIR__ . '/../images/productos/' . $filename;

            // Crear directorio si no existe
            if (!is_dir(__DIR__ . '/../images/productos/')) {
                mkdir(__DIR__ . '/../images/productos/', 0755, true);
            }

            if (file_put_contents($filepath, $imageData)) {
                // Guardar ruta relativa al proyecto (funciona tanto desde admin como desde public)
                $imagen = 'images/productos/' . $filename;
            }
        }

        // Construir INSERT sin incluir descripcion si no existe
        // La columna ID_Familia debe PERMITIR NULL en la BD, o necesitas que siempre haya familia
        $sql = "INSERT INTO productos (nombre_producto, precio_unitario, ID_Familia, imagenURL)
                VALUES ('{$nombre}', '{$precio}', {$familia_id}, '{$imagen}')";

        if ($conn->query($sql)) {
            $id = $conn->insert_id;
            echo json_encode(['success'=>true,'id'=>$id,'msg'=>'Producto creado exitosamente']);
        } else {
            echo json_encode(['success'=>false,'msg'=>'Error en INSERT: ' . $conn->error]);
        }
        exit;
    }

    if ($action === 'update') {
        $id = intval($input['id'] ?? 0);
        $nombre = $conn->real_escape_string($input['nombre_producto'] ?? '');
        $precio = $conn->real_escape_string($input['precio_unitario'] ?? '');
        $imagen = $conn->real_escape_string($input['imagenURL'] ?? '');

        if (!$id || !$nombre || !$precio) {
            echo json_encode(['success'=>false,'msg'=>'ID, nombre y precio requeridos']);
            exit;
        }

        // Manejar base64 si se envía imagenBase64
        if (isset($input['imagenBase64']) && !empty($input['imagenBase64'])) {
            $base64 = $input['imagenBase64'];
            if (strpos($base64, 'data:image') === 0) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
            }
            $imageData = base64_decode($base64);
            $filename = 'producto_' . time() . '.png';
            $filepath = __DIR__ . '/../images/productos/' . $filename;

            if (!is_dir(__DIR__ . '/../images/productos/')) {
                mkdir(__DIR__ . '/../images/productos/', 0755, true);
            }

            if (file_put_contents($filepath, $imageData)) {
                // Guardar ruta relativa al proyecto
                $imagen = 'images/productos/' . $filename;
            }
        }

        $updateImg = $imagen ? ", imagenURL='{$imagen}'" : '';
        $sql = "UPDATE productos SET nombre_producto='{$nombre}', precio_unitario='{$precio}'{$updateImg} WHERE ID_Producto={$id}";

        if ($conn->query($sql)) {
            echo json_encode(['success'=>true,'msg'=>'Producto actualizado exitosamente']);
        } else {
            echo json_encode(['success'=>false,'msg'=>$conn->error]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success'=>false,'msg'=>'ID requerido']);
            exit;
        }
        $sql = "DELETE FROM productos WHERE ID_Producto={$id}";
        if ($conn->query($sql)) {
            echo json_encode(['success'=>true,'msg'=>'Producto eliminado exitosamente']);
        } else {
            echo json_encode(['success'=>false,'msg'=>$conn->error]);
        }
        exit;
    }

    echo json_encode(['success'=>false,'msg'=>'Acción no soportada']);
    exit;
}

echo json_encode(['success'=>false,'msg'=>'Método no soportado']);

?>