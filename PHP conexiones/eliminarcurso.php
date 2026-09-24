<?php
session_start();
require_once 'conexion.php';

// Validar que el usuario haya iniciado sesión y tenga el rol de administrador
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'administrador') {
    echo "<script>
        alert('Acceso denegado: No tienes permisos de administrador.');
        window.location.href='../PHP/cursos.php';
    </script>";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCurso'])) {
    $idCurso = intval($_POST['idCurso']);


    // try que intenta eliminar un cursos de la base de datos por su id.
    try {
        $sql = "DELETE FROM cursos WHERE idCurso = :idCurso";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>
                alert('Curso eliminado exitosamente.');
                window.location.href='../PHP/cursos.php';
            </script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>
            alert('Error al eliminar el curso.');
            window.location.href='../PHP/cursos.php';
        </script>";
        exit();
    }
} else {
    header("Location: ../PHP/cursos.php");
    exit();
}
?>