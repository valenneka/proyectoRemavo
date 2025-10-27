<?php require_once __DIR__ . '/../../config.php';
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 3 && $_SESSION['usuario']['ID_Rol'] != 2)) {
    header("Location: " . BASE_URL . "/vista/public/error.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/carrouseles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/modals.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/slidebarAdmin.css">
    <title>Pizzería Dominico - Gestión de Menús</title>
</head>

<body>

    <?php include(__DIR__ . '/../components/slidebarAdmin.php'); ?>

    <!-- Contenedor para lista de familias (menus creados) -->
    <div class="seccion-familias">
        <div class="header-seccion-familias">
            <h2 class="titulo-seccion">Menús Creados</h2>
            <div class="icono crear-familia-btn" onclick="abrirModalNuevaFamilia()" title="Crear Nuevo Menú">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="contenedor-familias" id="familiesContainer">
            <!-- Las familias se renderizarán aquí como una lista con carrouseles -->
        </div>
    </div>

    <!-- Modal para detalles de la pizza -->
    <div class="modal" id="modalPizza">
        <div class="contenido-modal">
            <button class="cerrar-modal" onclick="cerrarModal()">×</button>
            <img class="imagen-modal" id="imagenModal" src="" alt="">
            <h2 id="nombreModal"></h2>
            <p class="descripcion-modal" id="descripcionModal"></p>
            <h3 id="precioModal"></h3>
            <button class="boton-comprar btn-guardar" onclick="comprarPizza()">Agregar al Carrito</button>
        </div>
    </div>

    <!-- Modal para editar pizza -->
    <div class="modal" id="modalEditar">
        <div class="contenido-modal-editar">
            <button class="cerrar-modal" onclick="cerrarModalEditar()">×</button>
            <h2 class="titulo-modal-editar">Estás modificando (nombre_producto)</h2>

            <div class="campo-formulario">
                <label>Nombre:</label>
                <input type="text" id="editarNombre" class="input-editar">
            </div>

            <div class="campo-formulario">
                <label>Precio:</label>
                <input type="text" id="editarPrecio" class="input-editar" placeholder="$ 350">
            </div>

            <div class="campo-formulario">
                <label>Descripción:</label>
                <textarea id="editarDescripcion" class="textarea-editar" rows="3"></textarea>
            </div>

            <div class="contenedor-imagen-upload">
                <div class="icono-imagen">📁</div>
                <button class="boton-buscar-imagen" onclick="document.getElementById('inputImagen').click()">Buscar
                    Imagen</button>
                <input type="file" id="inputImagen" accept="image/*" style="display: none;"
                    onchange="cargarImagen(event)">
                <img id="vistaPrevia" class="vista-previa-imagen" style="display: none;">
            </div>

            <div class="botones-modal">
                <button class="btn-eliminar" onclick="eliminarPizza()">Eliminar</button>
                <button class="btn-guardar" onclick="guardarCambios()">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para crear nueva familia -->
    <div class="modal" id="modalNuevaFamilia">
        <div class="contenido-modal-familia">
            <button class="cerrar-modal" onclick="cerrarModalFamilia()">×</button>
            <h2 class="titulo-modal-editar">Modificar Familia</h2>

            <div class="campo-formulario">
                <label>Nombre:</label>
                <input type="text" id="nombreFamilia" class="input-editar" placeholder="Ej: Pizzas, Bebidas, Postres">
            </div>

            <div class="campo-formulario">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label style="margin: 0;">Productos</label>
                    <button class="btn-crear-producto" onclick="abrirModalCrearProducto()" title="Crear nuevo producto">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5v14M5 12h14" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Nuevo Producto
                    </button>
                </div>
                <div class="lista-productos" id="listaProductosFamilia">
                    <!-- Aquí JS renderizará checkboxes con todos los productos (data-id) -->
                </div>
            </div>

            <div class="botones-modal">
                <button class="btn-eliminar" onclick="eliminarFamilia()">Eliminar</button>
                <button class="btn-guardar" onclick="guardarFamilia()">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para crear nuevo producto -->
    <div class="modal" id="modalCrearProducto">
        <div class="contenido-modal-editar">
            <button class="cerrar-modal" onclick="cerrarModalCrearProducto()">×</button>
            <h2 class="titulo-modal-editar">Crear Nuevo Producto</h2>

            <div class="campo-formulario">
                <label>Nombre:</label>
                <input type="text" id="crearNombre" class="input-editar" placeholder="Ej: Pizza Margarita">
            </div>

            <div class="campo-formulario">
                <label>Precio:</label>
                <input type="text" id="crearPrecio" class="input-editar" placeholder="$ 350">
            </div>

            <div class="campo-formulario">
                <label>Descripción:</label>
                <textarea id="crearDescripcion" class="textarea-editar" rows="3" placeholder="Descripción del producto"></textarea>
            </div>

            <div class="contenedor-imagen-upload">
                <div class="icono-imagen">📁</div>
                <button class="boton-buscar-imagen" onclick="document.getElementById('inputImagenCrear').click()">Buscar Imagen</button>
                <input type="file" id="inputImagenCrear" accept="image/*" style="display: none;" onchange="cargarImagenCrear(event)">
                <img id="vistaPreviaCrear" class="vista-previa-imagen" style="display: none;">
            </div>

            <div class="botones-modal">
                <button class="btn-cancelar" onclick="cerrarModalCrearProducto()">Cancelar</button>
                <button class="btn-guardar" onclick="crearNuevoProducto()">Crear Producto</button>
            </div>
        </div>
    </div>

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>/src/js/menuCarrousel.js"></script>

</body>

</html>