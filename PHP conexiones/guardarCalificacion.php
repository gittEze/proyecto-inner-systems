
<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['Sesion']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../PHP/login.php");
    exit();
}

// Obtener usuario
$sqlUsuario = "SELECT ID_Usuario, Rol
               FROM usuarios
               WHERE Correo = :correo";

$stmtUsuario = $pdo->prepare($sqlUsuario);
$stmtUsuario->execute([
    ':correo' => $_SESSION['Sesion']
]);

$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario || strtolower(trim($usuario['Rol'])) !== 'docente') {
    die("No tienes permiso para calificar entregas.");
}

$idUsuario = (int)$usuario['ID_Usuario'];
$idTarea = (int)($_POST['ID_Tarea'] ?? 0);
$idEntrega = (int)($_POST['ID_Entrega'] ?? 0);
$calificacion = trim($_POST['Calificacion'] ?? '');
$comentario = trim($_POST['Comentario_Docente'] ?? '');

if (
    $idTarea <= 0 ||
    $idEntrega <= 0 ||
    $calificacion === '' ||
    !is_numeric($calificacion) ||
    (float)$calificacion < 0
) {
    die("Los datos de la calificación no son válidos.");
}

try {
    // Comprobar que la tarea pertenece a un curso del docente
    $sqlTarea = "SELECT t.ID_Tarea
                 FROM tareas t
                 INNER JOIN cursos c ON c.ID_Curso = t.ID_Curso
                 WHERE t.ID_Tarea = :ID_Tarea
                 AND c.ID_Docente = :ID_Docente";

    $stmtTarea = $pdo->prepare($sqlTarea);
    $stmtTarea->execute([
        ':ID_Tarea' => $idTarea,
        ':ID_Docente' => $idUsuario
    ]);

    if (!$stmtTarea->fetch(PDO::FETCH_ASSOC)) {
        die("Esta tarea no pertenece a uno de tus cursos.");
    }

    // Actualizar la entrega correspondiente
    $sqlActualizar = "UPDATE entregas_tareas
                      SET Calificacion = :Calificacion,
                          Comentario_Docente = :Comentario_Docente,
                          Fecha_Calificacion = NOW()
                      WHERE ID_Entrega = :ID_Entrega
                      AND ID_Tarea = :ID_Tarea";

    $stmtActualizar = $pdo->prepare($sqlActualizar);
    $stmtActualizar->execute([
        ':Calificacion' => $calificacion,
        ':Comentario_Docente' => $comentario !== '' ? $comentario : null,
        ':ID_Entrega' => $idEntrega,
        ':ID_Tarea' => $idTarea
    ]);

    header(
        "Location: ../PHP/tarea.php?ID_Tarea=" .
        $idTarea . "&calificacion=ok"
    );
    exit();

} catch (PDOException $e) {
    die("Error al guardar la calificación: " . $e->getMessage());
}
?>
