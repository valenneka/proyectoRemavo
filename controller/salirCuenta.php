<?php
require_once('../config.php');
session_destroy();
header("Location: " . BASE_URL . "/vista/public/login.php");
?>