// Sistema de carrouseles para tienda pública (READ-ONLY)
// No se pueden editar familias ni productos desde aquí

let pizzaSeleccionada = null;

// Función para obtener productos desde el servidor
async function fetchProductos() {
    const res = await fetch(window.BASE_URL + '/controller/productos.php');
    const j = await res.json();
    if (!j.success) return [];
    return j.productos.map(p => {
        let imagen = p.imagenURL || p.imagen || '';

        // Convertir ruta relativa a URL absoluta
        if (imagen && !imagen.startsWith('http') && !imagen.startsWith('data:')) {
            // Si no comienza con /, agregar /
            if (!imagen.startsWith('/')) {
                imagen = '/' + imagen;
            }
            // Agregar BASE_URL
            imagen = window.BASE_URL + imagen;
        }

        return {
            id: p.ID_Producto,
            nombre: p.nombre_producto || p.nombre || '',
            precio: p.precio_unitario || p.precio || '',
            imagen: imagen,
            descripcion: p.descripcion || ''
        };
    });
}

// Función para obtener familias desde el servidor
async function fetchFamilias() {
    const res = await fetch(window.BASE_URL + '/controller/familias.php');
    const j = await res.json();
    if (!j.success) return [];
    const familias = j.familias.map(f => ({ id: f.ID_Familia, name: f.nombre, descripcion: f.descripcion }));
    // Obtener productos por familia
    for (let f of familias) {
        try {
            const r = await fetch(window.BASE_URL + '/controller/contiene.php?familia_id=' + f.id);
            const jr = await r.json();
            f.products = jr.success ? jr.productos.map(p => p.ID_Producto) : [];
        } catch (err) {
            f.products = [];
        }
    }
    return familias;
}

// Abrir modal por productId
async function abrirModalById(productId) {
    const productos = await fetchProductos();
    const producto = productos.find(p => p.id === productId);
    if (!producto) return;
    pizzaSeleccionada = producto;
    const modal = document.getElementById('modalPizza');
    document.getElementById('imagenModal').src = pizzaSeleccionada.imagen;
    document.getElementById('nombreModal').textContent = pizzaSeleccionada.nombre;
    document.getElementById('descripcionModal').textContent = pizzaSeleccionada.descripcion;
    document.getElementById('precioModal').textContent = pizzaSeleccionada.precio;
    modal.classList.add('activo');
}

// Cerrar modal
function cerrarModal() {
    const modal = document.getElementById('modalPizza');
    modal.classList.remove('activo');
}

// Agregar al carrito desde el modal
function agregarAlCarritoDesdeModal() {
    if (!pizzaSeleccionada) return;
    agregarAlCarrito(pizzaSeleccionada.id, 1);
    cerrarModal();
}

// Cerrar modal al hacer clic fuera del contenido
document.getElementById('modalPizza').addEventListener('click', (evento) => {
    if (evento.target.id === 'modalPizza') {
        cerrarModal();
    }
});

// Soporte para navegación con teclado
document.addEventListener('keydown', (evento) => {
    if (evento.key === 'ArrowLeft') {
        // Desplazar primer carrusel si existe
        const primer = document.querySelector('.carrusel');
        if (primer) desplazarCarruselEn(primer, -1);
    } else if (evento.key === 'ArrowRight') {
        const primer = document.querySelector('.carrusel');
        if (primer) desplazarCarruselEn(primer, 1);
    } else if (evento.key === 'Escape') {
        cerrarModal();
    }
});

// Desplazar un carrusel específico
function desplazarCarruselEn(carrusel, direccion) {
    const cantidadDesplazamiento = carrusel.offsetWidth * 0.8;
    carrusel.scrollBy({ left: direccion * cantidadDesplazamiento, behavior: 'smooth' });
}

// Renderizar todas las familias como secciones con su propio carrusel
async function renderFamilies() {
    const container = document.getElementById('familiesContainer');
    const sinFamiliasMsg = document.getElementById('sinFamilias');

    if (!container) return;

    try {
        const familias = await fetchFamilias();
        const productos = await fetchProductos();

        if (familias.length === 0) {
            container.innerHTML = '';
            sinFamiliasMsg.style.display = 'block';
            return;
        }

        sinFamiliasMsg.style.display = 'none';
        container.innerHTML = '';

        familias.forEach((fam, idx) => {
            const section = document.createElement('section');
            section.className = 'contenedor-carrusel';
            section.innerHTML = `
                <div class="encabezado">
                    <div>
                        <div class="titulo">${fam.name}</div>
                        <div class="linea-naranja"></div>
                    </div>
                </div>
                <div class="envolvedor-carrusel">
                    <button class="boton-navegacion anterior">‹</button>
                    <div class="carrusel" id="carruselFamilia${idx}"></div>
                    <button class="boton-navegacion siguiente">›</button>
                </div>
            `;
            container.appendChild(section);

            // Llenar el carrusel con productos filtrados
            const carruselEl = section.querySelector('.carrusel');
            const productosFiltrados = productos.filter(p => fam.products.includes(p.id));

            if (productosFiltrados.length === 0) {
                carruselEl.innerHTML = '<p style="padding: 20px; color: #999;">No hay productos en esta familia.</p>';
            } else {
                productosFiltrados.forEach(pizza => {
                    const tarjeta = document.createElement('div');
                    tarjeta.className = 'tarjeta-pizza';
                    tarjeta.innerHTML = `
                        <div class="contenedor-imagen" data-id="${pizza.id}">
                            <img src="${pizza.imagen}" alt="${pizza.nombre}" class="imagen-pizza">
                            <button class="btn-agregar-carrito" onclick="agregarAlCarrito(${pizza.id}); event.stopPropagation();" title="Agregar al carrito">+</button>
                        </div>
                        <div class="informacion-pizza" data-id="${pizza.id}">
                            <div class="nombre-pizza">${pizza.nombre}</div>
                            <div class="precio-pizza">${pizza.precio}</div>
                        </div>
                    `;

                    // Listeners para abrir modal
                    tarjeta.querySelectorAll('.contenedor-imagen, .informacion-pizza').forEach(el => {
                        el.addEventListener('click', () => abrirModalById(parseInt(el.dataset.id, 10)));
                    });

                    carruselEl.appendChild(tarjeta);
                });
            }

            // Navegación específica por carrusel
            const btnPrev = section.querySelector('.anterior');
            const btnNext = section.querySelector('.siguiente');
            btnPrev.addEventListener('click', () => desplazarCarruselEn(carruselEl, -1));
            btnNext.addEventListener('click', () => desplazarCarruselEn(carruselEl, 1));
        });

    } catch (err) {
        console.error('Error cargando familias:', err);
        sinFamiliasMsg.style.display = 'block';
        sinFamiliasMsg.textContent = 'Error cargando los productos.';
    }
}

// Cargar datos iniciales
renderFamilies();
