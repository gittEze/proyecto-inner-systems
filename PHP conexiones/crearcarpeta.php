<?php
session_start();
require_once 'conexion.php';

// Validar que la petición sea POST y provenga de un docente o administrador
$rol = strtolower(trim($_SESSION['Rol'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($rol !== 'docente' && $rol !== 'administrador')) {
    header("Location: ../PHP/miscursos.php");
    exit();
}

$idCurso = intval($_POST['ID_Curso'] ?? 0);
$nombreCarpeta = trim($_POST['Nombre_Carpeta'] ?? '');

if ($idCurso <= 0 || empty($nombreCarpeta)) {
    die("Error: Faltan datos obligatorios.");
}

// Obtener el último número de orden para ubicar la nueva carpeta al final
$stmtOrden = $pdo->prepare("SELECT MAX(Orden) AS MaxOrden FROM carpetas WHERE ID_Curso = :ID_Curso");
$stmtOrden->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtOrden->execute();
$resOrden = $stmtOrden->fetch(PDO::FETCH_ASSOC);
$nuevoOrden = ($resOrden['MaxOrden'] ?? 0) + 1;

// Insertar la carpeta en la base de datos
$sql = "INSERT INTO carpetas (ID_Curso, Nombre_Carpeta, Orden) VALUES (:ID_Curso, :Nombre_Carpeta, :Orden)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmt->bindParam(':Nombre_Carpeta', $nombreCarpeta, PDO::PARAM_STR);
$stmt->bindParam(':Orden', $nuevoOrden, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: ../PHP/vercursos.php?ID_Curso=" . $idCurso . "&tab=materiales");
    exit();
} else {
    echo "Error al crear la carpeta en la base de datos.";
}
?>