<?php
require_once('../config.php');
session_destroy();
header("Location: " . BASE_URL . "/src/vista/public/login.php");
?>