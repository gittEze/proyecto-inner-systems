<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../PHP_conexiones/conexion.php';

$esAdministrador = false;
$usuario = null;

if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion'];

    $sql = "SELECT Nombre, Apellido, Rol FROM usuarios WHERE Correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['Rol'] === 'Administrador') {
        $esAdministrador = true;
    }
}

// Condicional encargado de verificar si el rol es administrador.
if (!$esAdministrador) {
    header("Location: ../main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="../../CSS/estilo.css">
</head>
<body>
<main>

<?php include '../menu.php'; ?>

<!-- Contenedor cambiado a una clase semántica para la barra lateral de administración -->
<div class="admin-sidebar">

    <a class="admin-link" href="estadisticas.php">
        Estadísticas
    </a>

    <a class="admin-link" href="usuarios.php">
        Gestor de usuarios
    </a>

    <!-- Nota: Tenías este enlace repetido apuntando a usuarios.php, ajústalo según necesites -->
    <a class="admin-link" href="usuarios.php">
        Gestor de usuarios
    </a>
</div>


</main>
</body>
</html>