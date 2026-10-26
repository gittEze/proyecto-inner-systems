<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../PHP conexiones/conexion.php';

$esAdministrador = false;
$usuario = null;

// Verificar si la sesión del usuario está activa
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

// Solo administradores pueden acceder
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
<body class="admin-active">


<?php include __DIR__ . '/../menu.php'; ?>
 
<div class="admin-sidebar">
    <div class="admin-title">Panel de Administrador</div>

    <a class="admin-link" href="estadisticas.php">Estadísticas</a>
    <a class="admin-link" href="usuarios.php">Gestor de usuarios</a>
</div>

    <!-- Contenido principal -->
    <main>
        <!-- Aquí va el contenido de cada página del panel.
             Por ahora lo dejamos vacío o con un mensaje de bienvenida -->
        <div style="padding: 40px;">
            <h1>Panel de Administración</h1>
            <p>Bienvenido al panel de administración de Aprendomo.</p>
        </div>
    </main>

    <!-- Footer (fuera de main) -->
    <?php include '../footer.php'; ?>

</body>
</html>