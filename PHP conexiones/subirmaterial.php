<?php
session_start();
require_once 'conexion.php';

// Estrucutra encargada de validar que la petición venga de un docente o administrador
$rol = strtolower(trim($_SESSION['Rol'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($rol !== 'docente' && $rol !== 'administrador')) {
    header("Location: ../PHP/miscursos.php");
    exit();
}

$idCurso = intval($_POST['ID_Curso'] ?? 0);
$tituloMaterial = trim($_POST['Titulo_Material'] ?? '');


if ($idCurso <= 0 || empty($tituloMaterial)) {
    die("Error: Faltan datos obligatorios.");
}

// Procesar el archivo subido
if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
    $nombreOriginal = $_FILES['archivo_adjunto']['name'];
    $tmpName = $_FILES['archivo_adjunto']['tmp_name'];
    $extension= strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    // Determinar el tipo de material y la subcarpeta adecuada según la extensión
    $tipoMaterial = 'archivo';
    $subCarpeta = 'archivos/';

    if (in_array($extension, ['mp4', 'webm', 'ogg', 'avi', 'mkv'])) {
        $tipoMaterial = 'video';
        $subCarpeta = 'videos/';
    } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a'])) {
        $tipoMaterial = 'audio';
        $subCarpeta = 'audios/';
    }


    // Ruta de la carpeta donde se almacenarán los archivos
    $dirDestino = __DIR__ . '/../PHP/Subidas/' . $subCarpeta;

    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0777, true);
    }

    // Nombre único para evitar sobreescribir archivos existentes
    $nombreArchivo = time() . '_' . $nombreOriginal;
    $rutaCompleta = $dirDestino . $nombreArchivo;

    if (move_uploaded_file($tmpName, $rutaCompleta)) {
        // Insertar registro en la base de datos
        $sql = "INSERT INTO materiales (ID_Curso, Titulo_Material, Tipo_Material, Contenido_URL) 
                VALUES (:ID_Curso, :Titulo_Material, :Tipo_Material, :Contenido_URL)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
        $stmt->bindParam(':Titulo_Material', $tituloMaterial, PDO::PARAM_STR);
        $stmt->bindParam(':Tipo_Material', $tipoMaterial, PDO::PARAM_STR);
        $stmt->bindParam(':Contenido_URL', $nombreArchivo, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header("Location: ../PHP/vercursos.php?ID_Curso=" . $idCurso . "&tab=materiales");
            exit();
        } else {
            echo "Error al guardar los datos en la base de datos.";
        }
    } else {
        echo "Error al guardar el archivo en el servidor.";
    }
} else {
    echo "Por favor selecciona un archivo válido.";
}
?>