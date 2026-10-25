<?php

require ('conexion.php');

// Validación para espacios de los datos insertados por medio de trim.
$nombre = trim($_POST['Nombre'] ?? '');
$apellido = trim($_POST['Apellido'] ?? '');
$cedula = trim($_POST['Cedula'] ?? '');
$fecha_de_nacimiento = trim($_POST['Fecha_De_Nacimiento'] ?? '');
$correo = trim($_POST['Correo'] ?? '');
$contrasena = trim($_POST['Contrasenia'] ?? '');
$telefono = trim($_POST['Telefono'] ?? '');
$genero = trim($_POST['Genero'] ?? '');
$rol = trim($_POST['Rol'] ?? '');

// Estructura condicional que permite verificar si los campos están vacíos o no.
if(empty($nombre) || empty($apellido) || empty($cedula) || empty($fecha_de_nacimiento) || empty($correo) || empty($contrasena) || empty($telefono) || empty($genero) || empty($rol)){
    header("Location: ../PHP/login.php?pagina=register&error=campos_vacios");
    exit();
}

// Estructura condicional encargada de verificar si los datos tipo "int" son numéricos.
if(!is_numeric($cedula) || !is_numeric($telefono)){
    header("Location: ../PHP/login.php?pagina=register&error=formato_invalido");
    exit();
}

// Saneamiento de correo
$correo_saneado = filter_var($correo, FILTER_SANITIZE_EMAIL);

// -------------------------------------------------------------
// VERIFICACIÓN: Comprobar si el correo ya existe en la base de datos
// -------------------------------------------------------------
$sql_check = "SELECT ID_Usuario FROM usuarios WHERE Correo = :correo";
$consulta_check = $pdo->prepare($sql_check);
$consulta_check->bindParam(':correo', $correo_saneado, PDO::PARAM_STR);
$consulta_check->execute();

if($consulta_check->rowCount() > 0) {
    // Si ya existe, redirigir al formulario de registro pasando el error en la URL
    header("Location: ../PHP/login.php?pagina=register&error=correo_existe");
    exit();
}

// Hash a la contraseña
$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

// Inserción en la base de datos
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO usuarios (Nombre, Apellido, Cedula, Fecha_De_Nacimiento, Correo, Contrasenia, Telefono, Genero, Rol) VALUES (:nombre, :apellido, :cedula, :fecha_de_nacimiento, :correo, :contrasenia, :telefono, :genero, :rol)';
    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $consulta->bindParam(':apellido', $apellido, PDO::PARAM_STR);
    $consulta->bindParam(':cedula', $cedula, PDO::PARAM_INT);
    $consulta->bindParam(':fecha_de_nacimiento', $fecha_de_nacimiento, PDO::PARAM_STR);
    $consulta->bindParam(':correo', $correo_saneado, PDO::PARAM_STR);
    $consulta->bindParam(':contrasenia', $contrasena_hash, PDO::PARAM_STR);
    $consulta->bindParam(':telefono', $telefono, PDO::PARAM_INT);
    $consulta->bindParam(':genero', $genero, PDO::PARAM_STR);
    $consulta->bindParam(':rol', $rol, PDO::PARAM_STR);

    $consulta->execute();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

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
    $mail->addAddress($correo_saneado, $nombre);

    $mail->isHTML(true);                                 
    $mail->CharSet ='UTF-8';
    $mail->Subject = '¡Bienvenido a Aprendomo!';
    $mail->Body    = 'Hola <b>' . htmlspecialchars($nombre) . '</b>,<br><br>' .
                     'Te has registrado exitosamente en nuestra plataforma.<br>' .
                     'Ya puedes iniciar sesión con tu cuenta.<br><br>¡Mucho éxito!';

    $mail->send();
} catch (Exception $e) {
    // Si falla el correo, el registro continúa con normalidad
}

// Redirección con éxito al login
header("Location: ../PHP/login.php?pagina=login&exito=1");
exit();
?>