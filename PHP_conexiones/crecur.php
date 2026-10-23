<?php

session_start();

require ('conexion.php');
require_once ('../PHP/clases/Curso.php');

if (!isset($_SESSION['ID_Usuario']) || empty($_SESSION['ID_Usuario'])) {
    die("Error: Tu sesión ha expirado o no estás autenticado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idDocente = $_SESSION['ID_Usuario'];
    $nombreImagen = '';

    if(isset($_FILES['Dataso']) && $_FILES['Dataso']['error'] === 0){

        $nombreImagen = time() . "-" . $_FILES['Dataso']['name'];
        $tmp = $_FILES['Dataso']['tmp_name'];
        $rutaDestino = __DIR__ . "/Imagenes/" . $nombreImagen;
        move_uploaded_file($tmp, $rutaDestino);
    }

    $curso = new Curso(
        $_POST['Titulo_Curso'],
        $_POST['Descripcion_Curso'],
        $_POST['Tipo_Curso'],
        $_POST['Nivel_Curso'],
        $_POST['Duracion_Estimada'],
        $_POST['Precio'],
        $nombreImagen,
        $idDocente
    );
    $curso->guardar($pdo);
}

header("Location: ../PHP/cursos.php");
exit();