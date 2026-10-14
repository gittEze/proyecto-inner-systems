<?php
require_once ('conexion.php');

// Verificar la variable $pdo en lugar de $conexion
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die('No se pudo establecer la conexión a la base de datos.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

$email = $_POST['Correo'] ?? '';

// Consulta con sentencia preparada PDO
$query = "SELECT ID_Usuario, Nombre, Apellido, Correo FROM usuarios WHERE Correo = :correo";
$stmt = $pdo->prepare($query);
$stmt->execute([':correo' => $email]);

$usuario = $stmt->fetch();

if ($usuario) {
    $idUsuario = $usuario['ID_Usuario'];
    $nombreUsuario = $usuario['Nombre'] . ' ' . $usuario['Apellido'];

    $token = bin2hex(random_bytes(32));
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Actualizar token con PDO
    $updateQuery = "UPDATE usuarios SET token_recuperacion = :token, token_expiracion = :expiracion WHERE ID_Usuario = :id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([
        ':token' => $token,
        ':expiracion' => $expiracion,
        ':id' => $idUsuario
    ]);

    $enlaceRecuperacion = "http://localhost/proyecto-inner-systems/PHP/restablecer.php?token=" . $token;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = 'aprendomo@gmail.com';                   
        $mail->Password   = 'Aprendomitos_';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                               
        $mail->Port       = 587;       
        

        $mail->setFrom('aprendomo@gmail.com', 'Empresa Inner');
        $mail->addAddress($usuario['Correo'], $nombreUsuario);

        $mail->isHTML(true);                                 
        $mail->CharSet ='UTF-8';
        $mail->Subject = 'Recuperación de Contraseña - Empresa Inner';
        $mail->Body    = 'Hola <b>' . htmlspecialchars($nombreUsuario) . '</b>,<br><br>' .
                         'Hemos recibido una solicitud para restablecer tu contraseña.<br>' .
                         'Haz clic en el siguiente enlace para continuar con el proceso:<br><br>' .
                         '<a href="' . $enlaceRecuperacion . '" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">Restablecer Contraseña</a><br><br>' .
                         'Este enlace vencerá en 1 hora.';
        $mail->AltBody = 'Hola ' . $nombreUsuario . ', has solicitado restablecer tu contraseña.';

        $mail->send();
        echo 'El enlace de recuperación ha sido enviado a su correo.';
    } catch (Exception $e) {
        echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
    }    

} else {
    header("Location: ../PHP/login.php");
    exit;
}
?>