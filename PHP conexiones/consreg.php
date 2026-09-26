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


// Estructura condicional que permite verificar si los campos estan vacios o no.

if(empty($nombre) || empty($apellido) || empty($cedula) || empty($fecha_de_nacimiento) || empty($correo) || empty($contrasena) && empty($telefono) && empty($genero) && empty($rol)){
    echo "<script>alert('Se solicita que todos los campos esten completos.'); windows.location.href= '../PHP/login.php'</script>";
}

// Estructura condicional encargaada de verificar si los datos tipo  "int" son numéricos.

if(!is_numeric($cedula) and !is_numeric($telefono)){
    echo "<script>alert('Error: Cedula y Telefono deben ser numeros.'); windows.location.href= '../PHP/login.php'</script>";
 
}

// Hash a la contraseña previamente traida on protección contra terceros.

$contrasena_hash= password_hash($contrasena, PASSWORD_DEFAULT);


// Saneamiento de correo
$correo_saneado = filter_var($correo, FILTER_SANITIZE_EMAIL);
// Validación del correo
$correo_correcto = filter_var($correo_saneado);


//Consulta que por medio de PDO y el metodo post inserta los usuarios del registro en la BD.

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO usuarios (Nombre, Apellido, Cedula, Fecha_De_Nacimiento, Correo, Contrasenia, Telefono, Genero, Rol) VALUES (:nombre, :apellido, :cedula, :fecha_de_nacimiento, :correo, :contrasenia, :telefono, :genero, :rol)';
    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':nombre', $_POST['Nombre'], PDO::PARAM_STR);

    $consulta->bindParam(':apellido', $_POST['Apellido'], PDO::PARAM_STR);

    $consulta->bindParam(':cedula', $_POST['Cedula'], PDO::PARAM_INT);

    $consulta->bindParam(':fecha_de_nacimiento', $_POST['Fecha_De_Nacimiento'], PDO::PARAM_STR);

    $consulta->bindParam(':correo', $correo_correcto, PDO::PARAM_STR);

    $consulta->bindParam(':contrasenia',$contrasena_hash, PDO::PARAM_STR);

    $consulta->bindParam(':telefono', $_POST['Telefono'], PDO::PARAM_INT);

    $consulta->bindParam(':genero', $_POST['Genero'], PDO::PARAM_STR);

    $consulta->bindParam(':rol', $_POST['Rol'], PDO::PARAM_STR);

    $consulta->execute();
}

// Una vez se da el registro envia en el header del login un parametro con un valor (en este caso exito=1).

header("Location: ../PHP/login.php?exito=1&Correo=".$_POST['Correo']);




?>