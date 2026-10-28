<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../PHP conexiones/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarea | Aprendomo</title>
    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="stylesheet" href="../CSS/tareaEstilo.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main>
        <div style="padding: 40px; text-align: center;">
            <h1>Gestión de Tareas</h1>
            <p>Esta sección está en desarrollo. Aquí se mostrarán las tareas del curso.</p>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>