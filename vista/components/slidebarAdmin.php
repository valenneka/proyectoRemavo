<?php require_once __DIR__ . '/../../config.php';

if (
    !isset($_SESSION['usuario']) || ($_SESSION['usuario']['ID_Rol'] != 2 && $_SESSION['usuario']['ID_Rol'] != 3)
) {
    header("Location: " . BASE_URL . "/src/vista/public/error.php");
    exit;
}
?>

<!-- Fragmento: slidebarAdmin.php - solo markup del sidebar (sin <html>/<head>/<body>) -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-toggle">
        <!-- espacio para toggle (vació deliberado) -->
    </div>
    <div class="logo-section">
        <div class="logo">
            <a href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/images/Logo.svg" alt="Logo" class="section_logo">
            </a>
        </div>
    </div>

    <nav class="nav-menu">
        <ul class="menu-list">
            <li class="menu-item">
                <a href="<?= BASE_URL ?>/vista/admin/panelAdmin.php" class="menu-link">
                    <span class="menu-text">Inicio</span>
                </a>
            </li>
            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['ID_Rol'] == 3): ?>
                <li class="menu-item">
                    <a href="<?= BASE_URL ?>/vista/admin/usuarios.php" class="menu-link">
                        <span class="menu-text">Gestión Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="menu-item">
                <a href="<?= BASE_URL ?>/vista/admin/gestionPedidos.php" class="menu-link">
                    <span class="menu-text">Gestión Pedidos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="<?= BASE_URL ?>/vista/admin/gestionMenus.php" class="menu-link">
                    <span class="menu-text">Gestión de menús</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Botón flotante siempre presente -->
<button id="toggleSidebar" aria-label="Abrir o cerrar menú" title="Abrir o cerrar menú" type="button"></button>

<script>
 (function(){
    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');

    if (!toggleButton || !sidebar) return;

    const svgLeft = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    const svgRight = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    const setIcon = (closed) => {
        toggleButton.innerHTML = closed ? svgRight : svgLeft;
        toggleButton.setAttribute('aria-expanded', String(!closed));
        toggleButton.style.display = 'flex';
    };

    // Función para ajustar el margen del contenido
    const adjustContent = (closed) => {
        // Buscar elementos que necesitan ajuste de margen
        const h1Elements = document.querySelectorAll('h1');
        const tables = document.querySelectorAll('table');
        const messages = document.querySelectorAll('.acierto-message, .error-message');
        
        const marginValue = closed ? '20px' : '260px';
        
        h1Elements.forEach(el => {
            if (window.innerWidth > 768) {
                el.style.marginLeft = marginValue;
            }
        });
        
        tables.forEach(el => {
            if (window.innerWidth > 768) {
                el.style.marginLeft = marginValue;
                el.style.width = closed ? 'calc(100% - 40px)' : 'calc(100% - 280px)';
            }
        });
        
        messages.forEach(el => {
            if (window.innerWidth > 768) {
                el.style.marginLeft = marginValue;
            }
        });
    };

    // init: restablecer estado desde localStorage si existe
    const saved = localStorage.getItem('sidebarClosed');
    const initialClosed = saved === 'true';
    
    if (initialClosed) {
        sidebar.classList.add('closed');
    }
    
    setIcon(initialClosed);
    
    // Ajustar contenido después de que el DOM esté listo
    setTimeout(() => adjustContent(initialClosed), 100);

    toggleButton.addEventListener('click', () => {
        const willClose = !sidebar.classList.contains('closed');
        sidebar.classList.toggle('closed');
        setIcon(willClose);
        adjustContent(willClose);
        
        // Persistir preferencia
        localStorage.setItem('sidebarClosed', String(willClose));
    });
    
    // Ajustar en resize
    window.addEventListener('resize', () => {
        const closed = sidebar.classList.contains('closed');
        adjustContent(closed);
    });
 })();
</script>