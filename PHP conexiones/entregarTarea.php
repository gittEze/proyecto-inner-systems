<?php
session_start();
require_once 'conexion.php';

// Verificar sesión y formulario
if (
    !isset($_SESSION['Sesion']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    header("Location: ../PHP/login.php");
    exit();
}

$idTarea = intval($_POST['ID_Tarea'] ?? 0);
$enlace = trim($_POST['enlace_entrega'] ?? '');
$comentario = trim($_POST['comentario_entrega'] ?? '');

if ($idTarea <= 0) {
    die("La tarea indicada no es válida.");
}

// Obtener usuario
$sqlUsuario = "SELECT ID_Usuario, Rol
               FROM usuarios
               WHERE Correo = :correo";

$stmtUsuario = $pdo->prepare($sqlUsuario);
$stmtUsuario->execute([':correo' => $_SESSION['Sesion']]);
$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$rol = strtolower(trim($usuario['Rol']));

if ($rol !== 'estudiante' && $rol !== 'docente') {
    die("Solo los estudiantes y docentes pueden entregar tareas.");
}

$idUsuario = intval($usuario['ID_Usuario']);

// Verificar tarea y obtener su fecha de bloqueo
$sqlTarea = "SELECT t.ID_Tarea, t.ID_Curso, t.Fecha_Bloqueo
             FROM tareas t
             WHERE t.ID_Tarea = :ID_Tarea";

$stmtTarea = $pdo->prepare($sqlTarea);
$stmtTarea->execute([':ID_Tarea' => $idTarea]);
$tarea = $stmtTarea->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    die("La tarea no existe.");
}

// Verificar inscripción
$sqlInscripcion = "SELECT ID_Usuario
                   FROM inscripciones
                   WHERE ID_Usuario = :ID_Usuario
                   AND ID_Curso = :ID_Curso";

$stmtInscripcion = $pdo->prepare($sqlInscripcion);
$stmtInscripcion->execute([
    ':ID_Usuario' => $idUsuario,
    ':ID_Curso' => $tarea['ID_Curso']
]);

if (!$stmtInscripcion->fetch(PDO::FETCH_ASSOC)) {
    die("No estás inscrito en este curso.");
}

// Verificar bloqueo de entrega
if (
    !empty($tarea['Fecha_Bloqueo']) &&
    time() > strtotime($tarea['Fecha_Bloqueo'])
) {
    die("El plazo para entregar esta tarea está cerrado.");
}

// Obtener entrega anterior
$sqlAnterior = "SELECT *
                FROM entregas_tareas
                WHERE ID_Tarea = :ID_Tarea
                AND ID_Usuario = :ID_Usuario";

$stmtAnterior = $pdo->prepare($sqlAnterior);
$stmtAnterior->execute([
    ':ID_Tarea' => $idTarea,
    ':ID_Usuario' => $idUsuario
]);

$entregaAnterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);

$nombreArchivo = $entregaAnterior['Nombre_Archivo'] ?? null;

// Procesar archivo, si se adjuntó
if (
    isset($_FILES['archivo_entrega']) &&
    $_FILES['archivo_entrega']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $archivo = $_FILES['archivo_entrega'];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        die("Ocurrió un error al subir el archivo.");
    }

    // Límite de 10 MB
    if ($archivo['size'] > 10 * 1024 * 1024) {
        die("El archivo no puede superar los 10 MB.");
    }

    $extension = strtolower(
        pathinfo($archivo['name'], PATHINFO_EXTENSION)
    );

    $permitidas = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx',
        'ppt', 'pptx', 'txt', 'zip', 'rar',
        'jpg', 'jpeg', 'png', 'gif'
    ];

    if (!in_array($extension, $permitidas, true)) {
        die("El formato del archivo no está permitido.");
    }

    $carpeta = __DIR__ . '/../PHP/Subidas/entregas/';

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $nombreArchivo = time() . '_' .
                     bin2hex(random_bytes(4)) . '.' .
                     $extension;

    $rutaDestino = $carpeta . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        die("No se pudo guardar el archivo.");
    }
}

// Validar enlace
if ($enlace !== '') {
    $esUrlValida = filter_var($enlace, FILTER_VALIDATE_URL);

    $protocolo = strtolower(
        parse_url($enlace, PHP_URL_SCHEME) ?? ''
    );

    if (!$esUrlValida || !in_array($protocolo, ['http', 'https'], true)) {
        die("El enlace ingresado no es válido.");
    }
}

// Exigir al menos un archivo, enlace o comentario
if (
    empty($nombreArchivo) &&
    $enlace === '' &&
    $comentario === ''
) {
    die("Debes adjuntar un archivo, ingresar un enlace o escribir un comentario.");
}

try {
    if ($entregaAnterior) {
        // Actualizar entrega existente
        $sql = "UPDATE entregas_tareas
                SET Nombre_Archivo = :Nombre_Archivo,
                    Enlace_Entrega = :Enlace_Entrega,
                    Comentario_Entrega = :Comentario_Entrega,
                    Fecha_Entrega = NOW()
                WHERE ID_Tarea = :ID_Tarea
                AND ID_Usuario = :ID_Usuario";
    } else {
        // Registrar primera entrega
        $sql = "INSERT INTO entregas_tareas
                (ID_Tarea, ID_Usuario, Nombre_Archivo,
                 Enlace_Entrega, Comentario_Entrega, Fecha_Entrega)
                VALUES
                (:ID_Tarea, :ID_Usuario, :Nombre_Archivo,
                 :Enlace_Entrega, :Comentario_Entrega, NOW())";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Tarea' => $idTarea,
        ':ID_Usuario' => $idUsuario,
        ':Nombre_Archivo' => $nombreArchivo,
        ':Enlace_Entrega' => $enlace !== '' ? $enlace : null,
        ':Comentario_Entrega' => $comentario !== '' ? $comentario : null
    ]);

    header(
        "Location: ../PHP/tarea.php?ID_Tarea=" .
        $idTarea . "&entrega=ok"
    );
    exit();

} catch (PDOException $e) {
    die("Error al guardar la entrega.");
}
?>
