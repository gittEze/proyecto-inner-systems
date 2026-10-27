<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../PHP conexiones/conexion.php';

$esAdministrador = false;
if (isset($_SESSION['Sesion'])) {
    $stmt = $pdo->prepare("SELECT Rol FROM usuarios WHERE Correo = :correo LIMIT 1");
    $stmt->execute([':correo' => $_SESSION['Sesion']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && strtolower(trim($row['Rol'])) === 'administrador') {
        $esAdministrador = true;
    }
}

if (!$esAdministrador) {
    header('Location: ../main.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios.php');
    exit;
}

$id       = isset($_POST['ID_Usuario']) ? (int)$_POST['ID_Usuario'] : 0;
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo   = trim($_POST['correo'] ?? '');
$rol      = trim($_POST['rol'] ?? '');

$rolesValidos = ['Administrador', 'Docente', 'Estudiante'];

if ($id <= 0 || $nombre === '' || $apellido === '' || $correo === '' || !in_array($rol, $rolesValidos, true)) {
    header('Location: usuarios.php?error=datos');
    exit;
}

try {
    $check = $pdo->prepare("SELECT ID_Usuario FROM usuarios WHERE Correo = :correo AND ID_Usuario != :id LIMIT 1");
    $check->execute([':correo' => $correo, ':id' => $id]);
    if ($check->fetch()) {
        header('Location: usuarios.php?error=correo');
        exit;
    }

    $sql = "UPDATE usuarios SET Nombre = :nombre, Apellido = :apellido, Correo = :correo, Rol = :rol WHERE ID_Usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'   => $nombre,
        ':apellido' => $apellido,
        ':correo'   => $correo,
        ':rol'      => $rol,
        ':id'       => $id,
    ]);

    header('Location: usuarios.php?actualizado=success');
    exit;
} catch (PDOException $e) {
    header('Location: usuarios.php?error=db');
    exit;
}