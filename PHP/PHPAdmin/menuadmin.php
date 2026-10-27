<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../PHP conexiones/conexion.php';

$esAdministrador = false;
$usuario = null;

if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion'];
    $sql = "SELECT Nombre, Apellido, Rol FROM usuarios WHERE Correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && strtolower(trim($usuario['Rol'])) === 'administrador') {
        $esAdministrador = true;
    }
}

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
    <title>Panel Administrador - Aprendomo</title>
    <link rel="stylesheet" href="../../CSS/estilo.css">
    <link rel="icon" href="../../IMG/Icono.ico">
</head>
<body class="admin-active">

<?php include __DIR__ . '/../menu.php'; ?>

<div class="admin-sidebar">
    <div class="admin-title">Panel de Administrador</div>
    <a class="admin-link active" href="menuAdmin.php">Inicio panel</a>
    <a class="admin-link" href="estadisticas.php">Estadísticas</a>
    <a class="admin-link" href="usuarios.php">Gestor de usuarios</a>
</div>

<main class="admin-main-content">
    <h1 style="color:#0d47a1; margin-bottom:8px;">Panel de Administración</h1>
    <p style="color:#64748b; margin-bottom:28px;">
        Bienvenido, <?php echo htmlspecialchars(($usuario['Nombre'] ?? '') . ' ' . ($usuario['Apellido'] ?? '')); ?>.
    </p>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px;">
        <a href="estadisticas.php" style="text-decoration:none; background:#fff; border:2px solid #bbdefb; border-radius:14px; padding:24px; box-shadow:0 4px 14px rgba(0,0,0,.06);">
            <h2 style="margin:0 0 8px; color:#0d47a1; font-size:1.15rem;">Estadísticas</h2>
            <p style="margin:0; color:#64748b; font-size:.95rem;">Usuarios, cursos e inscripciones.</p>
        </a>
        <a href="usuarios.php" style="text-decoration:none; background:#fff; border:2px solid #bbdefb; border-radius:14px; padding:24px; box-shadow:0 4px 14px rgba(0,0,0,.06);">
            <h2 style="margin:0 0 8px; color:#0d47a1; font-size:1.15rem;">Gestor de usuarios</h2>
            <p style="margin:0; color:#64748b; font-size:.95rem;">Editar roles, datos o eliminar cuentas.</p>
        </a>
        <a href="../cursos.php" style="text-decoration:none; background:#fff; border:2px solid #bbdefb; border-radius:14px; padding:24px; box-shadow:0 4px 14px rgba(0,0,0,.06);">
            <h2 style="margin:0 0 8px; color:#0d47a1; font-size:1.15rem;">Cursos</h2>
            <p style="margin:0; color:#64748b; font-size:.95rem;">Ir al catálogo de cursos.</p>
        </a>
    </div>
</main>

<?php include __DIR__ . '/../footer.php'; ?>

</body>
</html>