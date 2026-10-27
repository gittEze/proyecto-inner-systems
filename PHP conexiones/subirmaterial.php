<?php
session_start();
require_once 'conexion.php';

$rol = strtolower(trim($_SESSION['Rol'] ?? ''));

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    ($rol !== 'docente' && $rol !== 'administrador')
) {
    header("Location: ../PHP/miscursos.php");
    exit();
}

$idCurso = intval($_POST['ID_Curso'] ?? 0);
$tituloMaterial = trim($_POST['Titulo_Material'] ?? '');
$tipoMaterial = trim($_POST['Tipo_Material'] ?? '');
$contenidoURL = trim($_POST['Contenido_URL'] ?? '');
$idCarpeta = !empty($_POST['ID_Carpeta'])
    ? intval($_POST['ID_Carpeta'])
    : null;

if ($idCurso <= 0 || $tituloMaterial === '') {
    die("Error: faltan datos obligatorios.");
}

if (!in_array($tipoMaterial, ['archivo', 'enlace'], true)) {
    die("Error: selecciona Archivo o Enlace.");
}

$nombreArchivoFinal = null;

if ($tipoMaterial === 'archivo') {
    if (
        !isset($_FILES['archivo_adjunto']) ||
        $_FILES['archivo_adjunto']['error'] !== UPLOAD_ERR_OK
    ) {
        die("Por favor, selecciona un archivo válido.");
    }

    $archivo = $_FILES['archivo_adjunto'];

    if (!is_uploaded_file($archivo['tmp_name'])) {
        die("Error: el archivo recibido no es válido.");
    }

    $nombreOriginal = basename($archivo['name']);
    $nombreLimpio = preg_replace(
        '/[^a-zA-Z0-9._-]/',
        '_',
        $nombreOriginal
    );

    if ($nombreLimpio === '' || $nombreLimpio === '.' || $nombreLimpio === '..') {
        $nombreLimpio = 'archivo';
    }

    $nombreArchivoFinal = time() . '_' .
        bin2hex(random_bytes(5)) . '_' . $nombreLimpio;

    $dirDestino = __DIR__ . '/../PHP/Subidas/archivos/';

    if (
        !is_dir($dirDestino) &&
        !mkdir($dirDestino, 0755, true) &&
        !is_dir($dirDestino)
    ) {
        die("Error al crear la carpeta de archivos.");
    }

    $rutaCompleta = $dirDestino . $nombreArchivoFinal;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        die("Error al guardar el archivo en el servidor.");
    }
} else {
    if (
        $contenidoURL === '' ||
        !filter_var($contenidoURL, FILTER_VALIDATE_URL) ||
        !in_array(
            strtolower((string) parse_url($contenidoURL, PHP_URL_SCHEME)),
            ['http', 'https'],
            true
        )
    ) {
        die("Introduce un enlace válido que comience con https:// o http://.");
    }

    $nombreArchivoFinal = $contenidoURL;
}

try {
    $sql = "INSERT INTO materiales
            (ID_Curso, ID_Carpeta, Titulo_Material, Tipo_Material, Contenido_URL)
            VALUES
            (:ID_Curso, :ID_Carpeta, :Titulo_Material, :Tipo_Material, :Contenido_URL)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':ID_Curso' => $idCurso,
        ':ID_Carpeta' => $idCarpeta,
        ':Titulo_Material' => $tituloMaterial,
        ':Tipo_Material' => $tipoMaterial,
        ':Contenido_URL' => $nombreArchivoFinal
    ]);

    header(
        "Location: ../PHP/vercursos.php?ID_Curso=" .
        $idCurso . "&tab=materiales"
    );
    exit();
} catch (PDOException $e) {
    if (
        $tipoMaterial === 'archivo' &&
        $nombreArchivoFinal !== null
    ) {
        $rutaCompleta = __DIR__ .
            '/../PHP/Subidas/archivos/' . $nombreArchivoFinal;

        if (is_file($rutaCompleta)) {
            unlink($rutaCompleta);
        }
    }

    error_log("Error al guardar material: " . $e->getMessage());
    die("Error al guardar el material en la base de datos.");
}