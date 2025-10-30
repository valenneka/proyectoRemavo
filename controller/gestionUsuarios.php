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

        // Botones: editar (lápiz) y eliminar
        echo "<td>
        <div class='acciones-usuario'>
            <button type='button' class='btn-editar-icon' data-user-id='" . $fila['ID_Usuario'] . "' title='Editar usuario' aria-label='Editar usuario'>
                <svg width='18' height='18' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' aria-hidden='true'>
                    <path d='M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z' fill='#34495e'/>
                    <path d='M20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z' fill='#34495e'/>
                </svg>
            </button>
            <form action='" . BASE_URL . "/controller/eliminarUsuario.php' method='POST' style='display:inline-block'>
                <input type='hidden' name='idUsuario' value='" . $fila['ID_Usuario'] . "'>
                <button type='submit' class='btn-eliminar-icon' onclick='return confirm(\"¿Seguro que quieres eliminar este usuario?\")'>
                    <img src='" . BASE_URL . "/images/trash.svg' alt='Eliminar'>
                </button>
            </form>
        </div>
      </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No hay usuarios registrados</td></tr>";
}


$stmt->close();
$conn->close();
