// Profile Handler - Manejo del perfil de usuario

let pedidoActual = null;

// Cargar último pedido al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    cargarUltimoPedido();
});

// Cargar el último pedido del usuario
async function cargarUltimoPedido() {
    try {
        const response = await fetch(`${window.BASE_URL}/controller/obtenerUltimoPedido.php`, {
            credentials: 'include'
        });
        
        const data = await response.json();
        
        if (!data.success) {
            mostrarError('No se pudo cargar el pedido: ' + (data.msg || 'Error desconocido'));
            return;
        }
        
        if (!data.hasPedido) {
            mostrarNoPedido();
            return;
        }
        
        pedidoActual = data.pedido;
        renderizarPedido(data.pedido);
        
    } catch (error) {
        console.error('Error cargando pedido:', error);
        mostrarError('Error al cargar el pedido: ' + error.message);
    }
}

// Renderizar el pedido en la interfaz
function renderizarPedido(pedido) {
    const orderCard = document.getElementById('orderCard');
    
    // Manejar estado null o undefined
    const estado = pedido.estado_pedido || 'Sin estado';
    const estadoClass = estado.toLowerCase().replace(/ /g, '-');
    
    orderCard.innerHTML = `
        <h3 class="order-header">Último pedido:</h3>
        
        <div class="order-section">
            <div class="section-header">
                <span class="section-title">Comida</span>
                <span class="status-badge ${estadoClass}">${estado}</span>
                ${estado === 'Pendiente' ? `
                    <button class="edit-pedido-btn" onclick="abrirModalPedido()" title="Editar pedido">
                        <svg class="edit-svg" viewBox="0 0 24 24">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                        </svg>
                    </button>
                    <button class="cancelar-pedido-btn" onclick="cancelarPedido()" title="Cancelar pedido">
                        ❌
                    </button>
                ` : ''}
            </div>
            
            <ul class="menu-items">
                ${(pedido.productos || []).map(prod => `
                    <li class="menu-item">${prod.nombre_producto || 'Producto desconocido'} x${prod.cantidad || 1}</li>
                `).join('')}
            </ul>
        </div>
    `;
}

// Mostrar mensaje cuando no hay pedidos
function mostrarNoPedido() {
    const orderCard = document.getElementById('orderCard');
    orderCard.innerHTML = `
        <h3 class="order-header">Último pedido:</h3>
        <div class="no-pedido-section">
            <p class="no-pedido">Aún no has realizado ningún pedido.</p>
            <p class="no-pedido-sub">¡Haz tu primer pedido en la tienda!</p>
            <a href="${window.BASE_URL}/vista/public/tienda.php" class="btn-ir-tienda">Ir a la Tienda</a>
        </div>
    `;
}

// Mostrar error
function mostrarError(mensaje) {
    const orderCard = document.getElementById('orderCard');
    orderCard.innerHTML = `
        <h3 class="order-header">Último pedido:</h3>
        <p class="error-message">${mensaje}</p>
    `;
}

// Abrir modal para modificar pedido
function abrirModalPedido() {
    if (!pedidoActual) {
        alert('No hay pedido para modificar');
        return;
    }
    
    const modal = document.getElementById('modalModificarPedido');
    
    // Llenar formulario con datos actuales
    document.getElementById('pedidoId').value = pedidoActual.ID_Pedido;
    document.getElementById('estadoPedido').value = pedidoActual.estado_pedido;
    document.getElementById('comentarioPedido').value = pedidoActual.notas || '';
    document.getElementById('direccionPedido').value = pedidoActual.direccion_entrega;
    
    // Llenar tabla de productos con controles de edición
    const tableBody = document.getElementById('pedidoItemsTable');
    tableBody.innerHTML = pedidoActual.productos.map((prod, index) => `
        <tr>
            <td>${prod.nombre_producto}</td>
            <td>
                <input type="number" 
                       id="cantidad_${index}" 
                       value="${prod.cantidad}" 
                       min="1" 
                       max="10" 
                       class="cantidad-input">
            </td>
            <td>
                <button type="button" 
                        class="btn-eliminar-producto" 
                        onclick="eliminarProducto(${index})"
                        title="Eliminar producto">
                    🗑️
                </button>
            </td>
        </tr>
    `).join('');
    
    modal.classList.add('active');
}

// Cerrar modal
function cerrarModalPedido() {
    const modal = document.getElementById('modalModificarPedido');
    modal.classList.remove('active');
}

// Guardar cambios del pedido
async function guardarCambiosPedido(event) {
    event.preventDefault();
    
    const pedidoId = document.getElementById('pedidoId').value;
    const direccion = document.getElementById('direccionPedido').value;
    
    if (!direccion.trim()) {
        alert('La dirección es obligatoria');
        return;
    }
    
    // Recopilar productos modificados
    const productosModificados = [];
    pedidoActual.productos.forEach((prod, index) => {
        const cantidadInput = document.getElementById(`cantidad_${index}`);
        if (cantidadInput) {
            productosModificados.push({
                nombre_producto: prod.nombre_producto,
                cantidad: parseInt(cantidadInput.value) || 1
            });
        }
    });
    
    try {
        const response = await fetch(`${window.BASE_URL}/controller/actualizarPedido.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                pedido_id: pedidoId,
                direccion_entrega: direccion,
                productos: productosModificados
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Pedido actualizado correctamente');
            cerrarModalPedido();
            cargarUltimoPedido(); // Recargar pedido
        } else {
            alert('Error: ' + (data.msg || 'No se pudo actualizar el pedido'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar el pedido');
    }
}

// ========== EDICIÓN DE DIRECCIÓN ==========

let direccionEditando = false;

function editarDireccion() {
    const input = document.getElementById('direccionInput');
    const btnGuardar = document.getElementById('btnGuardarDireccion');
    
    if (!direccionEditando) {
        // Activar modo edición
        input.removeAttribute('readonly');
        input.focus();
        input.select();
        btnGuardar.style.display = 'block';
        direccionEditando = true;
    }
}

async function guardarDireccion() {
    const input = document.getElementById('direccionInput');
    const nuevaDireccion = input.value.trim();
    
    if (!nuevaDireccion) {
        alert('La dirección no puede estar vacía');
        return;
    }
    
    try {
        const response = await fetch(`${window.BASE_URL}/controller/actualizarDireccion.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                direccion: nuevaDireccion
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Dirección actualizada correctamente');
            input.setAttribute('readonly', 'readonly');
            document.getElementById('btnGuardarDireccion').style.display = 'none';
            direccionEditando = false;
        } else {
            alert('Error: ' + (data.msg || 'No se pudo actualizar la dirección'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar la dirección');
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalModificarPedido')?.addEventListener('click', (e) => {
    if (e.target.id === 'modalModificarPedido') {
        cerrarModalPedido();
    }
});

// Cerrar modal con tecla ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        cerrarModalPedido();
    }
});

// ========== GESTIÓN DE PRODUCTOS DEL PEDIDO ==========

// Eliminar producto del pedido
function eliminarProducto(index) {
    if (confirm('¿Estás seguro de eliminar este producto del pedido?')) {
        pedidoActual.productos.splice(index, 1);
        abrirModalPedido(); // Recargar el modal
    }
}

// Agregar nuevo producto al pedido
function agregarProducto() {
    // Aquí podrías abrir un modal para seleccionar productos
    // Por ahora, agregamos un producto de ejemplo
    const nuevoProducto = {
        nombre_producto: 'Pizza Margherita',
        cantidad: 1
    };
    
    pedidoActual.productos.push(nuevoProducto);
    abrirModalPedido(); // Recargar el modal
}

// Cancelar pedido
async function cancelarPedido() {
    if (!pedidoActual) {
        alert('No hay pedido para cancelar');
        return;
    }
    
    if (!confirm('¿Estás seguro de cancelar este pedido?')) {
        return;
    }
    
    try {
        const response = await fetch(`${window.BASE_URL}/controller/cancelarPedido.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                pedido_id: pedidoActual.ID_Pedido
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Pedido cancelado correctamente');
            cargarUltimoPedido(); // Recargar pedido
        } else {
            alert('Error: ' + (data.msg || 'No se pudo cancelar el pedido'));
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cancelar el pedido');
    }
}

