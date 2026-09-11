<?php
<<<<<<< Updated upstream
=======
$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$baseDatos = 'softwaredesarrollo';
>>>>>>> Stashed changes

$server = "localhost";
$user = "Aprendomo";
$password = "PalaCaballoDiamante";
$database = "softwaredesarrollo";


$conexion = new mysqli($server, $user, $password, $database);


if ($conexion->connect_errno) {
    die("Conexión fallida: " . $conexion->connect_error);
}




?>