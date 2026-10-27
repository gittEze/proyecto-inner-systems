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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: usuarios.php?error=id');
    exit;
}

$miId = (int)($_SESSION['ID_Usuario'] ?? 0);
if ($id === $miId) {
    header('Location: usuarios.php?error=self');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE ID_Usuario = :id");
    $stmt->execute([':id' => $id]);
    header('Location: usuarios.php?eliminado=success');
    exit;
} catch (PDOException $e) {
    header('Location: usuarios.php?error=db');
    exit;
}