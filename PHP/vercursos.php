<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Condicional con isset que evalua existe el idCurso o si esta vacio,en caso de ello, el usuario ira a miscursos.php 
if (!isset($_GET['idCurso']) || empty($_GET['idCurso'])) {
    header("Location: miscursos.php");
    exit();
}

$idCurso = intval($_GET['idCurso']);

// Pestaña activa enviada por la URL (por defecto muestra 'materiales' si no encuentra un valor en 'tab')
$tabActiva = $_GET['tab'] ?? 'materiales';

// Consulta que permite traer información del curso por medio de su id.
$stmtCurso = $pdo->prepare("SELECT * FROM cursos WHERE idCurso = :idCurso");
$stmtCurso->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
$stmtCurso->execute();
$curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

//Condicional que evalua si existe el curso.

if (!$curso) {
    echo "El curso no existe.";
    exit();
}

// Estructura para verificar el rol del usuario.

$rolSesion = strtolower(trim($_SESSION['Rol'] ?? ''));
$esDocente = ($rolSesion === 'docente' || $rolSesion === 'administrador');


// En esa consulta se obtienen las carpetas que hay en el curso.
$stmtCarpetas = $pdo->prepare("SELECT * FROM carpeta WHERE idCurso = :idCurso ORDER BY Orden ASC");
$stmtCarpetas->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
$stmtCarpetas->execute();
$carpetas = $stmtCarpetas->fetchAll(PDO::FETCH_ASSOC);

// Esta consulta se encarga de obtener los materiales que no esten dentro de ninguna carpeta.
$stmtMatSueltos = $pdo->prepare("SELECT * FROM material WHERE idCurso = :idCurso AND idCarpeta IS NULL");
$stmtMatSueltos->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
$stmtMatSueltos->execute();
$materialesSueltos = $stmtMatSueltos->fetchAll(PDO::FETCH_ASSOC);

// Esta consulta se encarga de obtener los miembros que esten inscriptos en los cursos.
$stmtMiembros = $pdo->prepare("SELECT u.Nombre, u.Apellido, u.Correo 
                               FROM usuario u 
                               INNER JOIN inscripcion i ON u.idUsu = i.idUsu 
                               WHERE i.idCurso = :idCurso");
$stmtMiembros->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
$stmtMiembros->execute();
$miembros = $stmtMiembros->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($curso['Titulo_curso']) ?> | LMS</title>
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
                        <a href="vercursos.php?idCurso=<?= $idCurso ?>&tab=materiales" class="<?= $tabActiva === 'materiales' ? 'active' : '' ?>">
                            📦 Materiales
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?idCurso=<?= $idCurso ?>&tab=calificaciones" class="<?= $tabActiva === 'calificaciones' ? 'active' : '' ?>">
                            📊 Calificaciones
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?idCurso=<?= $idCurso ?>&tab=miembros" class="<?= $tabActiva === 'miembros' ? 'active' : '' ?>">
                            👥 Miembros (<?= count($miembros) ?>)
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="info-curso">
                <small><strong>Nivel:</strong> <?= htmlspecialchars($curso['Nivel_Curso'] ?? $curso['Nivel_curso'] ?? '') ?></small><br>
                <small><strong>Tipo:</strong> <?= htmlspecialchars($curso['Tipo_curso'] ?? '') ?></small>
            </div>
        </aside>

        <!-- Columna central -->
        <main class="content-center">
            <div class="curso-header">
                <h2><?= htmlspecialchars($curso['Titulo_curso']) ?></h2>
                <p class="descripcion"><?= htmlspecialchars($curso['Descripcion_curso']) ?></p>
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
                                <?php if ($mat['Tipo_material'] == 'enlace'): ?>
                                    <span class="icon">🔗</span>
                                    <a href="<?= htmlspecialchars($mat['Contenido_url']) ?>" target="_blank">
                                        <?= htmlspecialchars($mat['Titulo_material']) ?> (abrir en otra pestaña)
                                    </a>

                                <?php elseif ($mat['Tipo_material'] == 'video'): ?>
                                    <span class="icon">🎬</span>
                                    <div class="media-container">
                                        <strong><?= htmlspecialchars($mat['Titulo_material']) ?></strong><br>
                                        <video controls width="100%">
                                            <source src="Subidas/videos/<?= htmlspecialchars($mat['Contenido_url']) ?>" type="video/mp4">
                                        </video>
                                    </div>

                                <?php elseif ($mat['Tipo_material'] == 'audio'): ?>
                                    <span class="icon">🎧</span>
                                    <div class="media-container">
                                        <strong><?= htmlspecialchars($mat['Titulo_material']) ?></strong><br>
                                        <audio controls style="width: 100%;">
                                            <source src="Subidas/audios/<?= htmlspecialchars($mat['Contenido_url']) ?>" type="audio/mpeg">
                                        </audio>
                                    </div>

                                <?php else: ?>
                                    <span class="icon">📄</span>
                                    <a href="Subidas/archivos/<?= htmlspecialchars($mat['Contenido_url']) ?>" download>
                                        <?= htmlspecialchars($mat['Titulo_material']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Carpetas -->
                        <?php foreach ($carpetas as $carpeta): ?>
                            <details class="item-recurso carpeta">
                                <summary>
                                    <span class="icon">📁</span>
                                    <strong><?= htmlspecialchars($carpeta['Nombre_carpeta']) ?></strong>
                                </summary>
                                
                                <div class="contenido-carpeta">
                                    <?php
                                    $stmtMatFolder = $pdo->prepare("SELECT * FROM material WHERE idCarpeta = :idCarpeta");
                                    $stmtMatFolder->bindParam(':idCarpeta', $carpeta['idCarpeta'], PDO::PARAM_INT);
                                    $stmtMatFolder->execute();
                                    $materialesCarpeta = $stmtMatFolder->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($materialesCarpeta) > 0):
                                        foreach ($materialesCarpeta as $matFolder):
                                    ?>
                                        <div class="item-recurso sub-item">
                                            <span class="icon">📄</span>
                                            <span><?= htmlspecialchars($matFolder['Titulo_material']) ?></span>
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
                    <input type="hidden" name="idCurso" value="<?= $idCurso ?>">

                    <label>Título del archivo:</label>
                    <input type="text" name="Titulo_material" required placeholder="Ej: Unidad 1 - Presentación.pdf">

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