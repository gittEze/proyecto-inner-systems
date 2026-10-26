<?php
session_start();
require_once 'conexion.php';

// Solo docentes o administradores pueden subir proyectos
$rol = strtolower(trim($_SESSION['Rol'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($rol !== 'docente' && $rol !== 'administrador')) {
    header('Location: ../PHP/miscursos.php');
    exit();
}

if (!isset($_SESSION['ID_Usuario'])) {
    header('Location: ../PHP/login.php');
    exit();
}

$idCurso     = intval($_POST['ID_Curso'] ?? 0);
$titulo      = trim($_POST['Titulo_Proyecto'] ?? '');
$descripcion = trim($_POST['Descripcion'] ?? '');
$idDocente   = intval($_SESSION['ID_Usuario']);

if ($idCurso <= 0 || empty($titulo)) {
    die('Error: Faltan datos obligatorios (curso y título).');
}

// Verificar que el curso existe y que el usuario es el docente creador (o admin)
$stmtCurso = $pdo->prepare('SELECT ID_Curso, ID_Docente FROM cursos WHERE ID_Curso = :ID_Curso');
$stmtCurso->execute([':ID_Curso' => $idCurso]);
$curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    die('Error: El curso no existe.');
}

if ($rol !== 'administrador' && intval($curso['ID_Docente']) !== $idDocente) {
    die('Error: Solo el docente que creó el curso puede subir proyectos educativos.');
}

// Carpetas de destino (mismo patrón que subirmaterial.php)
$dirProyectos = __DIR__ . '/../PHP/Subidas/proyectos/';
$dirPortadas  = __DIR__ . '/../PHP/Subidas/proyectos/portadas/';

if (!is_dir($dirProyectos)) {
    mkdir($dirProyectos, 0777, true);
}
if (!is_dir($dirPortadas)) {
    mkdir($dirPortadas, 0777, true);
}

$nombreArchivo = null;
$nombrePortada = null;

// Archivo del proyecto (PDF, ZIP, docs, imágenes, etc.)
if (isset($_FILES['archivo_proyecto']) && $_FILES['archivo_proyecto']['error'] === UPLOAD_ERR_OK) {
    $nombreOriginal = basename($_FILES['archivo_proyecto']['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $permitidas = ['pdf', 'zip', 'rar', 'doc', 'docx', 'ppt', 'pptx', 'png', 'jpg', 'jpeg', 'gif', 'webp'];

    if (!in_array($extension, $permitidas)) {
        die('Error: Tipo de archivo no permitido. Usa PDF, ZIP, DOC, PPT o imágenes.');
    }

    $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombreOriginal);
    $rutaCompleta  = $dirProyectos . $nombreArchivo;

    if (!move_uploaded_file($_FILES['archivo_proyecto']['tmp_name'], $rutaCompleta)) {
        die('Error al guardar el archivo del proyecto.');
    }
}

// Imagen de portada (opcional)
if (isset($_FILES['imagen_portada']) && $_FILES['imagen_portada']['error'] === UPLOAD_ERR_OK) {
    $nombreOriginal = basename($_FILES['imagen_portada']['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $imgs = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    if (in_array($extension, $imgs)) {
        $nombrePortada = time() . '_portada_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombreOriginal);
        $rutaPortada   = $dirPortadas . $nombrePortada;
        move_uploaded_file($_FILES['imagen_portada']['tmp_name'], $rutaPortada);
    }
}

$sql = "INSERT INTO proyectos_educativos
        (ID_Curso, ID_Docente, Titulo_Proyecto, Descripcion, Archivo_URL, Imagen_Portada)
        VALUES
        (:ID_Curso, :ID_Docente, :Titulo_Proyecto, :Descripcion, :Archivo_URL, :Imagen_Portada)";

$stmt = $pdo->prepare($sql);
$ok = $stmt->execute([
    ':ID_Curso'        => $idCurso,
    ':ID_Docente'      => $idDocente,
    ':Titulo_Proyecto' => $titulo,
    ':Descripcion'     => $descripcion,
    ':Archivo_URL'     => $nombreArchivo,
    ':Imagen_Portada'  => $nombrePortada
]);

if ($ok) {
    header('Location: ../PHP/vercursos.php?ID_Curso=' . $idCurso . '&tab=materiales&proyecto=ok');
    exit();
}

echo 'Error al guardar el proyecto en la base de datos.';