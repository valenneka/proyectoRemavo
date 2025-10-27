// No mantenemos arrays locales como fuente de verdad. Consultamos al servidor cuando hace falta.
// Variable para almacenar la pizza seleccionada (objeto completo)
let pizzaSeleccionada = null;
let indicePizzaEditando = null; // todavía útil para UI temporal
let imagenTemporal = null;
let productosFamiliaTemporal = [];
// Estado actual de la familia mostrada en el encabezado
let currentFamilyName = 'Pizza';
let currentFamilyProducts = [];
// Flag para saber si estamos editando una familia existente
let editingFamily = false;
// id de familia en edición (null si es creación)
let editingFamilyId = null;

// Helpers que consultan el backend y normalizan datos
async function fetchProductos() {
    try {
        console.log('📡 Buscando productos desde:', window.BASE_URL + '/controller/productos.php');
        const res = await fetch(window.BASE_URL + '/controller/productos.php', {
            credentials: 'include' // Incluir cookies de sesión
        });

        console.log('📥 Response status:', res.status);
        const text = await res.text();
        console.log('📥 Response text (primeros 200 chars):', text.substring(0, 200));

        if (!text || text.trim() === '') {
            console.warn('⚠️ Respuesta vacía de productos.php');
            return [];
        }

        let j;
        try {
            j = JSON.parse(text);
        } catch (parseErr) {
            console.error('❌ Error parseando JSON en fetchProductos:', parseErr.message);
            console.error('❌ Texto:', text.substring(0, 300));
            throw new Error('Respuesta inválida del servidor: ' + text.substring(0, 100));
        }

        console.log('✅ JSON parseado en fetchProductos:', j);

        if (!j.success) {
            console.warn('⚠️ Servidor devolvió success: false, msg:', j.msg);
            return [];
        }

        if (!j.productos || !Array.isArray(j.productos)) {
            console.warn('⚠️ No hay array de productos en la respuesta');
            return [];
        }

        console.log('✅ Encontrados', j.productos.length, 'productos');
        return j.productos.map(p => {
            let imgURL = p.imagenURL || p.imagen || '';
            // Corregir rutas de imágenes que empiezan con /images/ → images/
            if (imgURL.startsWith('/images/')) {
                imgURL = imgURL.substring(1); // Remover la barra inicial
            }
            // Si la ruta es relativa, hacerla absoluta respecto a BASE_URL
            if (imgURL && !imgURL.startsWith('http') && !imgURL.startsWith('data:')) {
                imgURL = window.BASE_URL + '/' + imgURL;
            }
            return {
                id: p.ID_Producto,
                nombre: p.nombre_producto || p.nombre || '',
                precio: p.precio_unitario || p.precio || '',
                imagen: imgURL,
                descripcion: p.descripcion || ''
            };
        });
    } catch (err) {
        console.error('❌ Error crítico en fetchProductos:', err);
        throw err; // Re-lanzar para que el caller sepa que hubo error
    }
}

async function fetchFamilias() {
    try {
        console.log('📡 Buscando familias desde:', window.BASE_URL + '/controller/familias.php');
        const res = await fetch(window.BASE_URL + '/controller/familias.php', {
            credentials: 'include' // Incluir cookies de sesión
        });

        console.log('📥 Response status (familias):', res.status);
        const text = await res.text();
        console.log('📥 Response text familias (primeros 200 chars):', text.substring(0, 200));

        let j;
        try {
            j = JSON.parse(text);
        } catch (parseErr) {
            console.error('❌ Error parseando JSON en fetchFamilias:', parseErr.message);
            throw parseErr;
        }

        if (!j.success) {
            console.warn('⚠️ fetchFamilias: success = false');
            return [];
        }

        console.log('✅ Encontradas', j.familias.length, 'familias');
        const familias = j.familias.map(f => ({ id: f.ID_Familia, name: f.nombre, descripcion: f.descripcion }));

        // poblar productos por familia
        for (let f of familias) {
            try {
                const r = await fetch(window.BASE_URL + '/controller/contiene.php?familia_id=' + f.id, {
                    credentials: 'include'
                });
                const jr = await r.json();
                f.products = jr.success ? jr.productos.map(p => p.ID_Producto) : [];
                console.log(`✅ Familia ${f.name}: ${f.products.length} productos`);
            } catch (err) {
                console.error('❌ Error cargando productos de familia', f.id, ':', err);
                f.products = [];
            }
        }
        return familias;
    } catch (err) {
        console.error('❌ Error crítico en fetchFamilias:', err);
        return [];
    }
}

        // Función para generar las tarjetas de pizzas consultando al servidor cada vez.
        // Si se pasa filterList (array de ids o nombres), solo muestra esas pizzas.
        async function generarPizzas(filterList) {
            const carrusel = document.getElementById('carruselPizzas');
            carrusel.innerHTML = ''; // Limpiar carrusel antes de regenerar

            const productos = await fetchProductos();

            // Determinar lista a renderizar (IDs o nombres).
            const filterListVal = Array.isArray(filterList) ? filterList : (currentFamilyProducts && currentFamilyProducts.length ? currentFamilyProducts : null);
            let pizzasParaRender;
            if (filterListVal) {
                const areIds = filterListVal.every(v => Number.isInteger(v));
                pizzasParaRender = areIds ? productos.filter(p => filterListVal.includes(p.id)) : productos.filter(p => filterListVal.includes(p.nombre));
            } else {
                pizzasParaRender = productos;
            }

            pizzasParaRender.forEach((pizza) => {
                const tarjeta = document.createElement('div');
                tarjeta.className = 'tarjeta-pizza';

                tarjeta.innerHTML = `
                    <div class="etiqueta-editar" data-id="${pizza.id}">✏️</div>
                    <div class="contenedor-imagen" data-id="${pizza.id}">
                        <img src="${pizza.imagen}" alt="${pizza.nombre}" class="imagen-pizza">
                    </div>
                    <div class="informacion-pizza" data-id="${pizza.id}">
                        <div class="nombre-pizza">${pizza.nombre}</div>
                        <div class="precio-pizza">$ ${pizza.precio}</div>
                    </div>
                `;

                // Delegación de eventos: click en imagen/info abre modal por id
                tarjeta.querySelectorAll('.contenedor-imagen, .informacion-pizza').forEach(el => {
                    el.addEventListener('click', (e) => {
                        const id = parseInt(el.dataset.id, 10);
                        abrirModalById(id);
                    });
                });

                const editarEl = tarjeta.querySelector('.etiqueta-editar');
                editarEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = parseInt(editarEl.dataset.id, 10);
                    abrirModalEditarById(id);
                });

                carrusel.appendChild(tarjeta);
            });
        }

        // Función para desplazar el carrusel
        function desplazarCarrusel(direccion) {
            const carrusel = document.getElementById('carruselPizzas');
            const cantidadDesplazamiento = carrusel.offsetWidth * 0.8;
            carrusel.scrollBy({
                left: direccion * cantidadDesplazamiento,
                behavior: 'smooth'
            });
        }

        // Abrir modal por productId (consulta al servidor y muestra datos)
        async function abrirModalById(productId) {
            const productos = await fetchProductos();
            const producto = productos.find(p => p.id === productId);
            if (!producto) return;
            pizzaSeleccionada = producto;
            const modal = document.getElementById('modalPizza');
            document.getElementById('imagenModal').src = pizzaSeleccionada.imagen;
            document.getElementById('nombreModal').textContent = pizzaSeleccionada.nombre;
            document.getElementById('descripcionModal').textContent = pizzaSeleccionada.descripcion;
            document.getElementById('precioModal').textContent = '$ ' + pizzaSeleccionada.precio;
            modal.classList.add('activo');
        }

        // Función para cerrar el modal
        function cerrarModal() {
            const modal = document.getElementById('modalPizza');
            modal.classList.remove('activo');
        }

        // Función para simular la compra
        function comprarPizza() {
            alert(`¡Pizza "${pizzaSeleccionada.nombre}" agregada al carrito!\nPrecio: $ ${pizzaSeleccionada.precio}`);
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
                desplazarCarrusel(-1);
            } else if (evento.key === 'ArrowRight') {
                desplazarCarrusel(1);
            } else if (evento.key === 'Escape') {
                cerrarModal();
            }
        });

        // Cargar datos iniciales desde backend y renderizar
        async function loadData() {
            try {
                const familias = await fetchFamilias();
                // render families con carrouseles
                renderFamilies(familias);
            } catch (err) {
                console.error('Error cargando datos', err);
            }
        }

    // Ejecutar carga
    loadData();

        // Abrir modal de edición por productId
        async function abrirModalEditarById(productId) {
            indicePizzaEditando = productId; // usamos id en lugar de índice
            const productos = await fetchProductos();
            const pizza = productos.find(p => p.id === productId);
            if (!pizza) return;
            const modal = document.getElementById('modalEditar');
            // Actualizar título del modal
            document.querySelector('.titulo-modal-editar').textContent = `Estás modificando (${pizza.nombre})`;
            // Llenar los campos del formulario
            document.getElementById('editarNombre').value = pizza.nombre;
            document.getElementById('editarPrecio').value = pizza.precio;
            document.getElementById('editarDescripcion').value = pizza.descripcion;
            // Mostrar imagen actual
            const vistaPrevia = document.getElementById('vistaPrevia');
            vistaPrevia.src = pizza.imagen;
            vistaPrevia.style.display = 'block';
            imagenTemporal = null;
            modal.classList.add('activo');
        }

        // Función para cerrar el modal de edición
        function cerrarModalEditar() {
            const modal = document.getElementById('modalEditar');
            modal.classList.remove('activo');
            indicePizzaEditando = null;
            imagenTemporal = null;
        }

        // Función para cargar imagen
        function cargarImagen(evento) {
            const archivo = evento.target.files[0];
            if (archivo) {
                const lector = new FileReader();
                lector.onload = function(e) {
                    imagenTemporal = e.target.result;
                    const vistaPrevia = document.getElementById('vistaPrevia');
                    vistaPrevia.src = imagenTemporal;
                    vistaPrevia.style.display = 'block';
                };
                lector.readAsDataURL(archivo);
            }
        }

        // Función para guardar cambios de producto. Esto intenta llamar al backend; si falla, informa.
        async function guardarCambios() {
            if (indicePizzaEditando !== null) {
                const nombre = document.getElementById('editarNombre').value;
                const precio = document.getElementById('editarPrecio').value;
                const descripcion = document.getElementById('editarDescripcion').value;
                // Validar
                if (!nombre || !precio || !descripcion) {
                    alert('Por favor completa todos los campos');
                    return;
                }

                // Intentar enviar update al backend (endpoint esperado: controller/productos.php?action=update)
                try {
                    const payload = { action: 'update', id: indicePizzaEditando, nombre_producto: nombre, precio_unitario: precio, descripcion };
                    // si hay imagenTemporal, enviarla como base64 (dependiendo del backend)
                    if (imagenTemporal) payload.imagenBase64 = imagenTemporal;
                    const resp = await fetch(window.BASE_URL + '/controller/productos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const j = await resp.json();
                    if (!j.success) throw new Error(j.msg || 'Error actualizando producto');
                    // refrescar UI
                    await loadData();
                    alert('¡Cambios guardados exitosamente!');
                    cerrarModalEditar();
                } catch (err) {
                    console.error(err);
                    alert('No se pudo guardar en el servidor: ' + err.message + '. Asegúrate de que el endpoint exista.');
                }
            }
        }

        // Función para eliminar pizza (intenta llamar al backend)
        async function eliminarPizza() {
            if (indicePizzaEditando !== null) {
                const confirmacion = confirm('¿Estás seguro de eliminar este producto?');
                if (!confirmacion) return;
                try {
                    const resp = await fetch(window.BASE_URL + '/controller/productos.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'delete', id: indicePizzaEditando })
                    });
                    const j = await resp.json();
                    if (!j.success) throw new Error(j.msg || 'Error eliminando producto');
                    await loadData();
                    alert('Producto eliminado exitosamente');
                    cerrarModalEditar();
                } catch (err) {
                    console.error(err);
                    alert('No se pudo eliminar en el servidor: ' + err.message + '. Asegúrate de que el endpoint exista.');
                }
            }
        }

        // Función para abrir modal de nueva familia
        function abrirModalNuevaFamilia() {
            console.log('🔍 Abriendo modal nueva familia...');
            const modal = document.getElementById('modalNuevaFamilia');
            if (!modal) {
                console.error('❌ Modal no encontrado:', 'modalNuevaFamilia');
                return;
            }

            document.getElementById('nombreFamilia').value = '';
            // indicamos que es creación
            editingFamily = false;
            editingFamilyId = null;
            productosFamiliaTemporal = [];

            // Abrir modal inmediatamente
            modal.classList.add('activo');
            console.log('✅ Modal abierto');

            // Cargar productos de forma asincrónica en background
            console.log('📡 Iniciando carga de productos en background...');
            fetchProductos()
                .then(productos => {
                    console.log('✅ Productos cargados exitosamente:', productos.length, 'items');
                    productosFamiliaTemporal = productos.map(p => p.id);
                    actualizarListaProductosFamilia();
                })
                .catch(err => {
                    console.error('❌ Error cargando productos:', err);
                    console.error('❌ Stack:', err.stack);
                    alert('Error cargando productos: ' + err.message);
                });
        }

        // Función para cerrar modal de familia
        function cerrarModalFamilia() {
            const modal = document.getElementById('modalNuevaFamilia');
            modal.classList.remove('activo');
            productosFamiliaTemporal = [];
        }

        // Función para agregar producto a la familia
        function agregarProductoFamilia() {
            // Agregar producto manual eliminado; se gestionan desde los checkboxes
            return;
        }

        // (Select para agregar productos eliminado por diseño)

        // Función para actualizar la lista visual de productos
        async function actualizarListaProductosFamilia() {
            const lista = document.getElementById('listaProductosFamilia');
            lista.innerHTML = '';
            // Mostrar todos los productos con checkbox; marcar los que estén en productosFamiliaTemporal (IDs)
            const productos = await fetchProductos();
            productos.forEach(prod => {
                const pid = prod.id;
                const row = document.createElement('div');
                row.className = 'producto-item';
                row.style.display = 'flex';
                row.style.alignItems = 'center';
                row.style.justifyContent = 'space-between';
                row.style.gap = '8px';
                row.style.padding = '8px';
                row.style.borderBottom = '1px solid #eee';

                const checked = productosFamiliaTemporal.includes(pid);

                const leftPart = document.createElement('label');
                leftPart.style.display = 'flex';
                leftPart.style.alignItems = 'center';
                leftPart.style.gap = '8px';
                leftPart.style.flex = '1';
                leftPart.innerHTML = `
                    <input type="checkbox" data-id="${pid}" ${checked ? 'checked' : ''}>
                    <span>${prod.nombre} ($ ${prod.precio})</span>
                `;

                const editBtn = document.createElement('button');
                editBtn.textContent = '✏️';
                editBtn.style.padding = '4px 8px';
                editBtn.style.cursor = 'pointer';
                editBtn.style.border = '1px solid #ccc';
                editBtn.style.borderRadius = '4px';
                editBtn.style.backgroundColor = '#f0f0f0';
                editBtn.onclick = (e) => {
                    e.stopPropagation();
                    abrirModalEditarById(pid);
                };

                row.appendChild(leftPart);
                row.appendChild(editBtn);
                lista.appendChild(row);
            });
        }

        // Función para eliminar producto de la lista temporal
        function eliminarProductoFamilia(indice) {
            productosFamiliaTemporal.splice(indice, 1);
            actualizarListaProductosFamilia();
        }

        // Función para guardar la nueva familia
        function guardarFamilia() {
            const nombreFamilia = document.getElementById('nombreFamilia').value;

            if (!nombreFamilia || nombreFamilia.trim() === '') {
                alert('Por favor ingresa un nombre para la familia');
                return;
            }

            // Ya no requerimos que haya productos antes de guardar
            // Ahora puedes crear productos DESPUÉS de guardar la familia

            // recoger productos seleccionados desde la lista de checkboxes
            const checkboxes = Array.from(document.querySelectorAll('#listaProductosFamilia input[type="checkbox"]'));
            const selectedIds = checkboxes.filter(cb => cb.checked).map(cb => parseInt(cb.dataset.id));

            (async () => {
                try {
                    // crear o actualizar familia en backend
                    let famId = null;
                    if (editingFamily && editingFamilyId !== null) {
                        // update
                        const resp = await fetch(window.BASE_URL + '/controller/familias.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'update', id: editingFamilyId, nombre: nombreFamilia, descripcion: '' })
                        });
                        const j = await resp.json();
                        if (!j.success) throw new Error(j.msg || 'Error actualizando familia');
                        famId = editingFamilyId;
                    } else {
                        // create - familia nueva
                        const resp = await fetch(window.BASE_URL + '/controller/familias.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'create', nombre: nombreFamilia, descripcion: '' })
                        });
                        const j = await resp.json();
                        if (!j.success) throw new Error(j.msg || 'Error creando familia');
                        famId = j.id;

                        // IMPORTANTE: Asignar el ID de familia a editingFamilyId para que los productos nuevos se asocien
                        editingFamilyId = famId;
                        editingFamily = true;
                    }

                    // asignar productos EXISTENTES a la familia (actualiza productos.ID_Familia)
                    if (selectedIds.length > 0) {
                        const resp2 = await fetch(window.BASE_URL + '/controller/contiene.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'setProductsForFamily', familia_id: famId, productos: selectedIds })
                        });
                        const j2 = await resp2.json();
                        if (!j2.success) throw new Error(j2.msg || 'Error asignando productos');
                    }

                    alert('Familia guardada con éxito. Ahora puedes agregar más productos.');
                    // NO cerrar el modal para que puedan seguir agregando productos
                    // cerrarModalFamilia();
                } catch (err) {
                    console.error(err);
                    alert('Error guardando familia: ' + err.message);
                }
            })();
        }

        // Función para eliminar familia
        function eliminarFamilia() {
            const confirmacion = confirm('¿Estás seguro de eliminar esta familia?');
            if (confirmacion) {
                alert('Familia eliminada');
                cerrarModalFamilia();
            }
        }

        // Abrir modal para editar una familia existente (desde el header)
        function abrirModalEditarFamilia(datos) {
            // Si se pasan datos, usar; si no, usar valores de ejemplo o vacíos
            const nombre = (datos && datos.nombre) ? datos.nombre : currentFamilyName || 'Nombre de la familia';
            const productos = (datos && Array.isArray(datos.productos)) ? datos.productos : (currentFamilyProducts.length ? currentFamilyProducts : []);

            const modal = document.getElementById('modalNuevaFamilia');
            document.getElementById('nombreFamilia').value = nombre;
            productosFamiliaTemporal = [...productos];
            actualizarListaProductosFamilia();
            // No actualizamos currentFamilyName aquí: solo al guardar
            // indicamos que estamos editando
            editingFamily = true;
            // si se pasaron datos con id, setear editingFamilyId (puede ser number o string)
            editingFamilyId = (datos && datos.id) ? (typeof datos.id === 'number' ? datos.id : parseInt(datos.id)) : null;
            console.log('✅ editingFamilyId asignado:', editingFamilyId);
            modal.classList.add('activo');
        }

        // Renderizar todas las familias con carrouseles de productos
        async function renderFamilies(familias) {
            const container = document.getElementById('familiesContainer');
            if (!container) return;
            container.innerHTML = '';
            // usar familias pasadas o pedir al servidor
            const fams = familias || await fetchFamilias();
            const productos = await fetchProductos();

            // Renderizar como lista de tarjetas
            if (fams.length === 0) {
                container.innerHTML = '<div class="sin-familias-msg">No hay menús creados aún. Crea uno haciendo click en el botón +</div>';
                return;
            }

            fams.forEach((fam, idx) => {
                // Obtener productos de esta familia
                const productosFamilia = productos.filter(p => fam.products.includes(p.id));

                // Crear tarjeta de familia
                const tarjeta = document.createElement('div');
                tarjeta.className = 'tarjeta-familia';

                // Contenido HTML con carrousel de productos
                let carouselHTML = '';
                if (productosFamilia.length === 0) {
                    carouselHTML = '<div class="sin-productos-familia">Sin productos asignados a este menú</div>';
                } else {
                    carouselHTML = `
                        <div class="familia-carrusel-container">
                            <button class="familia-btn-nav anterior" onclick="desplazarCarruselFamilia(this, -1)">‹</button>
                            <div class="familia-carrusel" id="carruselFamilia${idx}">
                                ${productosFamilia.map(p => `
                                    <div class="familia-tarjeta-producto">
                                        <div class="familia-producto-editar" onclick="abrirModalEditarById(${p.id}); event.stopPropagation();">✏️</div>
                                        <div class="familia-producto-imagen" onclick="abrirModalById(${p.id})">
                                            <img src="${p.imagen}" alt="${p.nombre}" class="familia-producto-img">
                                        </div>
                                        <div class="familia-producto-info" onclick="abrirModalById(${p.id})">
                                            <div class="familia-producto-nombre">${p.nombre}</div>
                                            <div class="familia-producto-precio">$ ${p.precio}</div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <button class="familia-btn-nav siguiente" onclick="desplazarCarruselFamilia(this, 1)">›</button>
                        </div>
                    `;
                }

                tarjeta.innerHTML = `
                    <div class="familia-header">
                        <span class="familia-nombre">${fam.name}</span>
                        <span class="familia-cantidad">${productosFamilia.length} productos</span>
                    </div>
                    ${carouselHTML}
                    <div class="familia-acciones">
                        <button class="btn-familia btn-editar-familia" data-id="${fam.id}">✏️ Editar</button>
                        <button class="btn-familia btn-eliminar-familia" data-id="${fam.id}">🗑️ Eliminar</button>
                    </div>
                `;

                container.appendChild(tarjeta);

                // Listeners para botones
                tarjeta.querySelector('.btn-editar-familia').addEventListener('click', () => {
                    abrirModalEditarFamilia({ nombre: fam.name, productos: fam.products, id: fam.id });
                });

                tarjeta.querySelector('.btn-eliminar-familia').addEventListener('click', () => {
                    const confirmacion = confirm(`¿Estás seguro de eliminar la familia "${fam.name}"?`);
                    if (confirmacion) {
                        eliminarFamiliaById(fam.id);
                    }
                });
            });
        }

        // Función para desplazar carrousel de familia
        function desplazarCarruselFamilia(boton, direccion) {
            // Buscar el carrusel más cercano
            const carrusel = boton.parentElement.querySelector('.familia-carrusel');
            if (carrusel) {
                const cantidadDesplazamiento = carrusel.offsetWidth * 0.8;
                carrusel.scrollBy({ left: direccion * cantidadDesplazamiento, behavior: 'smooth' });
            }
        }

        // Función para eliminar familia por ID
        async function eliminarFamiliaById(familiaId) {
            try {
                const resp = await fetch(window.BASE_URL + '/controller/familias.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: familiaId })
                });
                const j = await resp.json();
                if (!j.success) throw new Error(j.msg || 'Error eliminando familia');
                alert('Familia eliminada exitosamente');
                await loadData();
            } catch (err) {
                console.error(err);
                alert('Error eliminando familia: ' + err.message);
            }
        }

        // Desplazar un carrusel específico (DOM element)
        function desplazarCarruselEn(carrusel, direccion) {
            const cantidadDesplazamiento = carrusel.offsetWidth * 0.8;
            carrusel.scrollBy({ left: direccion * cantidadDesplazamiento, behavior: 'smooth' });
        }

        // ========== FUNCIONES PARA CREAR NUEVO PRODUCTO ==========
        let imagenTemporalCrear = null;

        // Función para abrir modal de crear producto
        function abrirModalCrearProducto() {
            const modal = document.getElementById('modalCrearProducto');
            
            // Limpiar campos
            document.getElementById('crearNombre').value = '';
            document.getElementById('crearPrecio').value = '';
            document.getElementById('crearDescripcion').value = '';
            document.getElementById('vistaPreviaCrear').style.display = 'none';
            imagenTemporalCrear = null;
            
            modal.classList.add('activo');
        }

        // Función para cerrar modal de crear producto
        function cerrarModalCrearProducto() {
            const modal = document.getElementById('modalCrearProducto');
            modal.classList.remove('activo');
            imagenTemporalCrear = null;
        }

        // Función para cargar imagen en crear producto
        function cargarImagenCrear(evento) {
            const archivo = evento.target.files[0];
            if (archivo) {
                const lector = new FileReader();
                lector.onload = function(e) {
                    imagenTemporalCrear = e.target.result;
                    const vistaPrevia = document.getElementById('vistaPreviaCrear');
                    vistaPrevia.src = imagenTemporalCrear;
                    vistaPrevia.style.display = 'block';
                };
                lector.readAsDataURL(archivo);
            }
        }

        // Función para crear nuevo producto
        async function crearNuevoProducto() {
            const nombre = document.getElementById('crearNombre').value.trim();
            const precio = document.getElementById('crearPrecio').value.trim();
            const descripcion = document.getElementById('crearDescripcion').value.trim();

            // Validar campos
            if (!nombre || !precio || !descripcion) {
                alert('Por favor completa todos los campos');
                return;
            }

            if (!imagenTemporalCrear) {
                alert('Por favor selecciona una imagen para el producto');
                return;
            }

            try {
                // Preparar payload para crear producto
                const payload = {
                    action: 'create',
                    nombre_producto: nombre,
                    precio_unitario: precio,
                    descripcion: descripcion,
                    imagenBase64: imagenTemporalCrear
                };

                // Si estamos editando una familia, asociar el producto a esa familia
                if (editingFamilyId !== null) {
                    payload.ID_Familia = editingFamilyId;
                }

                // Enviar al backend
                const resp = await fetch(window.BASE_URL + '/controller/productos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify(payload)
                });

                const j = await resp.json();
                
                if (!j.success) {
                    throw new Error(j.msg || 'Error creando producto');
                }

                alert('¡Producto creado exitosamente!');
                
                // Cerrar modal de crear producto
                cerrarModalCrearProducto();
                
                // Actualizar lista de productos en el modal de familia
                await actualizarListaProductosFamilia();
                
                // Si hay una familia en edición, marcar el producto recién creado
                if (j.id && editingFamilyId !== null) {
                    // Agregar el ID del nuevo producto a la lista temporal
                    if (!productosFamiliaTemporal.includes(j.id)) {
                        productosFamiliaTemporal.push(j.id);
                    }
                    // Actualizar la lista visual
                    await actualizarListaProductosFamilia();
                }

            } catch (err) {
                console.error('Error creando producto:', err);
                alert('Error creando producto: ' + err.message);
            }
        }

