<?php
session_start();
session_unset();
session_destroy();
header('Location:../PHP/main.php');
exit();
?>

<!-- Este archivo tiene como objetivo el abrir y cerrar la sesiones de en el main para los inicios de sesión -->