// Landing Page - Sin Carruseles (Grid estático)

// Cargar pizzas populares al cargar la página
document.addEventListener('DOMContentLoaded', async () => {
    await cargarPizzasPopulares();
});

// Función para cargar pizzas populares desde la API
async function cargarPizzasPopulares() {
    try {
        const response = await fetch(`${window.BASE_URL}/controller/productos.php`);
        const data = await response.json();

        if (data.success && data.productos && data.productos.length > 0) {
            renderizarPizzas(data.productos);
        } else {
            // Si no hay productos, mostrar pizzas de ejemplo
            mostrarPizzasEjemplo();
        }
    } catch (error) {
        console.error('Error al cargar pizzas:', error);
        mostrarPizzasEjemplo();
    }
}

// Renderizar pizzas en el grid
function renderizarPizzas(pizzas) {
    const grid = document.getElementById('pizzas-grid');
    if (!grid) return;
    
    grid.innerHTML = '';

    // Tomar solo las primeras 5 pizzas para el landing (como en el diseño)
    const pizzasPopulares = pizzas.slice(0, 5);

    pizzasPopulares.forEach(pizza => {
        const card = crearPizzaCard(pizza);
        grid.appendChild(card);
    });
}

// Crear tarjeta de pizza
function crearPizzaCard(pizza) {
    const card = document.createElement('div');
    card.className = 'pizza-card';
    card.onclick = () => irATienda();

    // Procesar la URL de la imagen
    let imagenUrl = pizza.imagenURL || pizza.imagen;
    
    if (imagenUrl) {
        // Si la imagen empieza con /images/, quitarle la barra inicial
        if (imagenUrl.startsWith('/images/')) {
            imagenUrl = imagenUrl.substring(1);
        }
        // Si no es una URL completa, agregar BASE_URL
        if (!imagenUrl.startsWith('http') && !imagenUrl.startsWith('data:')) {
            imagenUrl = `${window.BASE_URL}/${imagenUrl}`;
        }
    } else {
        imagenUrl = `${window.BASE_URL}/images/Pizza.svg`;
    }

    // Formatear precio
    const precioFormateado = pizza.precio_unitario || pizza.precio || '350';

    card.innerHTML = `
        <img src="${imagenUrl}" alt="${pizza.nombre_producto || pizza.nombre || 'Pizza'}" onerror="this.src='${window.BASE_URL}/images/Pizza.svg'">
        <h3>${pizza.nombre_producto || pizza.nombre || 'Margarita'}</h3>
        <p class="price">$ ${precioFormateado}</p>
    `;

    return card;
}

// Mostrar pizzas de ejemplo si no hay datos
function mostrarPizzasEjemplo() {
    const grid = document.getElementById('pizzas-grid');
    if (!grid) return;
    
    grid.innerHTML = '';

    const pizzasEjemplo = [
        { nombre_producto: 'Margarita', precio_unitario: '350', imagen: null },
        { nombre_producto: 'Margarita', precio_unitario: '350', imagen: null },
        { nombre_producto: 'Margarita', precio_unitario: '350', imagen: null },
        { nombre_producto: 'Margarita', precio_unitario: '350', imagen: null },
        { nombre_producto: 'Margarita', precio_unitario: '350', imagen: null },
    ];

    pizzasEjemplo.forEach(pizza => {
        const card = crearPizzaCard(pizza);
        grid.appendChild(card);
    });
}

// Función para ir a la tienda
function irATienda() {
    window.location.href = `${window.BASE_URL}/vista/public/tienda.php`;
}
