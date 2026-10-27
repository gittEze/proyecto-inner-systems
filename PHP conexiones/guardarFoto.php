<?php
session_start();
require_once 'conexion.php';

// Verifica que exista una sesión iniciada
if (!isset($_SESSION['Sesion'])) {
    header("Location: ../PHP/login.php");
    exit();
}

$correo = $_SESSION['Sesion'];

// Obtener el ID del usuario actual (tabla correcta: usuarios)
$sql = "SELECT ID_Usuario FROM usuarios WHERE Correo = :correo";
$consulta = $pdo->prepare($sql);
$consulta->execute([':correo' => $correo]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: ../PHP/login.php");
    exit();
}

if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {

    $archivo = $_FILES['fotoPerfil'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $permitidas)) {
        header("Location: ../PHP/perfil.php?error=formato");
        exit();
    }

    // Nombre único
    $nombre_imagen = time() . "-" . bin2hex(random_bytes(4)) . "." . $extension;

    // Ruta de destino
    $carpeta = __DIR__ . "/../IMG/perfiles/";
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $ruta_destino = $carpeta . $nombre_imagen;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        // Actualizar en la base de datos
        $sql = "UPDATE usuarios SET Foto_Perfil = :Foto_Perfil WHERE ID_Usuario = :ID_Usuario";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':Foto_Perfil' => $nombre_imagen,
            ':ID_Usuario'  => $usuario['ID_Usuario']
        ]);
    }
}

// Volver al perfil
header("Location: ../PHP/perfil.php");
exit();
?>