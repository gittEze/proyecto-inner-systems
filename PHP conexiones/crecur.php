<?php

session_start();

require ('conexion.php');
require_once ('../PHP/clases/Curso.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

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

$stmtDocente = $pdo->prepare("SELECT Nombre, Apellido, Correo FROM usuarios WHERE ID_Usuario = :id");
    $stmtDocente->execute([':id' => $idDocente]);
    $docente = $stmtDocente->fetch(PDO::FETCH_ASSOC);

    if ($docente) {

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
            $mail->addAddress($docente['Correo'], $docente['Nombre']);

            $mail->isHTML(true);                                 
            $mail->CharSet ='UTF-8';
            $mail->Subject = 'Curso creado con éxito - Aprendomo';
            $mail->Body    = 'Hola profesor/a <b>' . htmlspecialchars($docente['Nombre'] . ' ' . $docente['Apellido']) . '</b>,<br><br>' .
                             'Tu curso ha sido creado y guardado exitosamente en la plataforma.<br>' .
                             'Los estudiantes ya podrán visualizarlo.';

            $mail->send();
        } catch (Exception $e) {
            // Manejo de error silencioso o registro
        }
    }
}

header("Location: ../PHP/cursos.php");
exit();