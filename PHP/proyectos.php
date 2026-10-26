<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Listado de proyectos educativos con datos del curso y del docente
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

// Detalle de un proyecto concreto (?ID_Proyecto=)
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
                <!-- Vista detalle de un proyecto -->
                <section class="proyecto-detalle">
                    <a href="proyectos.php" class="btn-cancelar" style="display: inline-block; margin-bottom: 15px;">← Volver al listado</a>

                    <article class="card-proyecto card-proyecto-detalle">
                        <div class="proyecto-portada">
                            <?php
                            if (!empty($proyectoDetalle['Imagen_Portada'])) {
                                $img = 'Subidas/proyectos/portadas/' . htmlspecialchars($proyectoDetalle['Imagen_Portada']);
                            } elseif (!empty($proyectoDetalle['Imagen_Curso'])) {
                                $img = '../PHP conexiones/Imagenes/' . htmlspecialchars($proyectoDetalle['Imagen_Curso']);
                            } else {
                                $img = '../IMG/Curso1.png';
                            }
                            ?>
                            <img src="<?= $img ?>" alt="Portada del proyecto">
                        </div>

                        <div class="proyecto-body">
                            <h2><?= htmlspecialchars($proyectoDetalle['Titulo_Proyecto']) ?></h2>

                            <p class="proyecto-meta">
                                <span>📚 Curso: <strong><?= htmlspecialchars($proyectoDetalle['Titulo_Curso']) ?></strong></span>
                                <span>👤 Docente: <?= htmlspecialchars(trim($proyectoDetalle['Nombre_Docente'] . ' ' . $proyectoDetalle['Apellido_Docente'])) ?></span>
                                <span>📅 <?= date('d/m/Y', strtotime($proyectoDetalle['Fecha_Publicacion'])) ?></span>
                            </p>

                            <?php if (!empty($proyectoDetalle['Descripcion'])): ?>
                                <div class="proyecto-descripcion">
                                    <?= nl2br(htmlspecialchars($proyectoDetalle['Descripcion'])) ?>
                                </div>
                            <?php endif; ?>

                            <div class="proyecto-acciones">
                                <?php if (!empty($proyectoDetalle['Archivo_URL'])): ?>
                                    <a class="btn-confirmar"
                                       href="Subidas/proyectos/<?= htmlspecialchars($proyectoDetalle['Archivo_URL']) ?>"
                                       target="_blank" download>
                                        📥 Descargar / ver proyecto
                                    </a>
                                <?php endif; ?>

                                <!-- Abre el modal de inscripción del curso en cursos.php -->
                                <a class="btn-confirmar btn-ir-curso"
                                   href="cursos.php#modal-<?= intval($proyectoDetalle['ID_Curso']) ?>">
                                    🎓 Ir al curso del proyecto e inscribirme
                                </a>
                            </div>
                        </div>
                    </article>
                </section>

            <?php else: ?>
                <!-- Listado de proyectos -->
                <div class="grid-proyectos">
                    <?php if (empty($proyectos)): ?>
                        <p class="text-muted" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px;">
                            Todavía no hay proyectos educativos publicados.
                            Los docentes pueden subirlos desde el curso que crearon, con el botón
                            <strong>«Subir proyecto educativo»</strong>.
                        </p>
                    <?php else: ?>
                        <?php foreach ($proyectos as $proy): ?>
                            <?php
                            if (!empty($proy['Imagen_Portada'])) {
                                $imgCard = 'Subidas/proyectos/portadas/' . htmlspecialchars($proy['Imagen_Portada']);
                            } elseif (!empty($proy['Imagen_Curso'])) {
                                $imgCard = '../PHP conexiones/Imagenes/' . htmlspecialchars($proy['Imagen_Curso']);
                            } else {
                                $imgCard = '../IMG/Curso1.png';
                            }
                            ?>
                            <article class="card-proyecto">
                                <a class="card-proyecto-link" href="proyectos.php?ID_Proyecto=<?= intval($proy['ID_Proyecto']) ?>">
                                    <div class="proyecto-portada">
                                        <img src="<?= $imgCard ?>" alt="<?= htmlspecialchars($proy['Titulo_Proyecto']) ?>">
                                    </div>
                                    <div class="proyecto-body">
                                        <h3><?= htmlspecialchars($proy['Titulo_Proyecto']) ?></h3>
                                        <p class="proyecto-curso-ref">
                                            Curso: <?= htmlspecialchars($proy['Titulo_Curso']) ?>
                                        </p>
                                        <?php if (!empty($proy['Descripcion'])): ?>
                                            <p class="proyecto-resumen">
                                                <?= htmlspecialchars(mb_strimwidth($proy['Descripcion'], 0, 120, '...')) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="proyecto-fecha">
                                            <?= date('d/m/Y', strtotime($proy['Fecha_Publicacion'])) ?>
                                            · <?= htmlspecialchars(trim($proy['Nombre_Docente'] . ' ' . $proy['Apellido_Docente'])) ?>
                                        </p>
                                    </div>
                                </a>

                                <div class="proyecto-acciones-card">
                                    <a class="btn-confirmar btn-sm"
                                       href="proyectos.php?ID_Proyecto=<?= intval($proy['ID_Proyecto']) ?>">
                                        Ver proyecto
                                    </a>
                                    <a class="btn-confirmar btn-sm btn-ir-curso"
                                       href="cursos.php#modal-<?= intval($proy['ID_Curso']) ?>">
                                        Ir al curso del proyecto
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>

        <?php include 'footer.php'; ?>
    </body>
</html>