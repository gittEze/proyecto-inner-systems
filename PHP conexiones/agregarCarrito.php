<?php
session_start();
require_once 'conexion.php';

// Solo usuarios autenticados
if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario'])) {
    header('Location: ../PHP/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['ID_Curso'])) {
    header('Location: ../PHP/cursos.php');
    exit();
}

$idCurso = intval($_POST['ID_Curso']);

// Verificar que el curso exista
$stmt = $pdo->prepare("SELECT ID_Curso, Titulo_Curso FROM cursos WHERE ID_Curso = :id");
$stmt->execute([':id' => $idCurso]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "<script>alert('El curso no existe.'); window.location.href='../PHP/cursos.php';</script>";
    exit();
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Evitar duplicados
if (!in_array($idCurso, $_SESSION['carrito'], true)) {
    $_SESSION['carrito'][] = $idCurso;
}

// Redirigir
$destino = isset($_POST['redirigir']) && $_POST['redirigir'] === 'cursos'
    ? '../PHP/cursos.php'
    : '../PHP/carrito.php';

header("Location: {$destino}");
exit();
?>