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
            mostrarError('No se pudo cargar el pedido');
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
        mostrarError('Error al cargar el pedido');
    }
}

// Renderizar el pedido en la interfaz
function renderizarPedido(pedido) {
    const orderCard = document.getElementById('orderCard');
    
    const estadoClass = pedido.estado_pedido.toLowerCase().replace(/ /g, '-');
    
    orderCard.innerHTML = `
        <h3 class="order-header">Último pedido:</h3>
        
        <div class="order-section">
            <div class="section-header">
                <span class="section-title">Comida</span>
                <span class="status-badge ${estadoClass}">${pedido.estado_pedido}</span>
                <button class="edit-pedido-btn" onclick="abrirModalPedido()" title="Editar pedido">
                    <svg class="edit-svg" viewBox="0 0 24 24">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                    </svg>
                </button>
            </div>
            
            <ul class="menu-items">
                ${pedido.productos.map(prod => `
                    <li class="menu-item">${prod.nombre_producto} x${prod.cantidad} - $${prod.subtotal}</li>
                `).join('')}
            </ul>
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #ddd;">
                <strong>Total: $${pedido.total}</strong>
            </div>
        </div>
    `;
}

// Mostrar mensaje cuando no hay pedidos
function mostrarNoPedido() {
    const orderCard = document.getElementById('orderCard');
    orderCard.innerHTML = `
        <h3 class="order-header">Último pedido:</h3>
        <p class="no-pedido">Aún no has realizado ningún pedido. ¡Haz tu primer pedido en la tienda!</p>
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
    
    // Llenar tabla de productos
    const tableBody = document.getElementById('pedidoItemsTable');
    tableBody.innerHTML = pedidoActual.productos.map(prod => `
        <tr>
            <td>${prod.nombre_producto}</td>
            <td>x${prod.cantidad}</td>
            <td>$${prod.subtotal}</td>
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
    const estado = document.getElementById('estadoPedido').value;
    const comentario = document.getElementById('comentarioPedido').value;
    const direccion = document.getElementById('direccionPedido').value;
    
    if (!direccion.trim()) {
        alert('La dirección es obligatoria');
        return;
    }
    
    try {
        const response = await fetch(`${window.BASE_URL}/controller/actualizarPedido.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                pedido_id: pedidoId,
                estado_pedido: estado,
                notas: comentario,
                direccion_entrega: direccion
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

