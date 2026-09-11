<?php
$server = "localhost";
$usuario = "root";
$contraseña = "";
$database = "softwaredesarrollo";


$dsn = 'mysql:host=' . $server . ';dbname=' . $database;
$pdo = new PDO($dsn, $usuario, $contraseña);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);


?>