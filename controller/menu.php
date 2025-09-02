<?php
require_once __DIR__ . '/../config.php';
include('conexionDB.php');

header('Content-Type: application/json; charset=utf-8');

// Consulta todas las categorías
$sqlCategorias = "SELECT id_categoria, nombre_categoria FROM Categorias";
$resCategorias = $conn->query($sqlCategorias);

$menu = [];

if ($resCategorias && $resCategorias->num_rows > 0) {
    while ($cat = $resCategorias->fetch_assoc()) {
        $idCategoria = $cat["id_categoria"];
        $nombreCategoria = $cat["nombre_categoria"];

        // Ahora traemos los productos de cada categoría
        $sqlProductos = $conn->prepare("SELECT nombre, descripcion, precio, imagen FROM Productos WHERE id_categoria = ?");
        $sqlProductos->bind_param("i", $idCategoria);
        $sqlProductos->execute();
        $resProductos = $sqlProductos->get_result();

        $productos = [];
        while ($prod = $resProductos->fetch_assoc()) {
            $productos[] = [
                "nombre" => $prod["nombre"],
                "descripcion" => $prod["descripcion"],
                "precio" => (float)$prod["precio"],
                "imagen" => $prod["imagen"]
            ];
        }

        // Guardamos en el array final
        $menu[$nombreCategoria] = $productos;
    }
}

echo json_encode($menu, JSON_UNESCAPED_UNICODE);
$conn->close();
