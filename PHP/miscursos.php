<?php
// Inicia la sesión 
session_start();
require_once '../PHP conexiones/conexion.php';

// Validar que exista la sesión del usuario, en cada de que exista la sesión, el usuario es enviado a login si la sesion existe. 
if (!isset($_SESSION['Sesion'])) {
    header("Location: login.php");
    exit();
}

//Variables que toman la sessión y el id del usaurio.
$correo = $_SESSION['Sesion'];
$ID_Usuario = $_SESSION['ID_Usuario'] ?? 0;

// Consulta con UNION que pemite juntar los cursos inscriptos mas los cursos creados por el docente,
// De esta forma el usaurio puede ver lo que creo o esta inscripto sin importar el rol.
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
    ':Correo' => $correo,
    ':ID_Usuario'  => $ID_Usuario
]);

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
                    <a href="vercursos.php?idCurso=<?= $c['ID_Curso'] ?>" class="card-curso">
                        <img src="../PHP conexiones/Imagenes/<?= !empty($c['Dataso']) ? htmlspecialchars($c['Dataso']) : 'default.png' ?>" alt="Portada">
                        <div class="card-curso-body">
                            <h3><?= htmlspecialchars($c['Titulo_Curso']) ?></h3>
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