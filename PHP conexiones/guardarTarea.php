
<?php
session_start();
require_once('conexion.php');

// Validar que la petición venga de un docente o administrador
$rol = strtolower(trim($_SESSION['Rol'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($rol !== 'docente' && $rol !== 'administrador')) {
    header("Location: ../PHP/miscursos.php");
    exit();
}

// Obtener los datos del formulario
$idCurso = intval($_POST['ID_Curso'] ?? 0);
$nombreTarea = trim($_POST['nombre_tarea'] ?? '');
$descripcionTarea = trim($_POST['descripcion_tarea'] ?? '');
$enlaceTarea = trim($_POST['enlace_tarea'] ?? '');
$fechaVencimiento = $_POST['fecha_vencimiento'] ?? '';
$fechaBloqueo = $_POST['fecha_bloqueo'] ?? '';

// Validar los datos obligatorios
if ($idCurso <= 0 || empty($nombreTarea) || empty($fechaVencimiento)) {
    die("Error: Faltan datos obligatorios.");
}

// Adaptar las fechas al formato de MySQL
$fechaVencimiento = str_replace('T', ' ', $fechaVencimiento) . ':00';

if (!empty($fechaBloqueo)) {
    $fechaBloqueo = str_replace('T', ' ', $fechaBloqueo) . ':00';
} else {
    $fechaBloqueo = null;
}

// Guardar la tarea en la base de datos
$sql = "INSERT INTO tareas
        (ID_Curso, Nombre_Tarea, Descripcion_Tarea, Enlace_Tarea, Fecha_Vencimiento, Fecha_Bloqueo)
        VALUES
        (:ID_Curso, :Nombre_Tarea, :Descripcion_Tarea, :Enlace_Tarea, :Fecha_Vencimiento, :Fecha_Bloqueo)";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmt->bindParam(':Nombre_Tarea', $nombreTarea, PDO::PARAM_STR);
$stmt->bindParam(':Descripcion_Tarea', $descripcionTarea, PDO::PARAM_STR);
$stmt->bindParam(':Enlace_Tarea', $enlaceTarea, PDO::PARAM_STR);
$stmt->bindParam(':Fecha_Vencimiento', $fechaVencimiento, PDO::PARAM_STR);

if ($fechaBloqueo === null) {
    $stmt->bindValue(':Fecha_Bloqueo', null, PDO::PARAM_NULL);
} else {
    $stmt->bindValue(':Fecha_Bloqueo', $fechaBloqueo, PDO::PARAM_STR);
}

// Redirigir al curso después de guardar
if ($stmt->execute()) {
    header("Location: ../PHP/vercursos.php?ID_Curso=" . $idCurso . "&tab=materiales");
    exit();
} else {
    echo "Error al guardar la tarea.";
}
?>
