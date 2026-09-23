<?php

require 'conexion.php';


//Conuslta que por medio de PDO y el metodo post inserta los usuarios del registro en la BD.

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO usuario (Nombre, Apellido, Cedula, Fecha_de_Nacimiento, Correo, Contraseña, Telefono, Genero, Rol) VALUES (:nombre, :apellido, :cedula, :fecha_nacimiento, :correo, :contrasena, :telefono, :genero, :rol)';
    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':nombre', $_POST['Nombre'], PDO::PARAM_STR);

    $consulta->bindParam(':apellido', $_POST['Apellido'], PDO::PARAM_STR);

    $consulta->bindParam(':cedula', $_POST['Cedula'], PDO::PARAM_INT);

    $consulta->bindParam(':fecha_nacimiento', $_POST['Fecha_de_Nacimiento'], PDO::PARAM_STR);

    $consulta->bindParam(':correo', $_POST['Correo'], PDO::PARAM_STR);

    $consulta->bindParam(':contrasena', $_POST['Contrasena'], PDO::PARAM_STR);

    $consulta->bindParam(':telefono', $_POST['Telefono'], PDO::PARAM_INT);

    $consulta->bindParam(':genero', $_POST['Genero'], PDO::PARAM_STR);

    $consulta->bindParam(':rol', $_POST['Rol'], PDO::PARAM_STR);

    $consulta->execute();
}

// Una vez se da el registro fue envia en el header del login en el parametro exito un dato.

header("Location: ../PHP/login.php?exito=1&Correo=".$_POST['Correo']);




?>