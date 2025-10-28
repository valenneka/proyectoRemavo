// Esta función agrega productos al carrito cuando el usuario hace clic en el botón
async function agregarAlCarrito(productId, cantidad = 1) {
    try {
        const response = await fetch(window.BASE_URL + '/controller/carrito.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'add',
                ID_Producto: productId,
                cantidad: cantidad
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            console.log('Respuesta del carrito:', { status: response.status, data: data });
            if (response.status === 401) {
                console.warn('No autenticado para carrito');
                // Intentar redirigir al login pero solo si estamos en la tienda
                if (!window.location.pathname.includes('carrito.php')) {
                    alert('Debes iniciar sesión para agregar productos al carrito');
                    window.location.href = window.BASE_URL + '/vista/public/login.php';
                }
                return;
            }
            console.error('Error en carrito:', data.msg);
            // No mostrar alerta, solo log
            return;
        }

        // Mostrar notificación de éxito
        mostrarNotificacion(`¡Producto agregado al carrito!`, 'success');

        // Actualizar badge del carrito si existe
        actualizarBadgeCarrito(data.cantidad_items);

    } catch (error) {
        console.error('Error agregando al carrito:', error);
    }
}

// Aquí quito productos del carrito cuando el usuario hace clic en eliminar
async function removerDelCarrito(productId) {
    try {
        const response = await fetch(window.BASE_URL + '/controller/carrito.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'remove',
                ID_Producto: productId
            })
        });

        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + (data.msg || 'No se pudo remover del carrito'));
            return;
        }

        mostrarNotificacion('Producto removido del carrito', 'info');
        actualizarBadgeCarrito(data.cantidad_items);
        actualizarVistaCarrito();

    } catch (error) {
        console.error('Error removiendo del carrito:', error);
    }
}

// Cambio la cantidad de productos en el carrito cuando el usuario modifica el número
async function actualizarCantidadCarrito(productId, cantidad) {
    try {
        const response = await fetch(window.BASE_URL + '/controller/carrito.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'update',
                ID_Producto: productId,
                cantidad: cantidad
            })
        });

        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + (data.msg || 'No se pudo actualizar la cantidad'));
            return;
        }

        actualizarBadgeCarrito(data.cantidad_items);
        actualizarVistaCarrito();

    } catch (error) {
        console.error('Error actualizando cantidad:', error);
    }
}

// Obtengo todos los productos que están en el carrito del usuario
async function obtenerCarrito() {
    try {
        const response = await fetch(window.BASE_URL + '/controller/carrito.php', {
            method: 'GET',
            credentials: 'include'
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            if (response.status === 401) {
                return null; // No autenticado
            }
            console.error('Error al obtener carrito:', data.msg);
            return null;
        }

        return data;

    } catch (error) {
        console.error('Error obteniendo carrito:', error);
        return null;
    }
}

// Borro todo el carrito cuando el usuario confirma que quiere vaciarlo
async function vaciarCarrito() {
    if (!confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
        return;
    }

    try {
        const response = await fetch(window.BASE_URL + '/controller/carrito.php', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'clear'
            })
        });

        const data = await response.json();

        if (!data.success) {
            alert('Error: ' + (data.msg || 'No se pudo vaciar el carrito'));
            return;
        }

        mostrarNotificacion('Carrito vaciado', 'info');
        actualizarBadgeCarrito(0);
        actualizarVistaCarrito();

    } catch (error) {
        console.error('Error vaciando carrito:', error);
    }
}

// Actualizo el número que aparece en el ícono del carrito en la barra de navegación
function actualizarBadgeCarrito(cantidad) {
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        if (cantidad > 0) {
            badge.textContent = cantidad;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Refresco toda la página del carrito para mostrar los productos actualizados
async function actualizarVistaCarrito() {
    const cartTable = document.querySelector('.cart-table');
    const summaryPanel = document.querySelector('.summary-panel');

    if (!cartTable || !summaryPanel) return; // No estamos en la página de carrito

    const cart = await obtenerCarrito();

    if (!cart) {
        cartTable.innerHTML = '<p style="padding: 20px; text-align: center; color: #999;">Debes iniciar sesión</p>';
        summaryPanel.innerHTML = '<p>Debes iniciar sesión para ver tu carrito</p>';
        return;
    }

    if (cart.items.length === 0) {
        cartTable.innerHTML = '<p style="padding: 20px; text-align: center; color: #999;">Tu carrito está vacío</p>';
        summaryPanel.innerHTML = `
            <h2 class="summary-title">Resumen del Pedido</h2>
            <div class="summary-total">
                <span>Carrito Vacío</span>
                <span class="price">$0</span>
            </div>
            <a href="${window.BASE_URL}/vista/public/tienda.php" class="continue-btn">Seguir Comprando</a>
        `;
        return;
    }

    // Renderizar tabla de carrito
    let cartHTML = '<div class="table-header"><span>Producto</span><span>Precio</span><span>Cantidad</span><span>Subtotal</span><span>Acciones</span></div>';

    cart.items.forEach(item => {
        // Convertir ruta de imagen a URL absoluta
        let imagenURL = item.imagenURL || '';
        if (imagenURL && !imagenURL.startsWith('http') && !imagenURL.startsWith('data:')) {
            if (!imagenURL.startsWith('/')) {
                imagenURL = '/' + imagenURL;
            }
            imagenURL = window.BASE_URL + imagenURL;
        }

        cartHTML += `
            <div class="cart-item">
                <div class="product-info">
                    <img src="${imagenURL}" alt="${item.nombre_producto}" class="product-image">
                    <span class="product-name">${item.nombre_producto}</span>
                </div>
                <div class="product-price">$ ${parseFloat(item.precio_unitario).toFixed(2)}</div>
                <div class="product-quantity">
                    <input type="number" min="1" value="${item.cantidad}"
                        onchange="actualizarCantidadCarrito(${item.ID_Producto}, this.value)">
                </div>
                <div class="product-subtotal">$ ${parseFloat(item.subtotal).toFixed(2)}</div>
                <div class="product-actions">
                    <button onclick="removerDelCarrito(${item.ID_Producto})" class="btn-eliminar">
                        <img src="${window.BASE_URL}/images/trash.svg" alt="Eliminar">
                    </button>
                </div>
            </div>
        `;
    });

    cartTable.innerHTML = cartHTML;

    // Renderizar resumen
    const itemCount = cart.items.length;
    const itemsText = itemCount === 1 ? 'item' : 'items';

    let summaryHTML = `<h2 class="summary-title">Resumen del Pedido</h2>`;

    cart.items.forEach(item => {
        summaryHTML += `
            <div class="summary-item">
                <span>${item.nombre_producto} x${item.cantidad}</span>
                <span class="price">$ ${parseFloat(item.subtotal).toFixed(2)}</span>
            </div>
        `;
    });

    summaryHTML += `
        <div class="summary-total">
            <span>Coste Total</span>
            <span class="price">$ ${parseFloat(cart.total).toFixed(2)}</span>
        </div>
        <button class="btn-vaciar-carrito" onclick="vaciarCarrito()">Vaciar Carrito</button>
    `;

    if (window.location.pathname.includes('carrito.php')) {
        summaryHTML += `
            <button class="continue-btn" onclick="abrirCheckout()">Continuar al Pago</button>
        `;
    }

    summaryPanel.innerHTML = summaryHTML;
}

// Esta es otra forma de llamar a la función anterior, la uso en algunos lugares
async function cargarCarrito() {
    await actualizarVistaCarrito();
}

// Muestro mensajes al usuario cuando pasa algo (éxito, error, etc.)
function mostrarNotificacion(mensaje, tipo = 'info') {
    const notif = document.createElement('div');
    notif.className = `notificacion notificacion-${tipo}`;
    notif.textContent = mensaje;
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${tipo === 'success' ? '#4CAF50' : tipo === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        border-radius: 4px;
        z-index: 10000;
        animation: slideIn 0.3s ease-in-out;
    `;

    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// Cuando carga la página del carrito, automáticamente muestro los productos
if (document.querySelector('.cart-table')) {
    actualizarVistaCarrito();
}

// Aquí agrego los estilos CSS para que las animaciones se vean bien
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .product-quantity input {
        width: 60px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn-vaciar-carrito {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        background: #ff6b35;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-vaciar-carrito:hover {
        background: #e55a24;
    }
`;
document.head.appendChild(style);
