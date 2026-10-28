<?php
session_start();
require_once 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';


if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario'])) {
    header('Location: ../PHP/login.php');
    exit();
}


if (!isset($_GET['paymentID']) || empty($_GET['ID_Curso'])) {
    header('Location: ../PHP/cursos.php');
    exit();
}

$idUsuario = intval($_SESSION['ID_Usuario']);
$idCurso   = intval($_GET['ID_Curso']);
$paymentID = htmlspecialchars($_GET['paymentID']);

try {

    $check = $pdo->prepare("SELECT 1 FROM inscripciones WHERE ID_Usuario = :u AND ID_Curso = :c");
    $check->execute([':u' => $idUsuario, ':c' => $idCurso]);
    
    if (!$check->fetch()) {

        $sql = "INSERT INTO inscripciones (ID_Usuario, ID_Curso, Fecha_Inscripcion) 
                VALUES (:ID_Usuario, :ID_Curso, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ID_Usuario' => $idUsuario,
            ':ID_Curso'   => $idCurso
        ]);
    }


    $stmtUser = $pdo->prepare("SELECT Nombre, Correo FROM usuarios WHERE ID_Usuario = :ID_Usuario");
    $stmtUser->execute([':ID_Usuario' => $idUsuario]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);


    $stmtCurso = $pdo->prepare("SELECT Titulo_Curso, Precio FROM cursos WHERE ID_Curso = :ID_Curso");
    $stmtCurso->execute([':ID_Curso' => $idCurso]);
    $cursoInfo = $stmtCurso->fetch(PDO::FETCH_ASSOC);


    if ($usuario && $cursoInfo) {
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
            $mail->Subject = '¡Pago Exitoso e Inscripción Confirmada - Aprendomo!';
            $mail->Body    = 'Hola <b>' . htmlspecialchars($usuario['Nombre']) . '</b>,<br><br>' .
                             '¡Tu pago a través de PayPal se ha procesado correctamente!<br>' .
                             'Te has inscrito exitosamente al curso: <b>' . htmlspecialchars($cursoInfo['Titulo_Curso']) . '</b>.<br>' .
                             '<b>Monto pagado:</b> $' . htmlspecialchars($cursoInfo['Precio']) . ' USD<br>' .
                             '<b>ID de Transacción:</b> ' . $paymentID . '<br><br>' .
                             'Ya puedes acceder a los contenidos desde la sección "Mis cursos".';

            $mail->send();
        } catch (Exception $e) {

        }
    }

    echo "<script>alert('¡Pago exitoso con PayPal! Te has inscripto correctamente al curso y se ha enviado un correo de confirmación.'); window.location.href='../PHP/miscursos.php';</script>";
    exit();

} catch (PDOException $e) {
    echo "<script>alert('Error al registrar la inscripción. Por favor inténtalo de nuevo.'); window.location.href='../PHP/cursos.php';</script>";
    exit();
}
?>