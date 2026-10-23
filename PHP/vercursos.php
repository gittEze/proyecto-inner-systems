<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Condicional con isset que evalua existe el idCurso o si esta vacio,en caso de ello, el usuario ira a miscursos.php 
if (!isset($_GET['ID_Curso']) || empty($_GET['ID_Curso'])) {
    header("Location: miscursos.php");
    exit();
}
// Variable que guarda el valor de un curso 
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

//Estructura para verificar el rol del usuario.

$rolSesion = strtolower(trim($_SESSION['Rol'] ?? ''));
$esDocente = ($rolSesion === 'docente' || $rolSesion === 'administrador');


// En esta consulta se obtienen las carpetas que hay en el curso.
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

// VALORACIONES
$idUsuarioSesion = isset($_SESSION['ID_Usuario']) ? intval($_SESSION['ID_Usuario']) : 0;

// Promedio y total de valoraciones del curso
$stmtStats = $pdo->prepare("SELECT 
    ROUND(AVG(Puntuacion), 1) AS promedio, 
    COUNT(*) AS total 
    FROM valoraciones 
    WHERE ID_Curso = :ID_Curso");
$stmtStats->execute([':ID_Curso' => $idCurso]);
$statsVal = $stmtStats->fetch(PDO::FETCH_ASSOC);
$promedio = $statsVal['promedio'] ?? 0;
$totalValoraciones = (int)($statsVal['total'] ?? 0);

// Listado de valoraciones con nombre del autor (más recientes primero)
$stmtVals = $pdo->prepare("SELECT v.Puntuacion, v.Comentario, v.Fecha,
                                   u.Nombre, u.Apellido, u.Correo, u.ID_Usuario
                            FROM valoraciones v
                            INNER JOIN usuarios u ON u.ID_Usuario = v.ID_Usuario
                            WHERE v.ID_Curso = :ID_Curso
                            ORDER BY v.Fecha DESC");
$stmtVals->execute([':ID_Curso' => $idCurso]);
$listaValoraciones = $stmtVals->fetchAll(PDO::FETCH_ASSOC);

// Valoración propia del usuario logueado (si existe)
$miValoracion = null;
if ($idUsuarioSesion > 0) {
    $stmtMia = $pdo->prepare("SELECT Puntuacion, Comentario FROM valoraciones 
                              WHERE ID_Curso = :ID_Curso AND ID_Usuario = :ID_Usuario");
    $stmtMia->execute([':ID_Curso' => $idCurso, ':ID_Usuario' => $idUsuarioSesion]);
    $miValoracion = $stmtMia->fetch(PDO::FETCH_ASSOC);
}

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
            <!-- Nav con los elementos -->
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
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=valoraciones" class="<?= $tabActiva === 'valoraciones' ? 'active' : '' ?>">
                            ⭐ Valoraciones
                            <?php if ($totalValoraciones > 0): ?>
                                (<?= $totalValoraciones ?>)
                            <?php endif; ?>
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
                            <a href="#modal-archivo" class="btn-confirmar"> 📄 Subir Archivo</a>
                             <a href="#modal-carpeta" class="btn-confirmar">📁 Crear Carpeta</a>
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
       
                                <!-- Estructura con una consulta que permite traer el id carpeta de los materials -->
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
                        
                        <!-- Estructura para contar los miembros y traerlos -->
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

            <!-- PESTAÑA 4: VALORACIONES Y COMENTARIOS -->
            <?php if ($tabActiva === 'valoraciones'): ?>
                <section class="seccion-tab">
                    <h3>Valoraciones del curso</h3>
                    <br>

                    <!-- Resumen de promedio -->
                    <div class="resumen-valoraciones">
                        <?php if ($totalValoraciones > 0): ?>
                            <div class="promedio-box">
                                <span class="promedio-numero"><?= number_format((float)$promedio, 1) ?></span>
                                <div class="estrellas-static">
                                    <?php
                                    $promedioRedondeado = round((float)$promedio);
                                    for ($i = 1; $i <= 5; $i++):
                                        $clase = ($i <= $promedioRedondeado) ? 'estrella-llena' : 'estrella-vacia';
                                    ?>
                                        <span class="<?= $clase ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <p class="total-valoraciones"><?= $totalValoraciones ?> valoración<?= $totalValoraciones !== 1 ? 'es' : '' ?></p>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Aún no hay valoraciones para este curso. ¡Sé el primero en opinar!</p>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <!-- Formulario para valorar (solo usuarios logueados) -->
                    <?php if (isset($_SESSION['Sesion']) && $idUsuarioSesion > 0): ?>
                        <div class="form-valoracion card-widget">
                            <h4><?= $miValoracion ? 'Actualizar mi valoración' : 'Deja tu valoración' ?></h4>

                            <?php if (isset($_GET['ok'])): ?>
                                <p class="mensaje-ok">✅ Valoración guardada correctamente.</p>
                            <?php endif; ?>

                            <form action="../PHP conexiones/guardarValoracion.php" method="POST" id="formValoracion">
                                <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">
                                <input type="hidden" name="Puntuacion" id="inputPuntuacion" value="<?= $miValoracion['Puntuacion'] ?? 0 ?>" required>

                                <label>Puntuación (1 a 5 estrellas):</label>
                                <div class="estrellas-selector" id="estrellasSelector" role="radiogroup" aria-label="Seleccionar puntuación">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button"
                                                class="estrella-btn <?= (isset($miValoracion['Puntuacion']) && $i <= $miValoracion['Puntuacion']) ? 'seleccionada' : '' ?>"
                                                data-valor="<?= $i ?>"
                                                aria-label="<?= $i ?> estrella<?= $i > 1 ? 's' : '' ?>">★</button>
                                    <?php endfor; ?>
                                </div>
                                <p class="texto-puntuacion" id="textoPuntuacion">
                                    <?= isset($miValoracion['Puntuacion']) ? $miValoracion['Puntuacion'] . ' estrella(s)' : 'Selecciona una puntuación' ?>
                                </p>

                                <label for="Comentario">Comentario (opcional):</label>
                                <textarea name="Comentario" id="Comentario" rows="4"
                                          placeholder="Escribe tu opinión sobre el curso..."
                                          maxlength="2000"><?= htmlspecialchars($miValoracion['Comentario'] ?? '') ?></textarea>

                                <div class="modal-acciones" style="margin-top: 15px;">
                                    <button type="submit" class="btn-confirmar" id="btnEnviarValoracion">
                                        <?= $miValoracion ? 'Actualizar valoración' : 'Publicar valoración' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="item-recurso">
                            <p>Para dejar una valoración debes <a href="login.php">iniciar sesión</a>.</p>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <!-- Listado de comentarios -->
                    <h4>Opiniones de los alumnos</h4>
                    <div class="lista-comentarios">
                        <?php if (count($listaValoraciones) > 0): ?>
                            <?php foreach ($listaValoraciones as $val): ?>
                                <?php
                                    $nombreAutor = trim(($val['Nombre'] ?? '') . ' ' . ($val['Apellido'] ?? ''));
                                    if ($nombreAutor === '') {
                                        $nombreAutor = explode('@', $val['Correo'])[0];
                                    }
                                    $fechaFormateada = date('d/m/Y H:i', strtotime($val['Fecha']));
                                ?>
                                <article class="comentario-item item-recurso">
                                    <div class="comentario-cabecera">
                                        <span class="icon">👤</span>
                                        <strong><?= htmlspecialchars($nombreAutor) ?></strong>
                                        <span class="estrellas-static-small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?= $i <= $val['Puntuacion'] ? 'estrella-llena' : 'estrella-vacia' ?>">★</span>
                                            <?php endfor; ?>
                                        </span>
                                        <time class="fecha-comentario" datetime="<?= htmlspecialchars($val['Fecha']) ?>">
                                            <?= $fechaFormateada ?>
                                        </time>
                                    </div>
                                    <?php if (!empty($val['Comentario'])): ?>
                                        <p class="texto-comentario"><?= nl2br(htmlspecialchars($val['Comentario'])) ?></p>
                                    <?php else: ?>
                                        <p class="texto-comentario text-muted"><em>Sin comentario escrito.</em></p>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Todavía no hay opiniones publicadas.</p>
                        <?php endif; ?>
                    </div>
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
    <!-- Estructura de codigo especeilizada en la creación de carpetas para los cursos -->
    <?php if ($esDocente): ?>
        <div id="modal-carpeta" class="modal-overlay">
            <div class="modal-box">
                <h2>Nueva Carpeta</h2>
                <form action="../PHP conexiones/crearcarpeta.php" method="POST">
                    <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">

                    <label>Nombre de la carpeta:</label>
                    <input type="text" name="Nombre_Carpeta" required placeholder="Ej: Unidad 1 - Introducción">

                    <div class="modal-acciones">
                        <button type="submit" class="btn-confirmar">Crear</button>
                        <a href="#" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>


    <!-- Modal de subida de archivos -->
    <?php if ($esDocente): ?>
        <div id="modal-archivo" class="modal-overlay">
            <div class="modal-box">
                <h2>Subir Archivo</h2>
                <form action="../PHP conexiones/subirmaterial.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">

                    <label>Título del archivo:</label>
                    <input type="text" name="Titulo_Material" required placeholder="Ej: Unidad 1 - Presentación.pdf">

                    <!-- Estructura para asignar un archivo dentro  de una carpeta.  -->
                    <label>Asignar a carpeta (opcional):</label>
                        <select name="ID_Carpeta">
                            <option value="">-- Sin carpeta (Archivo suelto) --</option>
                            <?php foreach ($carpetas as $c): ?>
                                <option value="<?= $c['ID_Carpeta'] ?>"><?= htmlspecialchars($c ['Nombre_Carpeta']) ?></option>
                            <?php endforeach; ?>
                        </select>

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

    <script>
        (function () {
            const selector = document.getElementById('estrellasSelector');
            const inputPunt = document.getElementById('inputPuntuacion');
            const textoPunt = document.getElementById('textoPuntuacion');
            const form = document.getElementById('formValoracion');

            if (!selector || !inputPunt) return;

            const botones = selector.querySelectorAll('.estrella-btn');

            function pintar(valor) {
                botones.forEach(function (btn) {
                    const v = parseInt(btn.getAttribute('data-valor'), 10);
                    if (v <= valor) {
                        btn.classList.add('seleccionada');
                    } else {
                        btn.classList.remove('seleccionada');
                    }
                });
                inputPunt.value = valor;
                if (textoPunt) {
                    textoPunt.textContent = valor > 0 ? valor + ' estrella' + (valor > 1 ? 's' : '') : 'Selecciona una puntuación';
                }
            }

            botones.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const valor = parseInt(this.getAttribute('data-valor'), 10);
                    pintar(valor);
                });

                btn.addEventListener('mouseenter', function () {
                    const valor = parseInt(this.getAttribute('data-valor'), 10);
                    botones.forEach(function (b) {
                        const v = parseInt(b.getAttribute('data-valor'), 10);
                        b.classList.toggle('hover-temp', v <= valor);
                    });
                });
            });

            selector.addEventListener('mouseleave', function () {
                botones.forEach(function (b) {
                    b.classList.remove('hover-temp');
                });
            });

            if (form) {
                form.addEventListener('submit', function (e) {
                    const p = parseInt(inputPunt.value, 10);
                    if (isNaN(p) || p < 1 || p > 5) {
                        e.preventDefault();
                        alert('Debes seleccionar una puntuación entre 1 y 5 estrellas.');
                        return false;
                    }
                });
            }
        })();
        </script>

</body>
</html>