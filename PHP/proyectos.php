<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Listado de proyectos educativos
$sql = "SELECT p.*,
               c.Titulo_Curso,
               c.Dataso AS Imagen_Curso,
               c.Tipo_Curso,
               c.Nivel_Curso,
               c.Precio,
               u.Nombre AS Nombre_Docente,
               u.Apellido AS Apellido_Docente
        FROM proyectos_educativos p
        INNER JOIN cursos c ON c.ID_Curso = p.ID_Curso
        INNER JOIN usuarios u ON u.ID_Usuario = p.ID_Docente
        ORDER BY p.Fecha_Publicacion DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $proyectos = [];
    $errorTabla = true;
}

// Detalle de un proyecto concreto
$idProyectoVer = isset($_GET['ID_Proyecto']) ? intval($_GET['ID_Proyecto']) : 0;
$proyectoDetalle = null;

if ($idProyectoVer > 0) {
    foreach ($proyectos as $p) {
        if (intval($p['ID_Proyecto']) === $idProyectoVer) {
            $proyectoDetalle = $p;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprendomo - Proyectos Educativos</title>
    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="main-proyectos">
        <h1 class="h1cur">Proyectos educativos</h1>
        <p class="pcur" style="text-align: center; max-width: 700px; margin: 0 auto 25px;">
            Proyectos realizados por docentes y estudiantes al finalizar un curso.
            Explorá el trabajo final y, si te interesa, inscribite en el curso donde se desarrolló.
        </p>

        <?php if (!empty($errorTabla)): ?>
            <div class="logrado" style="background: #fff3cd; color: #856404;">
                Aún no está creada la tabla de proyectos en la base de datos.
                Ejecutá el SQL de <code>SQL/consultas.sql</code> (tabla <strong>proyectos_educativos</strong>).
            </div>
        <?php endif; ?>

        <?php if ($proyectoDetalle): ?>
            <!-- Vista detalle -->
            <section class="proyecto-detalle">
                <a href="proyectos.php" class="btn-cancelar" style="display: inline-block; margin-bottom: 15px;">← Volver al listado</a>

                <article class="card-proyecto card-proyecto-detalle">
                    <div class="proyecto-portada">
                        <?php
                            if (!empty($proyectoDetalle['Imagen_Portada'])) {
                                $img = 'Subidas/proyectos/portadas/' .
                                    rawurlencode(basename($proyectoDetalle['Imagen_Portada']));
                            } elseif (!empty($proyectoDetalle['Imagen_Curso'])) {
                                $img = '../PHP conexiones/Imagenes/' .
                                    rawurlencode(basename($proyectoDetalle['Imagen_Curso']));
                            } else {
                                $img = '../IMG/Curso1.png';
                            }
                        ?>
                        <img src="<?= $img ?>" alt="Portada del proyecto">
                    </div>

                    <div class="proyecto-body">
                        <h2><?= htmlspecialchars($proyectoDetalle['Titulo_Proyecto']) ?></h2>
                        <p class="proyecto-meta">
                            Curso: <?= htmlspecialchars($proyectoDetalle['Titulo_Curso']) ?><br>
                            Docente: <?= htmlspecialchars($proyectoDetalle['Nombre_Docente'] . ' ' . $proyectoDetalle['Apellido_Docente']) ?><br>
                            Publicado: <?= date('d/m/Y', strtotime($proyectoDetalle['Fecha_Publicacion'])) ?>
                        </p>
                        <div class="proyecto-descripcion">
                            <?= nl2br(htmlspecialchars($proyectoDetalle['Descripcion'] ?? 'Sin descripción.')) ?>
                        </div>

                        <?php if (!empty($proyectoDetalle['Archivo_URL'])): ?>
                            <a href="../PHP/Subidas/proyectos/<?= rawurlencode(basename($proyectoDetalle['Archivo_URL'])) ?>" class="btn-confirmar" target="_blank" rel="noopener noreferrer" style="display:inline-block; margin-top:15px;" >
                                📂 Ver proyecto
                            </a>
                        <?php endif; ?>

                        <a href="cursos.php#modal-<?= (int)$proyectoDetalle['ID_Curso'] ?>" class="btn-confirmar" style="display:inline-block; margin-top:15px;" >
                            🎓 Ir al curso del proyecto
                        </a>
                    </div>
                </article>
            </section>

        <?php else: ?>
            <!-- Listado de proyectos -->
            <div class="grid-proyectos">
                <?php if (!empty($proyectos)): ?>
                    <?php foreach ($proyectos as $proy): ?>
                        <?php
                            if (!empty($proy['Imagen_Portada'])) {
                                $imgCard = 'Subidas/proyectos/portadas/' .
                                    rawurlencode(basename($proy['Imagen_Portada']));
                            } elseif (!empty($proy['Imagen_Curso'])) {
                                $imgCard = '../PHP conexiones/Imagenes/' .
                                    rawurlencode(basename($proy['Imagen_Curso']));
                            } else {
                                $imgCard = '../IMG/Curso1.png';
                            }
                        ?>
                        <a href="proyectos.php?ID_Proyecto=<?= $proy['ID_Proyecto'] ?>" class="card-proyecto">
                            <div class="proyecto-portada">
                                <img src="<?= $imgCard ?>" alt="Portada">
                            </div>
                            <div class="proyecto-body">
                                <!-- <span class="badge-tipo">Proyecto educativo</span> -->
                                <h3><?= htmlspecialchars($proy['Titulo_Proyecto']) ?></h3>

                                <p class="proyecto-curso">
                                    📚 Curso: <?= htmlspecialchars($proy['Titulo_Curso']) ?>
                                </p>

                                <small>
                                    👨‍🏫 <?= htmlspecialchars($proy['Nombre_Docente'] . ' ' . $proy['Apellido_Docente']) ?>
                                </small>

                                <p style="margin-top:12px;">
                                    <span class="btn-confirmar">Ver proyecto →</span>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center; grid-column: 1 / -1;">
                        Todavía no hay proyectos educativos publicados.
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>