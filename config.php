<?php
session_start();
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // Entorno local (Windows/Linux con XAMPP/Apache)
    define('BASE_URL', 'http://localhost/ProyectoRemavo');
} else {
    // Producción (cambia esto por tu dominio real)
    define('BASE_URL', 'https://pizzeriadominico.com');
}
?>
