<?php

// Se habre una sessión.
session_start();

// Se incluye la conexión a la Base de datos para la funcionalidad del archivo.
require_once 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

// Condicional isset que evalua si la sesion se inicio.

// Es necesario inscribirse para entrar a un curso.
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
        
        // Consultar el título del curso al que se inscribió
        $stmtCurso = $pdo->prepare("SELECT Titulo_Curso FROM cursos WHERE ID_Curso = :ID_Curso");
        $stmtCurso->execute([':ID_Curso' => $idCurso]);
        $cursoInfo = $stmtCurso->fetch(PDO::FETCH_ASSOC);

        if ($cursoInfo) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.gmail.com';                     
                $mail->SMTPAuth   = true;                                 
                $mail->Username   = 'innersystems.contacto@gmail.com';                   
                $mail->Password   = 'oovw zjot xarz gffh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;                        

                $mail->setFrom('aprendomo@gmail.com', 'Empresa Inner');
                $mail->addAddress($usuario['Correo'], $usuario['Nombre']);

                $mail->isHTML(true);                                 
                $mail->CharSet ='UTF-8';
                $mail->Subject = 'Inscripción Exitosa - Aprendomo';
                $mail->Body    = 'Hola <b>' . htmlspecialchars($usuario['Nombre']) . '</b>,<br><br>' .
                                 'Te has inscrito correctamente al curso: <b>' . htmlspecialchars($cursoInfo['Titulo_Curso']) . '</b>.<br>' .
                                 'Ya puedes acceder a los contenidos desde la sección "Mis cursos".';

                $mail->send();
            } catch (Exception $e) {
                // Manejo de error silencioso para no interrumpir el flujo de redirección si falla internet
            }
        }
    }
    
    header("Location: ../PHP/miscursos.php");
    exit();

} else {
    header("Location: ../PHP/cursos.php");
    exit();
}