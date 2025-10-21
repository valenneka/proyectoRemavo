<?php 
require_once('../config.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/vista/public/login.php");
}else {
    header("Location: " . BASE_URL . "/vista/public/perfil.php");
}