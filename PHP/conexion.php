<?php

$server = "localhost";
$user = "root";
$password = "";
$database = "softwaredesarrollo";

// 1. Crear la conexión
$conexion = new mysqli($server, $user, $password, $database);

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Conexión fallida: " . $conexion->connect_error);
}

?>