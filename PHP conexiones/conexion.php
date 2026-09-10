<?php
$host = 'localhost';
$usuario = 'Aprendomo';
$contraseña = 'PalaCaballoDiamante';
$baseDatos = 'softwaredesarrollo';

$dsn = 'mysql:host=' . $host . ';dbname=' . $baseDatos;
$pdo = new PDO($dsn, $usuario, $contraseña);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); //Para poder usar modo objeto

?>
