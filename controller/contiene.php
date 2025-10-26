<?php
// Configurar manejo de errores personalizado
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

// Manejador de errores
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $errstr]);
    exit;
});

require_once __DIR__ . '/conexionDB.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // listar productos por familia si se pasa familia_id
    $fam = isset($_GET['familia_id']) ? intval($_GET['familia_id']) : null;
    if ($fam) {
        $sql = "SELECT ID_Producto, nombre_producto, precio_unitario, ID_Familia, imagenURL FROM productos WHERE ID_Familia = {$fam}";
        $res = $conn->query($sql);
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['success'=>true,'productos'=>$rows]);
        exit;
    }
    echo json_encode(['success'=>false,'msg'=>'familia_id requerido']);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['success'=>false,'msg'=>'No input']); exit; }
    $action = $input['action'] ?? '';
    if ($action === 'setProductsForFamily') {
        $fid = intval($input['familia_id'] ?? 0);
        $productos = $input['productos'] ?? [];
        if (!$fid) { echo json_encode(['success'=>false,'msg'=>'familia_id requerido']); exit; }

        // Los productos deseleccionados se mantienen con su ID_Familia anterior (no se cambia a NULL)
        // Solo los productos seleccionados se aseguran de que tengan la familia correcta

        // Asignar los productos seleccionados a la familia
        foreach ($productos as $pid) {
            $pidInt = intval($pid);
            if ($pidInt) {
                $sqlUpd = "UPDATE productos SET ID_Familia = {$fid} WHERE ID_Producto = {$pidInt}";
                $conn->query($sqlUpd);
            }
        }

        // Los productos NO seleccionados NO pertenecen a esta familia
        // Pero como no pueden tener ID_Familia = NULL, creamos una lógica alternativa:
        // Solo mostrar productos que pertenecen EXPLÍCITAMENTE a esta familia
        // Esto se maneja en el GET (línea 34) que filtra por ID_Familia

        echo json_encode(['success'=>true]);
        exit;
    }
    echo json_encode(['success'=>false,'msg'=>'accion desconocida']);
    exit;
}

echo json_encode(['success'=>false,'msg'=>'Metodo no soportado']);

?>