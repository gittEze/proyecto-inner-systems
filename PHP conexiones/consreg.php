<?php

require 'conexion.php';


// Validación para espacios de los datos insertados por medio de trim.

$nombre = trim($_POST['Nombre'] ?? '');

$apellido = trim($_POST['Apellido'] ?? '');

$cedula = trim($_POST['Cedula'] ?? '');

$fecha_de_nacimiento = trim($_POST['Fecha_De_Nacimiento'] ?? '');

$correo = trim($_POST['Correo'] ?? '');

$contrasena = trim($_POST['Contrasenia'] ?? '');

$telefono = trim($_POST['Telefono'] ?? '');

$genero = trim($_POST['Genero'] ?? '');

$rol = trim($_POST['Rol'] ?? '');


//Consulta que por medio de PDO y el metodo post inserta los usuarios del registro en la BD.

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO usuarios (Nombre, Apellido, Cedula, Fecha_De_Nacimiento, Correo, Contrasenia, Telefono, Genero, Rol) VALUES (:nombre, :apellido, :cedula, :fecha_de_nacimiento, :correo, :contrasenia, :telefono, :genero, :rol)';
    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':nombre', $_POST['Nombre'], PDO::PARAM_STR);

    $consulta->bindParam(':apellido', $_POST['Apellido'], PDO::PARAM_STR);

    $consulta->bindParam(':cedula', $_POST['Cedula'], PDO::PARAM_INT);

    $consulta->bindParam(':fecha_de_nacimiento', $_POST['Fecha_De_Nacimiento'], PDO::PARAM_STR);

    $consulta->bindParam(':correo', $_POST['Correo'], PDO::PARAM_STR);

    $consulta->bindParam(':contrasenia', $_POST['Contrasenia'], PDO::PARAM_STR);

    $consulta->bindParam(':telefono', $_POST['Telefono'], PDO::PARAM_INT);

    $consulta->bindParam(':genero', $_POST['Genero'], PDO::PARAM_STR);

    $consulta->bindParam(':rol', $_POST['Rol'], PDO::PARAM_STR);

    $consulta->execute();
}

// Una vez se da el registro fue envia en el header del login en el parametro exito un dato.

header("Location: ../PHP/login.php?exito=1&Correo=".$_POST['Correo']);




?>