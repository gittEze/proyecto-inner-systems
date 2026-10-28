<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Validar que venga el ID del curso
if (!isset($_GET['ID_Curso']) || empty($_GET['ID_Curso'])) {
    header("Location: miscursos.php");
    exit();
}

$idCurso = intval($_GET['ID_Curso']);
$tabActiva = $_GET['tab'] ?? 'materiales';

// Datos del curso
$stmtCurso = $pdo->prepare("SELECT * FROM cursos WHERE ID_Curso = :ID_Curso");
$stmtCurso->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtCurso->execute();
$curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "El curso no existe.";
    exit();
}

// Datos del docente creador del curso
$stmtDocente = $pdo->prepare("
    SELECT Nombre, Apellido, Telefono, Correo
    FROM usuarios
    WHERE ID_Usuario = :ID_Docente
");
$stmtDocente->execute([
    ':ID_Docente' => $curso['ID_Docente']
]);
$docenteCurso = $stmtDocente->fetch(PDO::FETCH_ASSOC);

// Roles
$rolSesion = strtolower(trim($_SESSION['Rol'] ?? ''));
$esDocente = ($rolSesion === 'docente' || $rolSesion === 'administrador');
$idUsuarioSesion = isset($_SESSION['ID_Usuario']) ? intval($_SESSION['ID_Usuario']) : 0;

$esCreadorCurso = $esDocente && (
    $rolSesion === 'administrador' ||
    ($idUsuarioSesion > 0 && $idUsuarioSesion === intval($curso['ID_Docente']))
);

// Carpetas del curso
$stmtCarpetas = $pdo->prepare("SELECT * FROM carpetas WHERE ID_Curso = :ID_Curso ORDER BY Orden ASC");
$stmtCarpetas->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtCarpetas->execute();
$carpetas = $stmtCarpetas->fetchAll(PDO::FETCH_ASSOC);

// Materiales sueltos (sin carpeta)
$stmtMatSueltos = $pdo->prepare("SELECT * FROM materiales WHERE ID_Curso = :ID_Curso AND ID_Carpeta IS NULL");
$stmtMatSueltos->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtMatSueltos->execute();
$materialesSueltos = $stmtMatSueltos->fetchAll(PDO::FETCH_ASSOC);

// Miembros inscritos
$stmtMiembros = $pdo->prepare("SELECT u.Nombre, u.Apellido, u.Correo 
                               FROM usuarios u 
                               INNER JOIN inscripciones i ON u.ID_Usuario = i.ID_Usuario 
                               WHERE i.ID_Curso = :ID_Curso");
$stmtMiembros->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtMiembros->execute();
$miembros = $stmtMiembros->fetchAll(PDO::FETCH_ASSOC);

// Valoraciones - estadísticas
$stmtStats = $pdo->prepare("SELECT ROUND(AVG(Puntuacion), 1) AS promedio, COUNT(*) AS total 
                            FROM valoraciones WHERE ID_Curso = :ID_Curso");
$stmtStats->execute([':ID_Curso' => $idCurso]);
$statsVal = $stmtStats->fetch(PDO::FETCH_ASSOC);
$promedio = $statsVal['promedio'] ?? 0;
$totalValoraciones = (int)($statsVal['total'] ?? 0);

// Listado de valoraciones
$stmtVals = $pdo->prepare("SELECT v.Puntuacion, v.Comentario, v.Fecha,
                                   u.Nombre, u.Apellido, u.Correo, u.ID_Usuario
                            FROM valoraciones v
                            INNER JOIN usuarios u ON u.ID_Usuario = v.ID_Usuario
                            WHERE v.ID_Curso = :ID_Curso
                            ORDER BY v.Fecha DESC");
$stmtVals->execute([':ID_Curso' => $idCurso]);
$listaValoraciones = $stmtVals->fetchAll(PDO::FETCH_ASSOC);

// Valoración del usuario actual
$miValoracion = null;
if ($idUsuarioSesion > 0) {
    $stmtMia = $pdo->prepare("SELECT Puntuacion, Comentario FROM valoraciones 
                              WHERE ID_Curso = :ID_Curso AND ID_Usuario = :ID_Usuario");
    $stmtMia->execute([':ID_Curso' => $idCurso, ':ID_Usuario' => $idUsuarioSesion]);
    $miValoracion = $stmtMia->fetch(PDO::FETCH_ASSOC);
}

// Tareas del curso
$stmtTareas = $pdo->prepare("SELECT ID_Tarea, Nombre_Tarea, Fecha_Vencimiento
                             FROM tareas
                             WHERE ID_Curso = :ID_Curso
                             ORDER BY Fecha_Vencimiento ASC");
$stmtTareas->bindParam(':ID_Curso', $idCurso, PDO::PARAM_INT);
$stmtTareas->execute();
$tareas = $stmtTareas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($curso['Titulo_Curso']) ?> | Aprendomo</title>
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="stylesheet" href="../CSS/animaciones.css"> 
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="lms-container">

        <!-- Columna izquierda: Navegación -->
        <aside class="sidebar-left">
            <div class="curso-portada">
                <?php if (!empty($curso['Dataso'])): ?>
                    <img src="../PHP conexiones/Imagenes/<?= htmlspecialchars($curso['Dataso']) ?>" alt="Portada Curso">
                <?php else: ?>
                    <img src="../IMG/Curso1.png" alt="Portada Curso">
                <?php endif; ?>
            </div>

            <nav class="course-nav">
                <ul>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=materiales" 
                           class="<?= $tabActiva === 'materiales' ? 'active' : '' ?>">
                            📦 Materiales
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=calificaciones" 
                           class="<?= $tabActiva === 'calificaciones' ? 'active' : '' ?>">
                            📊 Calificaciones
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=miembros" 
                           class="<?= $tabActiva === 'miembros' ? 'active' : '' ?>">
                            👥 Miembros (<?= count($miembros) ?>)
                        </a>
                    </li>
                    <li>
                        <a href="vercursos.php?ID_Curso=<?= $idCurso ?>&tab=valoraciones" 
                           class="<?= $tabActiva === 'valoraciones' ? 'active' : '' ?>">
                            ⭐ Valoraciones
                            <?php if ($totalValoraciones > 0): ?>
                                (<?= $totalValoraciones ?>)
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="info-curso-sidebar">
                <small><strong>Nivel:</strong> <?= htmlspecialchars($curso['Nivel_Curso'] ?? '') ?></small><br>
                <small><strong>Tipo:</strong> <?= htmlspecialchars($curso['Tipo_Curso'] ?? '') ?></small>
            </div>
        </aside>

        <!-- Columna central -->
        <main class="content-center">
            <div class="curso-header">
                <h2><?= htmlspecialchars($curso['Titulo_Curso']) ?></h2>

                <p class="descripcion">
                    <?= htmlspecialchars($curso['Descripcion_Curso']) ?>
                </p>

                <?php if ($docenteCurso): ?>
                    <div class="contacto-docente">
                        <h3>Contacto del docente</h3>

                        <p>
                            <strong>Docente creador:</strong>
                            <?= htmlspecialchars(
                                ($docenteCurso['Nombre'] ?? '') . ' ' .
                                ($docenteCurso['Apellido'] ?? '')
                            ) ?>
                        </p>

                        <p>
                            <strong>Correo electrónico:</strong>
                            <?php if (!empty($docenteCurso['Correo'])): ?>
                                <a href="mailto:<?= htmlspecialchars($docenteCurso['Correo']) ?>">
                                    <?= htmlspecialchars($docenteCurso['Correo']) ?>
                                </a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </p>

                        <p class="contacto-docente-ayuda">
                            Si tienes dudas o dificultades con el curso,
                            puedes comunicarte con el docente.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <hr>
            
            <!-- ==================== PESTAÑA MATERIALES ==================== -->
            <?php if ($tabActiva === 'materiales'): ?>
                <section class="seccion-tab">
                    <h3>Recursos del Curso</h3>
                    <br>

                    <?php if ($esDocente): ?>
                        <div class="acciones-profesor">
                            <?php if ($esCreadorCurso): ?>
                                <a href="#modal-archivo" class="btn-confirmar">📄 Subir Archivo</a>
                                <a href="#modal-carpeta" class="btn-confirmar">📁 Crear Carpeta</a>
                                <button type="button" id="btnAbrirModal" class="btn-confirmar">📝 Crear Tarea</button>
                                <a href="#modal-proyecto" class="btn-confirmar">🎓 Subir proyecto educativo</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['proyecto']) && $_GET['proyecto'] === 'ok'): ?>
                        <div class="logrado" style="margin-bottom: 15px;">
                            Proyecto educativo publicado correctamente. Ya se puede ver en 
                            <a href="proyectos.php">Proyectos educativos</a>.
                        </div>
                    <?php endif; ?>

                    <div class="lista-recursos">
                        <!-- Materiales sueltos -->
                        <?php foreach ($materialesSueltos as $mat): ?>
                            <div class="item-recurso material">
                                <?php if ($mat['Tipo_Material'] == 'enlace'): ?>
                                    <span class="icon">🔗</span>
                                    <a href="<?= htmlspecialchars($mat['Contenido_URL']) ?>" target="_blank">
                                        <?= htmlspecialchars($mat['Titulo_Material']) ?>
                                    </a>
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
                                    $stmtMatCarpeta = $pdo->prepare("SELECT * FROM materiales WHERE ID_Carpeta = :ID_Carpeta");
                                    $stmtMatCarpeta->execute([':ID_Carpeta' => $carpeta['ID_Carpeta']]);
                                    $matsCarpeta = $stmtMatCarpeta->fetchAll(PDO::FETCH_ASSOC);

                                    if (empty($matsCarpeta)): ?>
                                        <p style="padding:10px; color:#888;">Carpeta vacía</p>
                                    <?php else:
                                        foreach ($matsCarpeta as $mat): ?>
                                            <div class="item-recurso material" style="margin-left:20px;">
                                            <?php if ($mat['Tipo_Material'] === 'enlace'): ?>
                                                <span class="icon">🔗</span>
                                                <a href="<?= htmlspecialchars($mat['Contenido_URL'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" >
                                                    <?= htmlspecialchars($mat['Titulo_Material'], ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="icon">📄</span>
                                                <a href="Subidas/archivos/<?= rawurlencode($mat['Contenido_URL']) ?>" download >
                                                    <?= htmlspecialchars($mat['Titulo_Material'], ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach;
                                        
                                    endif; ?>

                                    <?php
                                    // Tareas de esta carpeta
                                    $stmtTareasCarpeta = $pdo->prepare(
                                        "SELECT ID_Tarea, Nombre_Tarea, Fecha_Vencimiento 
                                         FROM tareas 
                                         WHERE ID_Carpeta = :ID_Carpeta 
                                         ORDER BY Fecha_Vencimiento ASC"
                                    );
                                    $stmtTareasCarpeta->execute([':ID_Carpeta' => $carpeta['ID_Carpeta']]);
                                    $tareasCarpeta = $stmtTareasCarpeta->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($tareasCarpeta as $tarea): ?>
                                        <div class="item-recurso material" style="margin-left:20px;">
                                            <span class="icon">📝</span>
                                            <a href="tarea.php?ID_Tarea=<?= (int)$tarea['ID_Tarea'] ?>">
                                                <?= htmlspecialchars($tarea['Nombre_Tarea'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                            <small> — Vence: <?= date('d/m/Y H:i', strtotime($tarea['Fecha_Vencimiento'])) ?></small>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </details>
                        <?php endforeach; ?>

                        <?php
                        // Tareas sin carpeta
                        $stmtTareasSueltas = $pdo->prepare(
                            "SELECT ID_Tarea, Nombre_Tarea, Fecha_Vencimiento 
                             FROM tareas 
                             WHERE ID_Curso = :ID_Curso AND ID_Carpeta IS NULL 
                             ORDER BY Fecha_Vencimiento ASC"
                        );
                        $stmtTareasSueltas->execute([':ID_Curso' => $idCurso]);
                        $tareasSueltas = $stmtTareasSueltas->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($tareasSueltas as $tarea): ?>
                            <div class="item-recurso">
                                <span class="icon">📝</span>
                                <strong><?= htmlspecialchars($tarea['Nombre_Tarea']) ?></strong>
                                <small> — Vence: <?= date('d/m/Y H:i', strtotime($tarea['Fecha_Vencimiento'])) ?></small>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($materialesSueltos) && empty($carpetas)): ?>
                            <p>Todavía no hay materiales en este curso.</p>
                        <?php endif; ?>
                    </div>
                </section>

            <!-- ==================== PESTAÑA CALIFICACIONES ==================== -->
            <?php elseif ($tabActiva === 'calificaciones'): ?>
                <section class="seccion-tab">
                    <h3>Calificaciones / Tareas</h3>
                    <br>

                    <?php if (isset($_GET['tarea']) && $_GET['tarea'] === 'ok'): ?>
                        <div class="logrado" style="margin-bottom: 15px;">
                            Tarea publicada correctamente.
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($tareas)): ?>
                        <div class="lista-tareas">
                            <?php foreach ($tareas as $tarea): ?>
                                <div class="item-recurso">
                                    <span class="icon">📝</span>
                                    <strong><?= htmlspecialchars($tarea['Nombre_Tarea']) ?></strong>
                                    <small> — Vence: <?= date('d/m/Y H:i', strtotime($tarea['Fecha_Vencimiento'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No hay tareas publicadas todavía.</p>
                    <?php endif; ?>
                </section>

            <!-- ==================== PESTAÑA MIEMBROS ==================== -->
            <?php elseif ($tabActiva === 'miembros'): ?>
                <section class="seccion-tab">
                    <h3>Miembros del curso (<?= count($miembros) ?>)</h3>
                    <br>
                    <?php if (!empty($miembros)): ?>
                        <div class="lista-miembros">
                            <?php foreach ($miembros as $m): ?>
                                <div class="item-recurso">
                                    <span class="icon">👤</span>
                                    <?= htmlspecialchars($m['Nombre'] . ' ' . $m['Apellido']) ?>
                                    <small>(<?= htmlspecialchars($m['Correo']) ?>)</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Todavía no hay alumnos inscritos.</p>
                    <?php endif; ?>
                </section>

            <!-- ==================== PESTAÑA VALORACIONES ==================== -->
            <?php elseif ($tabActiva === 'valoraciones'): ?>
                <section class="seccion-tab">
                    <h3>Valoraciones del curso</h3>
                    <br>

                    <div class="resumen-valoracion">
                        <?php if ($totalValoraciones > 0): ?>
                            <p>
                                <strong>Promedio:</strong> 
                                <?= number_format($promedio, 1) ?> / 5 
                                (<?= $totalValoraciones ?> valoración<?= $totalValoraciones !== 1 ? 'es' : '' ?>)
                            </p>
                        <?php else: ?>
                            <p>Todavía no hay valoraciones.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Formulario de valoración -->
                    <?php if ($idUsuarioSesion > 0): ?>
                        <div class="form-valoracion" style="margin: 25px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                            <h4><?= $miValoracion ? 'Editar tu valoración' : 'Deja tu valoración' ?></h4>
                            <form action="../PHP conexiones/guardarValoracion.php" method="POST" id="formValoracion">
                                <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">
                                <input type="hidden" name="Puntuacion" id="inputPuntuacion" 
                                       value="<?= $miValoracion['Puntuacion'] ?? 0 ?>" required>

                                <label>Puntuación (1 a 5 estrellas):</label>
                                <div class="estrellas-selector" id="estrellasSelector">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button"
                                                class="estrella-btn <?= (isset($miValoracion['Puntuacion']) && $i <= $miValoracion['Puntuacion']) ? 'seleccionada' : '' ?>"
                                                data-valor="<?= $i ?>">★</button>
                                    <?php endfor; ?>
                                </div>
                                <p class="texto-puntuacion" id="textoPuntuacion">
                                    <?= isset($miValoracion['Puntuacion']) ? $miValoracion['Puntuacion'] . ' estrella(s)' : 'Selecciona una puntuación' ?>
                                </p>

                                <label for="Comentario">Comentario (opcional):</label>
                                <textarea name="Comentario" id="Comentario" rows="4"
                                          placeholder="Escribe tu opinión sobre el curso..."
                                          maxlength="2000"><?= htmlspecialchars($miValoracion['Comentario'] ?? '') ?></textarea>

                                <div style="margin-top: 15px;">
                                    <button type="submit" class="btn-confirmar">
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

                    <!-- Lista de valoraciones -->
                    <div class="lista-valoraciones" style="margin-top: 30px;">
                        <?php foreach ($listaValoraciones as $val): ?>
                            <div class="item-recurso" style="margin-bottom: 15px;">
                                <strong><?= htmlspecialchars($val['Nombre'] . ' ' . $val['Apellido']) ?></strong>
                                <span style="color: #ffb400;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?= $i <= $val['Puntuacion'] ? '★' : '☆' ?>
                                    <?php endfor; ?>
                                </span>
                                <small><?= date('d/m/Y', strtotime($val['Fecha'])) ?></small>
                                <?php if (!empty($val['Comentario'])): ?>
                                    <p><?= nl2br(htmlspecialchars($val['Comentario'])) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </main>
    </div>

    <!-- ==================== MODALES (solo si es creador) ==================== -->
    <?php if ($esCreadorCurso): ?>
        <!-- Modal Subir Archivo -->
        <div id="modal-archivo" class="modal-overlay">
            <div class="modal-box">
                <h2>Subir archivo / material</h2>
                <form action="../PHP conexiones/subirmaterial.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">
                    
                    <label>Título del material:</label>
                    <input type="text" name="Titulo_Material" required>

                    <label>Tipo de material:</label>
                    <select name="Tipo_Material" id="tipoMaterial" required>
                        <option value="archivo">Archivo</option>
                        <option value="enlace">Enlace</option>
                    </select>

                    <div id="grupo-archivo">
                        <label for="archivoAdjunto">Seleccionar archivo:</label>
                        <input type="file" id="archivoAdjunto" name="archivo_adjunto" required>
                        <small>
                            Puedes subir documentos, PDF, imágenes, videos, audios,
                            archivos comprimidos y otros formatos.
                        </small>
                    </div>

                    <div id="grupo-enlace" style="display: none;">
                        <label for="contenidoURL">URL / Enlace:</label>
                        <input type="url" id="contenidoURL" name="Contenido_URL" placeholder="https://ejemplo.com/recurso" disabled>
                    </div>

                    <label>Carpeta (opcional):</label>
                    <select name="ID_Carpeta">
                        <option value="">Sin carpeta</option>
                        <?php foreach ($carpetas as $c): ?>
                            <option value="<?= $c['ID_Carpeta'] ?>"><?= htmlspecialchars($c['Nombre_Carpeta']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="modal-acciones">
                        <button type="submit" class="btn-confirmar">Subir</button>
                        <a href="#" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Crear Carpeta -->
        <div id="modal-carpeta" class="modal-overlay">
            <div class="modal-box">
                <h2>Crear carpeta</h2>
                <form action="../PHP conexiones/crearcarpeta.php" method="POST">
                    <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">
                    <label>Nombre de la carpeta:</label>
                    <input type="text" name="Nombre_Carpeta" required>
                    <div class="modal-acciones">
                        <button type="submit" class="btn-confirmar">Crear</button>
                        <a href="#" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        <div id="modal-proyecto" class="modal-overlay">
            <div class="modal-box">
                <h2>Publicar proyecto educativo</h2>
                <p>Comparte el trabajo final realizado durante el curso.</p>

                <form action="../PHP%20conexiones/subirProyecto.php" method="POST" enctype="multipart/form-data" >
                    <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">

                    <label for="tituloProyecto">Título del proyecto:</label>
                    <input type="text" id="tituloProyecto" name="Titulo_Proyecto" maxlength="200" required >

                    <label for="descripcionProyecto">Descripción del proyecto:</label>
                    <textarea id="descripcionProyecto" name="Descripcion" rows="5" placeholder="Explica qué se realizó, qué herramientas se utilizaron y qué se aprendió."
                    ></textarea>

                    <label for="archivoProyecto">Archivo del proyecto:</label>
                    <input type="file" id="archivoProyecto" name="archivo_proyecto" accept=".pdf,.zip,.rar,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg,.gif,.webp">

                    <label for="portadaProyecto">Imagen de portada (opcional):</label>
                    <input type="file" id="portadaProyecto" name="imagen_portada" accept="image/png,image/jpeg,image/gif,image/webp" >

                    <div class="modal-acciones">
                        <button type="submit" class="btn-confirmar">
                            Publicar proyecto
                        </button>
                        <a href="#" class="btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== MODAL CREAR TAREA ==================== -->
    <?php if ($esDocente && $esCreadorCurso): ?>
    <div id="modalTarea" class="modal-tarea-overlay">
        <div class="modal-tarea-container">
            <div class="modal-header">
                <h2>Crear Nueva Tarea</h2>
                <button type="button" class="btn-cerrar" id="btnCerrarModal">X</button>
            </div>

            <form id="formCrearTarea"
                action="../PHP%20conexiones/guardarTarea.php"
                method="POST"
                enctype="multipart/form-data">

                <input type="hidden" name="ID_Curso" value="<?= $idCurso ?>">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombreTarea">Nombre de la tarea *</label>
                        <input type="text" id="nombreTarea" name="nombre_tarea" 
                            placeholder="Ej: Ejercicios de Álgebra" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcionTarea">Descripción / Instrucciones</label>
                        <textarea id="descripcionTarea" name="descripcion_tarea" rows="4"
                                placeholder="Escribe las instrucciones para los alumnos..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="carpetaTarea">Asignar a carpeta (opcional):</label>
                        <select id="carpetaTarea" name="ID_Carpeta">
                            <option value="">Sin carpeta</option>
                            <?php foreach ($carpetas as $c): ?>
                                <option value="<?= $c['ID_Carpeta'] ?>">
                                    <?= htmlspecialchars($c['Nombre_Carpeta']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="archivosTarea">Adjuntar archivos</label>
                        <input type="file" id="archivosTarea" name="archivos_tarea[]" multiple>
                    </div>

                    <div class="form-group">
                        <label for="enlaceTarea">Adjuntar enlace / URL externa</label>
                        <input type="url" id="enlaceTarea" name="enlace_tarea" 
                            placeholder="https://ejemplo.com/recurso">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fechaVencimiento">Fecha de vencimiento *</label>
                            <input type="datetime-local" id="fechaVencimiento" 
                                name="fecha_vencimiento" required>
                        </div>

                        <div class="form-group">
                            <label for="fechaBloqueo">Fecha de bloqueo</label>
                            <input type="datetime-local" id="fechaBloqueo" name="fecha_bloqueo">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" id="btnCancelarModal">Cancelar</button>
                    <button type="submit" class="btn-publicar">Publicar Tarea</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php include 'footer.php'; ?>

    <script>
        // Selector de estrellas
        const estrellas = document.querySelectorAll('.estrella-btn');
        const inputPuntuacion = document.getElementById('inputPuntuacion');
        const textoPuntuacion = document.getElementById('textoPuntuacion');

        if (estrellas.length > 0) {
            estrellas.forEach(btn => {
                btn.addEventListener('click', () => {
                    const valor = parseInt(btn.dataset.valor);
                    inputPuntuacion.value = valor;
                    textoPuntuacion.textContent = valor + ' estrella(s)';

                    estrellas.forEach(b => {
                        b.classList.toggle('seleccionada', parseInt(b.dataset.valor) <= valor);
                    });
                });
            });
        }

        // Cerrar modales al hacer clic en Cancelar
        document.querySelectorAll('.btn-cancelar').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                window.location.hash = '';
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalTarea');
            const btnAbrir = document.getElementById('btnAbrirModal');
            const btnCerrar = document.getElementById('btnCerrarModal');
            const btnCancelar = document.getElementById('btnCancelarModal');
            const formTarea = document.getElementById('formCrearTarea');

            if (!modal || !btnAbrir) return;

            const abrirModal = () => {
                modal.classList.add('active');
            };

            const cerrarModal = () => {
                modal.classList.remove('active');
                if (formTarea) formTarea.reset();
            };

            btnAbrir.addEventListener('click', abrirModal);
            
            if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
            if (btnCancelar) btnCancelar.addEventListener('click', cerrarModal);

            // Cerrar al hacer clic fuera del modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) cerrarModal();
            });
        });

        // Mostrar archivo o enlace según el tipo elegido
        const tipoMaterial = document.getElementById('tipoMaterial');
        const grupoArchivo = document.getElementById('grupo-archivo');
        const grupoEnlace = document.getElementById('grupo-enlace');
        const archivoAdjunto = document.getElementById('archivoAdjunto');
        const contenidoURL = document.getElementById('contenidoURL');

        function actualizarTipoMaterial() {
            const esEnlace = tipoMaterial.value === 'enlace';

            grupoArchivo.style.display = esEnlace ? 'none' : 'block';
            grupoEnlace.style.display = esEnlace ? 'block' : 'none';

            archivoAdjunto.disabled = esEnlace;
            archivoAdjunto.required = !esEnlace;

            contenidoURL.disabled = !esEnlace;
            contenidoURL.required = esEnlace;

            if (esEnlace) {
                archivoAdjunto.value = '';
            } else {
                contenidoURL.value = '';
            }
        }

        if (tipoMaterial && grupoArchivo && grupoEnlace &&
            archivoAdjunto && contenidoURL) {
            tipoMaterial.addEventListener('change', actualizarTipoMaterial);
            actualizarTipoMaterial();
        }
    </script>
</body>
</html>