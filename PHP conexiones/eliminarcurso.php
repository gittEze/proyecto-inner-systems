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

// Se evalua si los datos son recibidos por el metodo POST y verifican qu este idCurso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_Curso'])) {
    $ID_Curso = intval($_POST['ID_Curso']);


    // try que intenta eliminar un cursos de la base de datos por su id.
    try {
        // Consulta para eliminar el curso en base al id.
        $sql = "DELETE FROM cursos WHERE ID_Curso = :ID_Curso";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ID_Curso', $ID_Curso, PDO::PARAM_INT);

        // Condicional encargado de verificar si la consulte se ejecuta,en caso de que si, muestra el alert confirmadno el exito.
        if ($stmt->execute()) {
            echo "<script>
                alert('Curso eliminado exitosamente.');
                window.location.href='../PHP/cursos.php';
            </script>";
            exit();
        }
        // Captura la excepcion y se muestra un script con otro alert indicando que ocurrio un error, en tal caso vuelve a cargar la misma pestaña.
    } catch (PDOException $e) {
        echo "<script>
            alert('Error al eliminar el curso.');
            window.location.href='../PHP/cursos.php';
        </script>";
        exit();
    }
    // En este else se reitera el volver a cursos.php independientemente del condicional anterior.
} else {
    header("Location: ../PHP/cursos.php");
    exit();
}
?>