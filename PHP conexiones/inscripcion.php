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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['idCurso'])) {
    $correo = $_SESSION['Sesion'];
    $idCurso = intval($_POST['idCurso']);

    // Esta consulta se encarga de obtener el id del usuario que se logueo en la plataforma
    $stmtUser = $pdo->prepare("SELECT idUsu FROM usuario WHERE Correo = :correo");
    $stmtUser->execute([':correo' => $correo]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $idUsu = $usuario['idUsu'];

    // Esta consulta inserta los id de Usuario y Curso en la tabla inscipcion, sirviendo como punto de contacto para saber si el alumno se inscribio.  
        $sql = "INSERT IGNORE INTO inscripcion (idUsu, idCurso) VALUES (:idUsu, :idCurso)";
        $stmtIns = $pdo->prepare($sql);
        $stmtIns->execute([
            ':idUsu' => $idUsu,
            ':idCurso' => $idCurso
        ]);
    }
    header("Location: ../PHP/mis_cursos.php");
    exit();

} else {
    header("Location: ../PHP/cursos.php");
    exit();
}