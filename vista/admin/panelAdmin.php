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
    <title>Pizzería Dominico - Panel Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/panelAdmin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/src/css/slidebarAdmin.css">
</head>

<body>
    <?php include(__DIR__ . '/../components/slidebarAdmin.php'); ?>
    
    <div class="main-content">
        <!-- Estadísticas del mes -->
        <div class="dashboard-cards">
            <div class="card">
                <h3>Pedidos del Mes</h3>
                <div class="value" id="pedidosMes">-</div>
                <div class="change" id="pedidosCompletados">-</div>
            </div>
            
            <div class="card">
                <h3>Usuarios Registrados</h3>
                <div class="value" id="usuariosMes">-</div>
                <div class="change" id="totalUsuarios">-</div>
            </div>
            
            <div class="card">
                <h3>Ingresos del Mes</h3>
                <div class="value" id="ingresosMes">-</div>
                <div class="change">Este mes</div>
            </div>
            
            <div class="card">
                <h3>Total Pedidos</h3>
                <div class="value" id="totalPedidos">-</div>
                <div class="change">Histórico</div>
            </div>
        </div>
    </div>

    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        
        // Cargar estadísticas al cargar la página
        document.addEventListener('DOMContentLoaded', cargarEstadisticas);
        
        async function cargarEstadisticas() {
            try {
                const response = await fetch(window.BASE_URL + '/controller/estadisticasAdmin.php', {
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Actualizar estadísticas de pedidos
                    document.getElementById('pedidosMes').textContent = data.pedidos_mes.total;
                    document.getElementById('pedidosCompletados').textContent = 
                        `${data.pedidos_mes.completados} completados`;
                    
                    // Actualizar estadísticas de usuarios
                    document.getElementById('usuariosMes').textContent = data.usuarios_mes.registrados;
                    document.getElementById('totalUsuarios').textContent = 
                        `${data.usuarios_mes.total} total`;
                    
                    // Actualizar ingresos
                    document.getElementById('ingresosMes').textContent = 
                        '$' + data.pedidos_mes.ingresos.toFixed(2);
                    
                    // Actualizar total de pedidos
                    document.getElementById('totalPedidos').textContent = data.pedidos_totales;
                    
                } else {
                    console.error('Error cargando estadísticas:', data.msg);
                    mostrarError('Error cargando estadísticas');
                }
                
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            }
        }
        
        function mostrarError(mensaje) {
            // Mostrar error en las tarjetas
            document.getElementById('pedidosMes').textContent = 'Error';
            document.getElementById('usuariosMes').textContent = 'Error';
            document.getElementById('ingresosMes').textContent = 'Error';
            document.getElementById('totalPedidos').textContent = 'Error';
        }
    </script>
</body>
</html>