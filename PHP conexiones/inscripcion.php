<?php
session_start();
require_once 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

// Debe estar logueado
if (!isset($_SESSION['Sesion'])) {
    header("Location: ../PHP/login.php");  
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ID_Curso'])) {

    $correo  = $_SESSION['Sesion'];
    $idCurso = intval($_POST['ID_Curso']);

    // Obtener datos completos del usuario
    $stmtUser = $pdo->prepare("SELECT ID_Usuario, Nombre, Correo FROM usuarios WHERE Correo = :Correo");
    $stmtUser->execute([':Correo' => $correo]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $idUsuario = $usuario['ID_Usuario'];

        // Insertar inscripción (con Fecha_Inscripcion)
        $sql = "INSERT IGNORE INTO inscripciones (ID_Usuario, ID_Curso, Fecha_Inscripcion) 
                VALUES (:ID_Usuario, :ID_Curso, NOW())";
        $stmtIns = $pdo->prepare($sql);
        $stmtIns->execute([
            ':ID_Usuario' => $idUsuario,
            ':ID_Curso'   => $idCurso
        ]);
        
        // Datos del curso para el correo
        $stmtCurso = $pdo->prepare("SELECT Titulo_Curso FROM cursos WHERE ID_Curso = :ID_Curso");
        $stmtCurso->execute([':ID_Curso' => $idCurso]);
        $cursoInfo = $stmtCurso->fetch(PDO::FETCH_ASSOC);

        if ($cursoInfo) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.gmail.com';                     
                $mail->SMTPAuth   = true;                                 
                $mail->Username   = 'innersystems.contacto@gmail.com';                   
                $mail->Password   = 'oovw zjot xarz gffh';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;                        

                $mail->setFrom('aprendomo@gmail.com', 'Aprendomo');
                $mail->addAddress($usuario['Correo'], $usuario['Nombre']);

                $mail->isHTML(true);                                 
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Inscripción Exitosa - Aprendomo';
                $mail->Body    = 'Hola <b>' . htmlspecialchars($usuario['Nombre']) . '</b>,<br><br>' .
                                 'Te has inscrito correctamente al curso: <b>' . htmlspecialchars($cursoInfo['Titulo_Curso']) . '</b>.<br>' .
                                 'Ya puedes acceder a los contenidos desde la sección "Mis cursos".';

                $mail->send();
            } catch (Exception $e) {
                // Si falla el correo, no interrumpimos el flujo
            }
        }
    }
    
    header("Location: ../PHP/miscursos.php");
    exit();

} else {
    header("Location: ../PHP/cursos.php");
    exit();
}
?>