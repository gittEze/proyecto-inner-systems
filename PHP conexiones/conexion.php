<?php
$server   = "localhost";
$usuario  = "root";
$contraseña = "";                
$database = "aprendomo";

try {
    $dsn = "mysql:host=$server;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $usuario, $contraseña);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>