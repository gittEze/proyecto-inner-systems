<?php

session_start();
require 'conexion.php';

// Verifica que exista una sesión iniciada
if (!isset($_SESSION['Sesion'])) {
    header("Location: ../PHP/login.php");
    exit();
}

$correo = $_SESSION['Sesion'];

// Consulta que permite obtener el ID del usuario actual
$sql = "SELECT idUsu FROM usuario WHERE Correo = :correo";

$consulta = $pdo->prepare($sql);
$consulta->execute([':correo' => $correo]);

$usuario = $consulta->fetch(PDO::FETCH_ASSOC);


if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === 0) {

    // Nombre de la imagen
    $nombre_imagen = time() . "-" . $_FILES['fotoPerfil']['name'];

    // Ubicación temporal de la imagen
    $tmp = $_FILES['fotoPerfil']['tmp_name'];

    // Ruta donde se guardará la imagen
    $ruta_destino = __DIR__ . "/../IMG/perfiles/" . $nombre_imagen;

    // Guarda la imagen en la carpeta
    move_uploaded_file($tmp, $ruta_destino);


    // Guarda el nombre de la imagen en la base de datos
    $sql = "UPDATE usuario 
            SET Foto_Perfil = :Foto_Perfil 
            WHERE idUsu = :idUsu";

    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':Foto_Perfil', $nombre_imagen, PDO::PARAM_STR);
    $consulta->bindParam(':idUsu', $usuario['idUsu'], PDO::PARAM_INT);

    $consulta->execute();
}

// Luego de cambiar la foto, vuelve al perfil
header("Location: ../PHP/perfil.php");
exit();

?>