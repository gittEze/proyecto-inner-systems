<?php
session_start();
require_once 'conexion.php';

// Solo docente o administrador
$rol = strtolower(trim($_SESSION['Rol'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($rol !== 'docente' && $rol !== 'administrador')) {
    header("Location: ../PHP/miscursos.php");
    exit();
}

// Datos del formulario
$idCurso          = intval($_POST['ID_Curso'] ?? 0);
$nombreTarea      = trim($_POST['nombre_tarea'] ?? '');
$descripcionTarea = trim($_POST['descripcion_tarea'] ?? '');
$enlaceTarea      = trim($_POST['enlace_tarea'] ?? '');
$fechaVencimiento = $_POST['fecha_vencimiento'] ?? '';
$fechaBloqueo     = $_POST['fecha_bloqueo'] ?? '';
$idCarpeta        = !empty($_POST['ID_Carpeta']) ? intval($_POST['ID_Carpeta']) : null;

// Validación
if ($idCurso <= 0 || empty($nombreTarea) || empty($fechaVencimiento)) {
    die("Error: Faltan datos obligatorios (nombre, fecha de vencimiento o curso).");
}

// Formato de fechas para MySQL
$fechaVencimiento = str_replace('T', ' ', $fechaVencimiento);
if (strlen($fechaVencimiento) === 16) {
    $fechaVencimiento .= ':00';
}

if (!empty($fechaBloqueo)) {
    $fechaBloqueo = str_replace('T', ' ', $fechaBloqueo);
    if (strlen($fechaBloqueo) === 16) {
        $fechaBloqueo .= ':00';
    }
} else {
    $fechaBloqueo = null;
}

try {
    // INSERT con ID_Carpeta
    $sql = "INSERT INTO tareas
            (ID_Curso, ID_Carpeta, Nombre_Tarea, Descripcion_Tarea, Enlace_Tarea, Fecha_Vencimiento, Fecha_Bloqueo)
            VALUES
            (:ID_Curso, :ID_Carpeta, :Nombre_Tarea, :Descripcion_Tarea, :Enlace_Tarea, :Fecha_Vencimiento, :Fecha_Bloqueo)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ID_Curso'          => $idCurso,
        ':ID_Carpeta'        => $idCarpeta,
        ':Nombre_Tarea'      => $nombreTarea,
        ':Descripcion_Tarea' => $descripcionTarea ?: null,
        ':Enlace_Tarea'      => $enlaceTarea ?: null,
        ':Fecha_Vencimiento' => $fechaVencimiento,
        ':Fecha_Bloqueo'     => $fechaBloqueo
    ]);

    $idTarea = $pdo->lastInsertId();

    // Archivos adjuntos
    if (!empty($_FILES['archivos_tarea']['name'][0])) {

        $carpetaDestino = __DIR__ . '/../PHP/Subidas/archivos/';

        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        $permitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'gif'];

        foreach ($_FILES['archivos_tarea']['name'] as $index => $nombreOriginal) {
            if ($_FILES['archivos_tarea']['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
            if (!in_array($extension, $permitidas)) {
                continue;
            }

            $nombreArchivo = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $rutaFinal = $carpetaDestino . $nombreArchivo;

            if (move_uploaded_file($_FILES['archivos_tarea']['tmp_name'][$index], $rutaFinal)) {
                $sqlArchivo = "INSERT INTO archivos_tarea (ID_Tarea, Nombre_Archivo, Ruta_Archivo)
                               VALUES (:ID_Tarea, :Nombre_Archivo, :Ruta_Archivo)";
                $stmtArchivo = $pdo->prepare($sqlArchivo);
                $stmtArchivo->execute([
                    ':ID_Tarea'       => $idTarea,
                    ':Nombre_Archivo' => $nombreOriginal,
                    ':Ruta_Archivo'   => $nombreArchivo
                ]);
            }
        }
    }

    // Redirigir a la pestaña donde SÍ se ven las tareas
    header("Location: ../PHP/vercursos.php?ID_Curso=" . $idCurso . "&tab=calificaciones&tarea=ok");
    exit();

} catch (PDOException $e) {
    die("Error al guardar la tarea: " . $e->getMessage());
}