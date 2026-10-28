<?php
session_start();
require_once 'conexion.php';

$rol = strtolower(trim($_SESSION['Rol'] ?? ''));
$idUsuario = isset($_SESSION['ID_Usuario']) ? (int)$_SESSION['ID_Usuario'] : 0;

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    ($rol !== 'docente' && $rol !== 'administrador') ||
    $idUsuario <= 0
) {
    header('Location: ../PHP/miscursos.php');
    exit();
}

$idMaterial = (int)($_POST['ID_Material'] ?? 0);
$idCurso    = (int)($_POST['ID_Curso'] ?? 0);

if ($idMaterial <= 0 || $idCurso <= 0) {
    die('Datos inválidos.');
}

// Verificar que el material pertenece al curso y que el usuario es el creador (o admin)
$sqlCheck = "
    SELECT m.ID_Material, m.Tipo_Material, m.Contenido_URL, c.ID_Docente
    FROM materiales m
    INNER JOIN cursos c ON c.ID_Curso = m.ID_Curso
    WHERE m.ID_Material = :ID_Material AND m.ID_Curso = :ID_Curso
";
$stmt = $pdo->prepare($sqlCheck);
$stmt->execute([
    ':ID_Material' => $idMaterial,
    ':ID_Curso'    => $idCurso
]);
$mat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mat) {
    die('El material no existe o no pertenece a este curso.');
}

if ($rol !== 'administrador' && (int)$mat['ID_Docente'] !== $idUsuario) {
    die('No tienes permiso para eliminar este material.');
}

// Si es archivo físico, borrarlo del disco
if ($mat['Tipo_Material'] === 'archivo' && !empty($mat['Contenido_URL'])) {
    $ruta = __DIR__ . '/../PHP/Subidas/archivos/' . $mat['Contenido_URL'];
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

// Borrar de la BD
$stmtDel = $pdo->prepare('DELETE FROM materiales WHERE ID_Material = :ID_Material');
$stmtDel->execute([':ID_Material' => $idMaterial]);

header('Location: ../PHP/vercursos.php?ID_Curso=' . $idCurso . '&tab=materiales&msg=eliminado');
exit();