<?php
session_start();
require_once 'conexion.php';

// Solo docentes y administradores
$rol = strtolower(trim($_SESSION['Rol'] ?? ''));
if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario']) || ($rol !== 'docente' && $rol !== 'administrador')) {
    header('Location: ../PHP/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../PHP/crear.php');
    exit();
}

$titulo       = trim($_POST['Titulo_Curso'] ?? '');
$descripcion  = trim($_POST['Descripcion_Curso'] ?? '');
$tipo         = trim($_POST['Tipo_Curso'] ?? '');
$nivel        = trim($_POST['Nivel_Curso'] ?? '');
$duracion     = (int)($_POST['Duracion_Estimada'] ?? 0);
$precio       = (int)($_POST['Precio'] ?? 0);
$idDocente    = (int)$_SESSION['ID_Usuario'];

if (empty($titulo) || empty($descripcion) || empty($tipo) || empty($nivel) || $duracion <= 0 || $precio <= 0) {
    header('Location: ../PHP/crear.php?error=campos');
    exit();
}

// Manejo de la imagen
$nombreImagen = null;

if (isset($_FILES['Dataso']) && $_FILES['Dataso']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['Dataso'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $permitidas)) {
        header('Location: ../PHP/crear.php?error=imagen');
        exit();
    }

    // Crear carpeta si no existe
    $carpetaDestino = __DIR__ . '/Imagenes/';
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }

    $nombreImagen = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $rutaFinal = $carpetaDestino . $nombreImagen;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
        header('Location: ../PHP/crear.php?error=subida');
        exit();
    }
} else {
    header('Location: ../PHP/crear.php?error=imagen');
    exit();
}

try {
    $sql = "INSERT INTO cursos 
            (Titulo_Curso, Descripcion_Curso, Tipo_Curso, Nivel_Curso, Duracion_Estimada, Precio, Dataso, ID_Docente)
            VALUES 
            (:titulo, :descripcion, :tipo, :nivel, :duracion, :precio, :imagen, :idDocente)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo'      => $titulo,
        ':descripcion' => $descripcion,
        ':tipo'        => $tipo,
        ':nivel'       => $nivel,
        ':duracion'    => $duracion,
        ':precio'      => $precio,
        ':imagen'      => $nombreImagen,
        ':idDocente'   => $idDocente
    ]);

    header('Location: ../PHP/cursos.php?exito=1');
    exit();

} catch (PDOException $e) {
    // Si falla, borramos la imagen subida
    if ($nombreImagen && file_exists($carpetaDestino . $nombreImagen)) {
        unlink($carpetaDestino . $nombreImagen);
    }
    die("Error al guardar el curso: " . $e->getMessage());
}
?>