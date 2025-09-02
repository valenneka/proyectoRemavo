let currentIndex = 0;
let pizzas = [];
const pizzasVisibles = 5;

async function fetchMenu() {
  try {
    const response = await fetch("test.json");
    pizzas = await response.json();
    renderCarousel();
  } catch (error) {
    console.error("Error fetching menu:", error);
  }
}

function renderCarousel() {
  const carousel = document.getElementById("carousel");
  carousel.innerHTML = "";

  // Clones antes + pizzas + clones después
  const items = [
    ...pizzas.slice(-pizzasVisibles),
    ...pizzas,
    ...pizzas.slice(0, pizzasVisibles)
  ];

  items.forEach(pizza => carousel.appendChild(createItem(pizza)));

  currentIndex = pizzasVisibles;
  updateCarousel(false); // false = sin animación inicial
}

function createItem(pizza) {
  const item = document.createElement("div");
  item.className = "carousel-item";
  item.innerHTML = `
    <img src="${pizza.imagen}" alt="${pizza.nombre}">
    <h2>${pizza.nombre}</h2>
    <p>${pizza.descripcion}</p>
    <p><strong>$${pizza.precio}</strong></p>
  `;
  return item;
}

function updateCarousel(withTransition = true) {
  const carousel = document.getElementById("carousel");
  carousel.style.transition = withTransition ? "transform 0.4s ease" : "none";
  carousel.style.transform = `translateX(-${currentIndex * (100 / pizzasVisibles)}%)`;

  carousel.addEventListener("transitionend", () => {
    if (currentIndex < pizzasVisibles) {
      currentIndex += pizzas.length;
      updateCarousel(false);
    }
    if (currentIndex >= pizzas.length + pizzasVisibles) {
      currentIndex -= pizzas.length;
      updateCarousel(false);
    }
  }, { once: true });
}

// Botones
document.getElementById("prevBtn").addEventListener("click", () => {
  currentIndex--;
  updateCarousel();
});
document.getElementById("nextBtn").addEventListener("click", () => {
  currentIndex++;
  updateCarousel();
});

fetchMenu();