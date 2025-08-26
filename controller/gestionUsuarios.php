<?php
require_once __DIR__ . '/../config.php';
include('conexionDB.php');

$roles = [];
$resultadoRoles = $conn->query("SELECT ID_Rol, nombre FROM Roles");
while ($rol = $resultadoRoles->fetch_assoc()) {
    $roles[] = $rol;
}


$stmt = $conn->prepare("SELECT ID_Usuario, nombre, correo,ID_Rol FROM Usuarios");
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fila['ID_Usuario']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['correo']) . "</td>";

        // Select para roles
        echo "<td>
                  <form method='POST' action='" . BASE_URL . "/controller/cambiarRol.php'>
                      <input type='hidden' name='idUsuario' value='" . $fila['ID_Usuario'] . "'>
                      <select name='rol' onchange='this.form.submit()'>";

        foreach ($roles as $rol) {
            $selected = ($fila['ID_Rol'] == $rol['ID_Rol']) ? "selected" : "";
            echo "<option value='" . $rol['ID_Rol'] . "' $selected>" . htmlspecialchars($rol['nombre']) . "</option>";
        }

        echo "</select>
                  </form>
              </td>";

        // Botón eliminar
        echo "<td>
        <form action='" . BASE_URL . "/controller/eliminarUsuario.php' method='POST'>
            <input type='hidden' name='idUsuario' value='" . $fila['ID_Usuario'] . "'>
            <button type='submit' class='btn-eliminar-icon' onclick='return confirm(\"¿Seguro que quieres eliminar este usuario?\")'>
                <img src='" . BASE_URL . "/images/trash.svg' alt='Eliminar'>
            </button>
        </form>
      </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay usuarios registrados</td></tr>";
}


$stmt->close();
$conn->close();
