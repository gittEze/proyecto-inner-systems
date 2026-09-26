<?php

// Se habre una sessión.
session_start();

// Se inclue la conexión a la Base de datos para la funcionalidad del archivo.
require_once 'conexion.php';

// Condicional isset que evalua si la sesion se inicio.
if (!isset($_SESSION['Sesion'])) {
    header("Location: ../PHP/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ID_Curso'])) {
    $correo = $_SESSION['Sesion'];
    $idCurso = intval($_POST['ID_Curso']);

    // Esta consulta se encarga de obtener el id del usuario que se logueo en la plataforma
    $stmtUser = $pdo->prepare("SELECT ID_Usuario FROM usuarios WHERE Correo = :Correo");
    $stmtUser->execute([':Correo' => $correo]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $idUsuario = $usuario['ID_Usuario'];

    // Esta consulta inserta los id de Usuario y Curso en la tabla inscipcion, sirviendo como punto de contacto para saber si el alumno se inscribio.  
        $sql = "INSERT IGNORE INTO inscripciones (ID_Usuario, ID_Curso) VALUES (:ID_Usuario, :ID_Curso)";
        $stmtIns = $pdo->prepare($sql);
        $stmtIns->execute([
            ':ID_Usuario' => $idUsuario,
            ':ID_Curso' => $idCurso
        ]);
    }
    header("Location: ../PHP/miscursos.php");
    exit();

} else {
    header("Location: ../PHP/cursos.php");
    exit();
}