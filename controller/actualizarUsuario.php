<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/conexionDB.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3)) {
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    echo json_encode(['success' => false, 'msg' => 'Datos inválidos']);
    exit;
}

$id = isset($data['ID_Usuario']) ? intval($data['ID_Usuario']) : 0;
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$correo = isset($data['correo']) ? trim($data['correo']) : '';
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';
$direccion = isset($data['direccion']) ? trim($data['direccion']) : '';

if ($id <= 0 || $nombre === '' || $correo === '') {
    echo json_encode(['success' => false, 'msg' => 'Faltan campos obligatorios']);
    exit;
}

try {
    // Validar correo único para otros usuarios
    $stmtCheck = $conn->prepare('SELECT ID_Usuario FROM Usuarios WHERE correo = ? AND ID_Usuario <> ?');
    $stmtCheck->bind_param('si', $correo, $id);
    $stmtCheck->execute();
    $rs = $stmtCheck->get_result();
    if ($rs && $rs->num_rows > 0) {
        echo json_encode(['success' => false, 'msg' => 'El correo ya está en uso por otro usuario']);
        exit;
    }
    $stmtCheck->close();

    $stmt = $conn->prepare('UPDATE Usuarios SET nombre = ?, correo = ?, telefono = ?, direccion = ? WHERE ID_Usuario = ?');
    $stmt->bind_param('ssssi', $nombre, $correo, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'msg' => 'Usuario actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'No se pudo actualizar el usuario']);
    }
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}
?>


