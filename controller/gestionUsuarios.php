<?php
require_once __DIR__ . '/../config.php';
include('conexionDB.php');


$stmt = $conn->prepare("SELECT ID_Usuario, nombre, correo,ID_Rol FROM Usuarios");
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fila['ID_Usuario']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['correo']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['ID_Rol']) . "</td>";

        echo "<td>
        <form action='" . BASE_URL . "/controller/eliminarUsuario.php' method='POST' style='display:inline;'>
            <input type='hidden' name='idUsuario' value='" . $fila['ID_Usuario'] . "'>
            <button type='submit' onclick='return confirm(\"¿Seguro que quieres eliminar este usuario?\")'>Eliminar</button>
        </form>
      </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td>No hay usuarios registrados</td></tr>";
}

$stmt->close();
$conn->close();
