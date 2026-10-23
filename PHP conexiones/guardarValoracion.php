<?php
session_start();
require_once 'conexion.php';

// Solo usuarios autenticados
if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario'])) {
    header('Location: ../PHP/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../PHP/cursos.php');
    exit();
}

$idCurso    = isset($_POST['ID_Curso']) ? intval($_POST['ID_Curso']) : 0;
$puntuacion = isset($_POST['Puntuacion']) ? intval($_POST['Puntuacion']) : 0;
$comentario = isset($_POST['Comentario']) ? trim($_POST['Comentario']) : '';
$idUsuario  = intval($_SESSION['ID_Usuario']);

// Validaciones servidor
if ($idCurso <= 0) {
    echo "<script>alert('Curso inválido.'); window.history.back();</script>";
    exit();
}

if ($puntuacion < 1 || $puntuacion > 5) {
    echo "<script>alert('La puntuación debe ser un número entero entre 1 y 5.'); window.history.back();</script>";
    exit();
}

// El comentario puede estar vacío, pero no solo espacios (ya se hizo trim)
if ($comentario === '') {
    $comentario = null;
}

// Verificar que el curso exista
$stmtCurso = $pdo->prepare("SELECT ID_Curso FROM cursos WHERE ID_Curso = :id");
$stmtCurso->execute([':id' => $idCurso]);
if (!$stmtCurso->fetch()) {
    echo "<script>alert('El curso no existe.'); window.history.back();</script>";
    exit();
}

try {
    // INSERT ... ON DUPLICATE KEY UPDATE (respeta UNIQUE uk_usuario_curso)
    $sql = "INSERT INTO valoraciones (ID_Curso, ID_Usuario, Puntuacion, Comentario)
            VALUES (:ID_Curso, :ID_Usuario, :Puntuacion, :Comentario)
            ON DUPLICATE KEY UPDATE
                Puntuacion = VALUES(Puntuacion),
                Comentario = VALUES(Comentario),
                Fecha = CURRENT_TIMESTAMP";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Curso'    => $idCurso,
        ':ID_Usuario'  => $idUsuario,
        ':Puntuacion'  => $puntuacion,
        ':Comentario'  => $comentario
    ]);

    header("Location: ../PHP/vercursos.php?ID_Curso={$idCurso}&tab=valoraciones&ok=1");
    exit();

} catch (PDOException $e) {
    // No exponer detalles de la excepción al usuario
    echo "<script>alert('Error al guardar la valoración. Inténtalo de nuevo.'); window.history.back();</script>";
    exit();
}