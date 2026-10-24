<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario'])) {
    header('Location: ../PHP/login.php');
    exit();
}

// Estructura que valida la información traida de Paypal por medio de POST válido.
if (!isset($_GET['paymentID']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../PHP/carrito.php');
    exit();
}

$idUsuario = intval($_SESSION['ID_Usuario']);
$carrito   = isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])
    ? array_map('intval', $_SESSION['carrito'])
    : [];

if (empty($carrito)) {
    echo "<script>alert('El carrito está vacío.'); window.location.href='../PHP/carrito.php';</script>";
    exit();
}

$inscritos = 0;
$yaInscritos = 0;

try {
    $sql = "INSERT IGNORE INTO inscripciones (ID_Usuario, ID_Curso) VALUES (:ID_Usuario, :ID_Curso)";
    $stmt = $pdo->prepare($sql);

    foreach ($carrito as $idCurso) {
        if ($idCurso <= 0) {
            continue;
        }
        // Comprobar si ya estaba inscrito
        $check = $pdo->prepare("SELECT 1 FROM inscripciones WHERE ID_Usuario = :u AND ID_Curso = :c");
        $check->execute([':u' => $idUsuario, ':c' => $idCurso]);
        if ($check->fetch()) {
            $yaInscritos++;
            continue;
        }

        $stmt->execute([
            ':ID_Usuario' => $idUsuario,
            ':ID_Curso'   => $idCurso
        ]);
        if ($stmt->rowCount() > 0) {
            $inscritos++;
        }
    }

    // Vaciar carrito tras inscribir
    $_SESSION['carrito'] = [];

    $mensaje = "Te inscribiste a {$inscritos} curso(s).";
    if ($yaInscritos > 0) {
        $mensaje .= " {$yaInscritos} ya estaban en tu lista.";
    }

    echo "<script>alert(" . json_encode($mensaje) . "); window.location.href='../PHP/miscursos.php';</script>";
    exit();

} catch (PDOException $e) {
    echo "<script>alert('Error al inscribir. Inténtalo de nuevo.'); window.location.href='../PHP/carrito.php';</script>";
    exit();
}