<?php 
require_once('../config.php');


if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/src/vista/public/login.php");
}else {
    header("Location: " . BASE_URL . "/src/vista/public/perfil.php");
}