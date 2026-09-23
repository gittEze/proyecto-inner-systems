<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Validar que exista la sesión del usuario
if (!isset($_SESSION['Sesion'])) {
    header("Location: login.php");
    exit();
}

$correo = $_SESSION['Sesion'];

// Consulta SQL con JOIN para obtener solo los cursos en los que está inscripto el usuario actual
$sql = "SELECT c.* 
        FROM cursos c
        INNER JOIN inscripcion i ON c.idCurso = i.idCurso
        INNER JOIN usuario u ON i.idUsu = u.idUsu
        WHERE u.Correo = :correo
        ORDER BY c.idCurso DESC";

        

$stmt = $pdo->prepare($sql);
$stmt->execute([':correo' => $correo]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos | LMS</title>
    <link rel="stylesheet" href="../CSS/estilo.css">

</head>
<body>

    <?php include 'menu.php'; ?>


    <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
        <h2>Mis Cursos</h2>
        
        <div class="grid-cursos">
            <?php if (count($cursos) > 0): ?>
                <?php foreach ($cursos as $c): ?>
                    <!-- Enlace conectado directamente a vercursos.php pasándole el idCurso -->
                    <a href="vercursos.php?idCurso=<?= $c['idCurso'] ?>" class="card-curso">
                        <img src="../PHP conexiones/Imagenes/<?= !empty($c['Dataso']) ? htmlspecialchars($c['Dataso']) : 'default.png' ?>" alt="Portada">
                        <div class="card-curso-body">
                            <h3><?= htmlspecialchars($c['Titulo_curso']) ?></h3>
                            <small class="text-muted"><?= htmlspecialchars($c['Nivel_Curso'] ?? $c['Nivel_curso'] ?? '') ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Todavía no te has inscripto a ningún curso.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>