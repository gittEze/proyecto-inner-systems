<?php
session_start();
require_once 'conexion.php';

// Validar inicio de sesión
if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario'])) {
    header('Location: ../PHP/login.php');
    exit();
}

// Validar que se reciban el ID del curso y el paymentID de PayPal
if (!isset($_GET['paymentID']) || empty($_GET['ID_Curso'])) {
    header('Location: ../PHP/cursos.php');
    exit();
}

$idUsuario = intval($_SESSION['ID_Usuario']);
$idCurso   = intval($_GET['ID_Curso']);

try {
    // Comprobar si el usuario ya estaba inscrito
    $check = $pdo->prepare("SELECT 1 FROM inscripciones WHERE ID_Usuario = :u AND ID_Curso = :c");
    $check->execute([':u' => $idUsuario, ':c' => $idCurso]);
    
    if (!$check->fetch()) {
        // Insertar la inscripción registrando la fecha actual (NOW)
        $sql = "INSERT INTO inscripciones (ID_Usuario, ID_Curso, Fecha_Inscripcion) 
                VALUES (:ID_Usuario, :ID_Curso, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ID_Usuario' => $idUsuario,
            ':ID_Curso'   => $idCurso
        ]);
    }

    echo "<script>alert('¡Pago exitoso con PayPal! Te has inscripto correctamente al curso.'); window.location.href='../PHP/miscursos.php';</script>";
    exit();

} catch (PDOException $e) {
    echo "<script>alert('Error al registrar la inscripción. Por favor inténtalo de nuevo.'); window.location.href='../PHP/cursos.php';</script>";
    exit();
}
?>