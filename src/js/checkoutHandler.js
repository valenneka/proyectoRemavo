// Checkout Handler - Manejo de checkout y procesamiento de pedidos

// Función para seleccionar método de pago
function seleccionarMetodoPago(tipo, elemento) {
    // Remover selección de todos los métodos
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Marcar el método seleccionado
    elemento.classList.add('selected');
    
    // Marcar el radio button correspondiente
    const radio = elemento.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
    }
}

// Abrir modal de checkout
async function abrirCheckout() {
    const cart = await obtenerCarrito();

    if (!cart || cart.cantidad_items === 0) {
        alert('El carrito está vacío');
        return;
    }

    // Rellenar resumen del pedido
    const resumenContainer = document.getElementById('resumenPedido');
    resumenContainer.innerHTML = '';

    cart.items.forEach(item => {
        const resumenItem = document.createElement('div');
        resumenItem.className = 'resumen-item';
        resumenItem.innerHTML = `
            <div>
                <div class="resumen-item-name">${item.nombre_producto}</div>
                <div class="resumen-item-cantidad">Cantidad: ${item.cantidad}</div>
            </div>
            <div class="price">$${item.subtotal.toFixed(2)}</div>
        `;
        resumenContainer.appendChild(resumenItem);
    });

    // Actualizar total
    document.getElementById('totalCheckout').textContent = `$${cart.total.toFixed(2)}`;

    // Prellenar datos del usuario si están disponibles
    const usuario = window.usuarioData || {};
    if (usuario.telefono) {
        document.getElementById('telefono').value = usuario.telefono;
    }
    if (usuario.direccion) {
        document.getElementById('direccion').value = usuario.direccion;
    }

    // Mostrar modal
    document.getElementById('checkoutModal').classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

// Cerrar modal de checkout
function cerrarCheckout() {
    document.getElementById('checkoutModal').classList.remove('active');
    document.body.style.overflow = '';
    document.getElementById('checkoutForm').reset();
}

// Procesar pedido
async function procesarPedido(event) {
    event.preventDefault();

    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');

    // Deshabilitar botón para evitar doble envío
    submitBtn.disabled = true;
    submitBtn.textContent = 'Procesando...';

    // Obtener datos del formulario
    const metodoPagoSeleccionado = document.querySelector('input[name="metodo_pago"]:checked');
    const formData = {
        direccion: document.getElementById('direccion').value.trim(),
        telefono: document.getElementById('telefono').value.trim(),
        notas: document.getElementById('notas').value.trim(),
        metodo_pago: metodoPagoSeleccionado ? metodoPagoSeleccionado.value : 'Efectivo'
    };

    // Validar datos
    if (!formData.direccion || !formData.telefono) {
        alert('Por favor completa todos los campos requeridos');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Confirmar Pedido';
        return;
    }

    try {
        const response = await fetch(`${window.BASE_URL}/controller/procesarPedido.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            // Cerrar modal de checkout
            cerrarCheckout();

            // Mostrar modal de confirmación
            document.getElementById('numeroPedido').textContent = `#${data.ID_Pedido}`;
            document.getElementById('confirmacionModal').classList.add('active');

            // Limpiar carrito en la interfaz
            await cargarCarrito();

        } else {
            alert(data.msg || 'Error al procesar el pedido');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Confirmar Pedido';
        }

    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el pedido. Por favor intenta nuevamente.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Confirmar Pedido';
    }
}

// Cerrar modal de confirmación
function cerrarConfirmacion() {
    document.getElementById('confirmacionModal').classList.remove('active');
    document.body.style.overflow = '';

    // Redirigir al perfil o inicio
    window.location.href = `${window.BASE_URL}/vista/public/perfil.php`;
}

// Cerrar modals al hacer clic fuera
document.addEventListener('click', function(event) {
    const checkoutModal = document.getElementById('checkoutModal');
    const confirmacionModal = document.getElementById('confirmacionModal');

    if (event.target === checkoutModal) {
        cerrarCheckout();
    }

    if (event.target === confirmacionModal) {
        cerrarConfirmacion();
    }
});

// Cerrar modals con tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (document.getElementById('checkoutModal').classList.contains('active')) {
            cerrarCheckout();
        }
        if (document.getElementById('confirmacionModal').classList.contains('active')) {
            cerrarConfirmacion();
        }
    }
});
