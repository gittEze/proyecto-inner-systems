<?php

$server = "localhost";
$user = "Aprendomo";
$password = "PalaCaballoDiamante";
$database = "softwaredesarrollo";


$conexion = new mysqli($server, $user, $password, $database);


if ($conexion->connect_errno) {
    die("Conexión fallida: " . $conexion->connect_error);
}




?>