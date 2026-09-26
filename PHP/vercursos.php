<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Condicional con isset que evalua existe el idCurso o si esta vacio,en caso de ello, el usuario ira a miscursos.php 
if (!isset($_GET['ID_Curso']) || empty($_GET['ID_Curso'])) {
    header("Location: miscursos.php");
    exit();
}

$idCurso = intval($_GET['ID_Curso']);

// Pestaña activa enviada por la URL (por defecto muestra 'materiales' si no encuentra un valor en 'tab')
$tabActiva = $_GET['tab'] ?? 'materiales';

// Consulta que permite traer información del curso por medio de su id.
$stmtCurso = $pdo->prepare("SELECT * FROM cursos WHERE ID_Curso = :ID_Curso");
$stmtCurso->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtCurso->execute();
$curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

//Condicional que evalua si existe el curso.

if (!$curso) {
    echo "El curso no existe.";
    exit();
}

// Estructura para verificar el rol del usuario.

$rolSesion = strtolower(trim($_SESSION['Rol'] ?? ''));
$esDocente = ($rolSesion === 'Docente' || $rolSesion === 'Administrador');


// En esa consulta se obtienen las carpetas que hay en el curso.
$stmtCarpetas = $pdo->prepare("SELECT * FROM carpetas WHERE ID_Curso = :ID_Curso ORDER BY Orden ASC");
$stmtCarpetas->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtCarpetas->execute();
$carpetas = $stmtCarpetas->fetchAll(PDO::FETCH_ASSOC);

// Esta consulta se encarga de obtener los materiales que no esten dentro de ninguna carpeta.
$stmtMatSueltos = $pdo->prepare("SELECT * FROM materiales WHERE ID_Curso = :ID_Curso AND ID_Carpeta IS NULL");
$stmtMatSueltos->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtMatSueltos->execute();
$materialesSueltos = $stmtMatSueltos->fetchAll(PDO::FETCH_ASSOC);

// Esta consulta se encarga de obtener los miembros que esten inscriptos en los cursos.
$stmtMiembros = $pdo->prepare("SELECT u.Nombre, u.Apellido, u.Correo 
                               FROM usuarios u 
                               INNER JOIN inscripciones i ON u.ID_Usuario = i.ID_Usuario 
                               WHERE i.ID_Curso = :ID_Curso");
$stmtMiembros->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtMiembros->execute();
$miembros = $stmtMiembros->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($curso['Titulo_Curso']) ?> | LMS</title>
    <link rel="stylesheet" href="../CSS/estilo.css">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="lms-container">

        <!-- Columna izquierda: Navegación -->
        <aside class="sidebar-left">
            <div class="curso-portada">
                <img src="../PHP conexiones/Imagenes/<?= !empty($curso['Dataso']) ? htmlspecialchars($curso['Dataso']) : 'default.png' ?>" alt="Portada Curso">
            </div>
            <nav class="course-nav">
                <ul>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=materiales" class="<?= $tabActiva === 'materiales' ? 'active' : '' ?>">
                            📦 Materiales
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=calificaciones" class="<?= $tabActiva === 'calificaciones' ? 'active' : '' ?>">
                            📊 Calificaciones
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=miembros" class="<?= $tabActiva === 'miembros' ? 'active' : '' ?>">
                            👥 Miembros (<?= count($miembros) ?>)
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="info-curso">
                <small><strong>Nivel:</strong> <?= htmlspecialchars($curso['Nivel_Curso'] ?? $curso['Nivel_Curso'] ?? '') ?></small><br>
                <small><strong>Tipo:</strong> <?= htmlspecialchars($curso['Tipo_Curso'] ?? '') ?></small>
            </div>
        </aside>

        <!-- Columna central -->
        <main class="content-center">
            <div class="curso-header">
                <h2><?= htmlspecialchars($curso['Titulo_Curso']) ?></h2>
                <p class="descripcion"><?= htmlspecialchars($curso['Descripcion_Curso']) ?></p>
            </div>

            <hr>

            <!-- PESTAÑA 1: MATERIALES -->
            <?php if ($tabActiva === 'materiales'): ?>
                <section class="seccion-tab">
                    <h3>Recursos del Curso</h3>
                    <br>

                    <!-- Acciones exclusivas para docentes/admins -->
                    <?php if ($esDocente): ?>
                        <div class="acciones-profesor">
                            <a href="#modal-archivo" class="btn-confirmar">📁 Subir Archivo</a>
                        </div>
                    <?php endif; ?>

                    <div class="lista-recursos">
                        
                        <!-- Lista de archivos sueltos -->
                        <?php foreach ($materialesSueltos as $mat): ?>
                            <div class="item-recurso material">
                                <?php if ($mat['Tipo_Material'] == 'enlace'): ?>
                                    <span class="icon">🔗</span>
                                    <a href="<?= htmlspecialchars($mat['Contenido_URL']) ?>" target="_blank">
                                        <?= htmlspecialchars($mat['Titulo_Material']) ?> (abrir en otra pestaña)
                                    </a>

                                <?php elseif ($mat['Tipo_Material'] == 'video'): ?>
                                    <span class="icon">🎬</span>
                                    <div class="media-container">
                                        <strong><?= htmlspecialchars($mat['Titulo_Material']) ?></strong><br>
                                        <video controls width="100%">
                                            <source src="Subidas/videos/<?= htmlspecialchars($mat['Contenido_URL']) ?>" type="video/mp4">
                                        </video>
                                    </div>

                                <?php elseif ($mat['Tipo_Material'] == 'audio'): ?>
                                    <span class="icon">🎧</span>
                                    <div class="media-container">
                                        <strong><?= htmlspecialchars($mat['Titulo_Material']) ?></strong><br>
                                        <audio controls style="width: 100%;">
                                            <source src="Subidas/audios/<?= htmlspecialchars($mat['Contenido_URL']) ?>" type="audio/mpeg">
                                        </audio>
                                    </div>

                                <?php else: ?>
                                    <span class="icon">📄</span>
                                    <a href="Subidas/archivos/<?= htmlspecialchars($mat['Contenido_URL']) ?>" download>
                                        <?= htmlspecialchars($mat['Titulo_Material']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Carpetas -->
                        <?php foreach ($carpetas as $carpeta): ?>
                            <details class="item-recurso carpeta">
                                <summary>
                                    <span class="icon">📁</span>
                                    <strong><?= htmlspecialchars($carpeta['Nombre_Carpeta']) ?></strong>
                                </summary>
                                
                                <div class="contenido-carpeta">
                                    <?php
                                    $stmtMatFolder = $pdo->prepare("SELECT * FROM materiales WHERE ID_Carpeta = :ID_Carpeta");
                                    $stmtMatFolder->bindParam(':ID_Carpeta', $carpeta['ID_Carpeta'], PDO::PARAM_INT);
                                    $stmtMatFolder->execute();
                                    $materialesCarpeta = $stmtMatFolder->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($materialesCarpeta) > 0):
                                        foreach ($materialesCarpeta as $matFolder):
                                    ?>
                                        <div class="item-recurso sub-item">
                                            <span class="icon">📄</span>
                                            <span><?= htmlspecialchars($matFolder['Titulo_Material']) ?></span>
                                        </div>
                                    <?php 
                                        endforeach;
                                    else:
                                    ?>
                                        <small class="text-muted">Carpeta vacía.</small>
                                    <?php endif; ?>
                                </div>
                            </details>
                        <?php endforeach; ?>

                    </div>
                </section>
            <?php endif; ?>

            <!-- PESTAÑA 2: CALIFICACIONES -->
            <?php if ($tabActiva === 'calificaciones'): ?>
                <section class="seccion-tab">
                    <h3>Calificaciones</h3>
                    <br>
                    <div class="item-recurso">
                        <span class="icon">📊</span>
                        <p>No hay libreta de calificaciones disponible por el momento.</p>
                    </div>
                </section>
            <?php endif; ?>

            <!-- PESTAÑA 3: MIEMBROS -->
            <?php if ($tabActiva === 'miembros'): ?>
                <section class="seccion-tab">
                    <h3>Integrantes del Curso</h3>

                    <details class="desplegable-miembros" open>
                        <summary>👥 Alumnos inscriptos (<?= count($miembros) ?>)</summary>
                        
                        <?php if (count($miembros) > 0): ?>
                            <ul class="lista-miembros">
                                <?php foreach ($miembros as $m): ?>
                                    <?php 
                                        $nombreCompleto = trim(($m['Nombre'] ?? '') . ' ' . ($m['Apellido'] ?? ''));
                                        if (empty($nombreCompleto)) {
                                            $nombreCompleto = explode('@', $m['Correo'])[0];
                                        }
                                    ?>
                                    <li class="item-miembro">
                                        <span class="icon">👤</span>
                                        <div>
                                            <strong><?= htmlspecialchars($nombreCompleto) ?></strong>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted" style="margin-top: 10px;">Aún no hay alumnos inscritos en este curso.</p>
                        <?php endif; ?>
                    </details>
                </section>
            <?php endif; ?>

        </main>

        <!-- Columna derecha -->
        <aside class="sidebar-right">
            <div class="card-widget">
                <h3>Información 📅</h3>
                <p class="text-muted">No hay eventos agendados para esta semana.</p>
            </div>
        </aside>

    </div>

    <!-- Modal de subida de archivos -->
    <?php if ($esDocente): ?>
        <div id="modal-archivo" class="modal-overlay">
            <div class="modal-box">
                <h2>Subir Archivo</h2>
                <form action="../PHP conexiones/subirmaterial.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">

                    <label>Título del archivo:</label>
                    <input type="text" name="Titulo_Material" required placeholder="Ej: Unidad 1 - Presentación.pdf">

                    <label>Seleccionar Archivo:</label>
                    <input type="file" name="archivo_adjunto" required>

                    <div class="modal-acciones">
                        <button type="submit" class="btn-confirmar">Subir</button>
                        <a href="#" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php include 'footer.php'; ?>

</body>
</html>