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
$titulo     = trim($_POST['Titulo_Material'] ?? '');

if ($idMaterial <= 0 || $idCurso <= 0) {
    die('Datos inválidos.');
}

// Verificar propiedad
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
    die('No tienes permiso para modificar este material.');
}

$tipo = $mat['Tipo_Material'];
$nuevoContenido = $mat['Contenido_URL']; // por defecto se mantiene

if ($tipo === 'archivo') {
    if (
        !isset($_FILES['archivo_adjunto']) ||
        $_FILES['archivo_adjunto']['error'] !== UPLOAD_ERR_OK
    ) {
        die('Debes seleccionar un archivo válido para sustituir.');
    }

    $archivo = $_FILES['archivo_adjunto'];
    if (!is_uploaded_file($archivo['tmp_name'])) {
        die('Archivo no válido.');
    }

    $nombreOriginal = basename($archivo['name']);
    $nombreLimpio = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombreOriginal);
    if ($nombreLimpio === '' || $nombreLimpio === '.' || $nombreLimpio === '..') {
        $nombreLimpio = 'archivo';
    }

    $nombreArchivoFinal = time() . '_' . bin2hex(random_bytes(5)) . '_' . $nombreLimpio;
    $dirDestino = __DIR__ . '/../PHP/Subidas/archivos/';

    if (!is_dir($dirDestino) && !mkdir($dirDestino, 0755, true) && !is_dir($dirDestino)) {
        die('Error al crear la carpeta de archivos.');
    }

    $rutaCompleta = $dirDestino . $nombreArchivoFinal;
    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        die('Error al guardar el nuevo archivo.');
    }

    // Borrar el archivo anterior
    if (!empty($mat['Contenido_URL'])) {
        $rutaVieja = $dirDestino . $mat['Contenido_URL'];
        if (is_file($rutaVieja)) {
            @unlink($rutaVieja);
        }
    }

    $nuevoContenido = $nombreArchivoFinal;

} else {
    // Es enlace
    $url = trim($_POST['Contenido_URL'] ?? '');
    if (
        $url === '' ||
        !filter_var($url, FILTER_VALIDATE_URL) ||
        !in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
    ) {
        die('Introduce un enlace válido (http o https).');
    }
    $nuevoContenido = $url;
}

// Actualizar título si vino, y el contenido
$sqlUp = "UPDATE materiales 
          SET Contenido_URL = :Contenido_URL" .
          ($titulo !== '' ? ", Titulo_Material = :Titulo_Material" : "") .
          " WHERE ID_Material = :ID_Material";

$params = [
    ':Contenido_URL' => $nuevoContenido,
    ':ID_Material'   => $idMaterial
];
if ($titulo !== '') {
    $params[':Titulo_Material'] = $titulo;
}

$stmtUp = $pdo->prepare($sqlUp);
$stmtUp->execute($params);

header('Location: ../PHP/vercursos.php?ID_Curso=' . $idCurso . '&tab=materiales&msg=sustituido');
exit();