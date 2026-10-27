<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Validar que exista la sesión
if (!isset($_SESSION['Sesion'])) {
    header("Location: login.php");
    exit();
}

$correo     = $_SESSION['Sesion'];
$ID_Usuario = $_SESSION['ID_Usuario'] ?? 0;

// Cursos en los que está inscrito + cursos que creó como docente
$sql = "SELECT c.* 
        FROM cursos c
        INNER JOIN inscripciones i ON c.ID_Curso = i.ID_Curso
        INNER JOIN usuarios u ON i.ID_Usuario = u.ID_Usuario
        WHERE u.Correo = :Correo

        UNION

        SELECT c.* 
        FROM cursos c
        WHERE c.ID_Docente = :ID_Usuario

        ORDER BY ID_Curso DESC";
    
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':Correo'     => $correo,
    ':ID_Usuario' => $ID_Usuario
]);

$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos | Aprendomo</title>
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
        <h2>Mis Cursos</h2>
        
        <div class="grid-cursos">
            <?php if (count($cursos) > 0): ?>
                <?php foreach ($cursos as $c): ?>
                    <a href="vercursos.php?ID_Curso=<?= $c['ID_Curso'] ?>" class="card-curso">
                        <?php if (!empty($c['Dataso'])): ?>
                            <img src="../PHP conexiones/Imagenes/<?= htmlspecialchars($c['Dataso']) ?>" alt="Portada">
                        <?php else: ?>
                            <img src="../IMG/Curso1.png" alt="Portada">
                        <?php endif; ?>
                        <div class="card-curso-body">
                            <h3><?= htmlspecialchars($c['Titulo_Curso']) ?></h3>
                            <small class="text-muted"><?= htmlspecialchars($c['Nivel_Curso'] ?? '') ?></small>
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