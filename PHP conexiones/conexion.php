<?php
// Estructura que permite la conexión con la base de datos.
$server = "localhost";
$usuario = "root";
$contraseña = "";
$database = "softwaredesarrollo";

// La variable $dsn toma los datos de la estructura anterior y los guarda.
$dsn = 'mysql:host=' . $server . ';dbname=' . $database;
$pdo = new PDO($dsn, $usuario, $contraseña);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
// La conexión funciona mediante PDO.

?>