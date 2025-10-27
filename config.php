<?php
session_start();
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // Entorno local (Windows/Linux con XAMPP/Apache)
    define('BASE_URL', 'http://localhost/PizzeriaDominico');
} else {
    // En produccion cambiar esto por dominio real)
    define('BASE_URL', 'https://pizzeriadominico.com');
}
?>
