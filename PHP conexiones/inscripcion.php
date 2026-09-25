<?php
session_start();
require_once 'conexion.php';


if (!isset($_SESSION['Sesion'])) {
    header("Location: ../PHP/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ID_Curso'])) {
    $correo = $_SESSION['Sesion'];
    $ID_Curso = intval($_POST['ID_Curso']);

    // Esta consulta se encarga de obtener el id del usuario que se logueo en la plataforma
    $stmtUser = $pdo->prepare("SELECT ID_Usuario FROM usuarios WHERE Correo = :correo");
    $stmtUser->execute([':correo' => $correo]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $ID_Usuario = $usuario['ID_Usuario'];;

    // Esta consulta inserta los id de Usuario y Curso en la tabla inscipcion, sirviendo como punto de contacto para saber si el alumno se inscribio.  
        $sql = "INSERT IGNORE INTO inscripciones (ID_Usuario, ID_Curso, Fecha_Inscripcion) VALUES (:ID_Usuario, :ID_Curso, NOW())";
        $stmtIns = $pdo->prepare($sql);
        $stmtIns->execute([
            ':ID_Usuario' => $ID_Usuario,
            ':ID_Curso' => $ID_Curso
        ]);
    }


    header("Location: ../PHP/mis_cursos.php");
    exit();
} else {
    header("Location: ../PHP/cursos.php");
    exit();
}