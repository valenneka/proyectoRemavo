<?php require_once __DIR__ . '/config.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/images/Logo.svg">
    <title>Pizzería Dominico</title>
    <link rel="stylesheet" href="src/css/footer.css">
    <link rel="stylesheet" href="src/css/navbar.css">
    <link rel="stylesheet" href="src/css/landing.css">
</head>

<body>
    <?php include(__DIR__ . '/vista/components/navbar.php'); ?>

    <main style="flex: 1; display: flex; flex-direction: column;">
    <!-- Hero Section -->
    <section class="promo" id="inicio">
        <div class="container">
            <h1 class="titulo">Caseras,<br> Con una pizca extra<br> De <span class="amor">Amor</span></h1>
            <p>Perfección de sabor en una corteza crujiente: una sinfonía entre el queso y los aderezos.</p>
            <a href="<?= BASE_URL ?>/vista/public/tienda.php" class="ordenarBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="vertical-align: middle; margin-right: 8px;">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" fill="white"/>
                </svg>
                Ordenar ahora
            </a>
        </div>
        <div class="hero-image-container">
            <div class="imagen-pizza">
                <div class="circulo-naranja"></div>
                <img src="images/Pizza.svg" alt="Pizza">
            </div>
            <div class="decorative-olives olives-left">
                <div class="olive olive-1"></div>
                <div class="olive olive-2"></div>
            </div>
        </div>
    </section>

    <!-- Fresh Section -->
    <section class="pizza-banner">
        <div class="pizza-img">
            <img src="images/Pizza2.svg" alt="Pizza deliciosa">
            <div class="decorative-veggie tomato"></div>
            <div class="decorative-veggie basil"></div>
        </div>
        <div class="pizza-texto">
            <h2><strong>Frescas y siempre sabrosas</strong></h2>
            <p>Disfrute de la deliciosa armonía de sabores con nuestras pizzas de ingredientes frescos y sabrosos.</p>
        </div>
        <div class="decorative-right">
            <div class="decorative-veggie onion"></div>
            <div class="decorative-veggie olive-single"></div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews-section">
        <div class="section-header-simple">
            <h2 class="section-title">Reseñas</h2>
            <p class="section-subtitle">Lo que dicen nuestros clientes</p>
            <a href="https://www.google.com/search?q=pizzeria+dominico" target="_blank" class="google-link">
                <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png" alt="Google" style="height: 20px; vertical-align: middle;">
                Ver más reseñas
            </a>
        </div>
        <div class="reviews-grid">
            <!-- Review Card 1 -->
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar">
                        <div class="avatar-placeholder">RG</div>
                    </div>
                    <div class="review-info">
                        <h4>Roberto Gabriel Acosta</h4>
                        <div class="stars">★★★★★</div>
                        <span class="review-date">Hace 3 semanas</span>
                    </div>
                </div>
                <p>Lugar muy tranqui para comer...</p>
                <span class="review-source">Google • Local Guide</span>
            </div>
            <!-- Review Card 2 -->
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar">
                        <div class="avatar-placeholder">AG</div>
                    </div>
                    <div class="review-info">
                        <h4>Ana Gabriela Silveira</h4>
                        <div class="stars">★★★★★</div>
                        <span class="review-date">Hace un mes</span>
                    </div>
                </div>
                <p>Exquisita cena y excelente atención!...</p>
                <span class="review-source">Google • Local Guide</span>
            </div>
            <!-- Review Card 3 -->
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar">
                        <div class="avatar-placeholder">AM</div>
                    </div>
                    <div class="review-info">
                        <h4>Andrés Muñoz</h4>
                        <div class="stars">★★★★★</div>
                        <span class="review-date">Hace un mes</span>
                    </div>
                </div>
                <p>Entrega a domicilio | $200-400</p>
                <span class="review-source">Google • Local Guide</span>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="our-story" id="historia">
        <h2 class="story-title">Nuestra historia</h2>
        <div class="contact-info">
            <div class="contact-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#ff6b35">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span>Av. Blandengues 320</span>
            </div>
            <div class="contact-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#ff6b35">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <span>pizzeria@gmail.com</span>
            </div>
            <div class="contact-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#ff6b35">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
                <span>+53 241 542 9513</span>
            </div>
        </div>
        <div class="story-content">
            <div class="story-image">
                <img src="<?= BASE_URL ?>/images/pizzeria-exterior.jpg"
                     alt="Nuestra Pizzería"
                     onerror="this.src='<?= BASE_URL ?>/images/Pizza2.svg'">
            </div>
            <div class="story-text">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla rhoncus venenatis odio, vitae feugiat nisl tristique quis. Vivamus ut enim non nunc placerat porttitor. Ut sed purus consequat, maximus eros nec, vehicula dolor. Fusce ultrices neque, a fermentum est consectetur eu.</p>
                <p>Maecenas id bibendum, mattis leo eu, bibendum neque. Cras tincidunt orci et, ullamcorper mauris sit amet metus sapien. Maecenas. tincidunt nisl diam. Sed laoreet massa at massa sodales consectetur sed ac mauris. Phasellus viverra vitae nulla at cursus. Praesent ante enim, pellentesque blandit cursus consectetur eu, maximus sollicitudin augue.</p>
                <p>Curabitur ac arcu eget ultricies, scelerisque. Duis fermentum neque, turpis vel dolor blandit. Etiam euismod, nisl ac ornare aliquet, magna velit faucibus nisl, in tincidunt ipsum.</p>
            </div>
        </div>
    </section>
    </main>

    <?php include('vista/components/footer.php'); ?>
</body>

</html>