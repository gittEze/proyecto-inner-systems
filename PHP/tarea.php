<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../PHP conexiones/conexion.php';

// Verificar sesión
if (!isset($_SESSION['Sesion'])) {
    header("Location: login.php");
    exit();
}

// Obtener usuario
$sqlUsuario = "SELECT ID_Usuario, Rol
               FROM usuarios
               WHERE Correo = :correo";

$stmtUsuario = $pdo->prepare($sqlUsuario);
$stmtUsuario->execute([
    ':correo' => $_SESSION['Sesion']
]);

$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: login.php");
    exit();
}

$idUsuario = (int)$usuario['ID_Usuario'];
$rol = strtolower(trim($usuario['Rol']));

// Verificar ID de la tarea
$idTarea = (int)($_GET['ID_Tarea'] ?? 0);

if ($idTarea <= 0) {
    header("Location: miscursos.php");
    exit();
}

// Obtener tarea y curso
$sqlTarea = "SELECT t.*, c.Titulo_Curso, c.ID_Docente
             FROM tareas t
             INNER JOIN cursos c ON c.ID_Curso = t.ID_Curso
             WHERE t.ID_Tarea = :ID_Tarea";

$stmtTarea = $pdo->prepare($sqlTarea);
$stmtTarea->execute([
    ':ID_Tarea' => $idTarea
]);

$tarea = $stmtTarea->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    die("La tarea no existe.");
}

// Comprobar permisos

/* Comprobar permisos */
$esAdministrador = ($rol === 'administrador');

$esDocente = ($rol === 'docente');

$esDocenteCurso = (
    $esDocente &&
    $idUsuario === (int)$tarea['ID_Docente']
);

$esAlumno = ($rol === 'estudiante');

$inscrito = false;

// Comprobar inscripción de estudiantes y docentes
if ($esAlumno || $esDocente) {

    $sqlInscripcion = "SELECT ID_Usuario
                       FROM inscripciones
                       WHERE ID_Usuario = :ID_Usuario
                       AND ID_Curso = :ID_Curso";

    $stmtInscripcion = $pdo->prepare($sqlInscripcion);

    $stmtInscripcion->execute([
        ':ID_Usuario' => $idUsuario,
        ':ID_Curso' => $tarea['ID_Curso']
    ]);

    $inscrito = $stmtInscripcion->fetch(PDO::FETCH_ASSOC) !== false;
}

// Un docente inscrito puede consultar la tarea,
// aunque no sea el creador del curso.
$esDocenteInscrito = ($esDocente && $inscrito);

// Verificar acceso
if (
    !$esAdministrador &&
    !$esDocenteCurso &&
    !$esDocenteInscrito &&
    !($esAlumno && $inscrito)
) {
    die("No tienes permiso para consultar esta tarea.");
}

// Obtener archivos adjuntos del docente
$sqlArchivos = "SELECT Nombre_Archivo, Ruta_Archivo
                FROM archivos_tarea
                WHERE ID_Tarea = :ID_Tarea";

$stmtArchivos = $pdo->prepare($sqlArchivos);
$stmtArchivos->execute([
    ':ID_Tarea' => $idTarea
]);

$archivos = $stmtArchivos->fetchAll(PDO::FETCH_ASSOC);

// Obtener entrega del estudiante actual
$entrega = null;

if (($esAlumno || $esDocente) && $inscrito) {
    $sqlEntrega = "SELECT *
                   FROM entregas_tareas
                   WHERE ID_Tarea = :ID_Tarea
                   AND ID_Usuario = :ID_Usuario";

    $stmtEntrega = $pdo->prepare($sqlEntrega);
    $stmtEntrega->execute([
        ':ID_Tarea' => $idTarea,
        ':ID_Usuario' => $idUsuario
    ]);

    $entrega = $stmtEntrega->fetch(PDO::FETCH_ASSOC);
}

// Obtener entregas de todos los estudiantes para el docente propietario
$entregasAlumnos = [];

if ($esDocenteCurso) {
    $sqlEntregas = "SELECT e.*,
                       u.Nombre,
                       u.Apellido,
                       u.Foto_Perfil
                    FROM entregas_tareas e
                    INNER JOIN usuarios u
                        ON u.ID_Usuario = e.ID_Usuario
                    WHERE e.ID_Tarea = :ID_Tarea
                    ORDER BY e.Fecha_Entrega DESC";

    $stmtEntregas = $pdo->prepare($sqlEntregas);
    $stmtEntregas->execute([
        ':ID_Tarea' => $idTarea
    ]);

    $entregasAlumnos = $stmtEntregas->fetchAll(PDO::FETCH_ASSOC);
}

// Comprobar si la tarea está bloqueada
$bloqueada = false;

if (!empty($tarea['Fecha_Bloqueo'])) {
    $bloqueada = (time() > strtotime($tarea['Fecha_Bloqueo']));
}

// Fechas
$fechaPublicacion = date(
    'd/m/Y H:i',
    strtotime($tarea['Fecha_Publicacion'])
);

$fechaVencimiento = date(
    'd/m/Y H:i',
    strtotime($tarea['Fecha_Vencimiento'])
);

$entregaTardia = (
    time() > strtotime($tarea['Fecha_Vencimiento'])
);

$fechaBloqueo = 'No establecida';

if (!empty($tarea['Fecha_Bloqueo'])) {
    $timestampBloqueo = strtotime($tarea['Fecha_Bloqueo']);

    if ($timestampBloqueo !== false) {
        $fechaBloqueo = date(
            'd/m/Y H:i',
            $timestampBloqueo
        );
    }
}

$puedeEntregar = (
    ($esAlumno || $esDocente) &&
    $inscrito &&
    !$bloqueada
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($tarea['Nombre_Tarea']) ?> - Aprendomo</title>

    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="stylesheet" href="../CSS/tareaEstilo.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>

<body>
    <?php include 'menu.php'; ?>

    <main class="pagina-tarea">

        <a class="volver-tarea"
           href="vercursos.php?ID_Curso=<?= (int)$tarea['ID_Curso'] ?>&tab=calificaciones">
            ← Volver al curso
        </a>

        <?php if (isset($_GET['entrega']) && $_GET['entrega'] === 'ok'): ?>
            <p class="mensaje-tarea mensaje-exito">
                Tu entrega se guardó correctamente.
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['calificacion']) && $_GET['calificacion'] === 'ok'): ?>
            <p class="mensaje-tarea mensaje-exito">
                La calificación se guardó correctamente.
            </p>
        <?php endif; ?>

        <!-- Información de la tarea -->
        <section class="tarjeta-tarea">

            <p class="curso-tarea">
                <?= htmlspecialchars($tarea['Titulo_Curso']) ?>
            </p>

            <h1><?= htmlspecialchars($tarea['Nombre_Tarea']) ?></h1>

            <div class="datos-tarea">
                <p>
                    <strong>Publicada:</strong>
                    <?= $fechaPublicacion ?>
                </p>

                <p>
                    <strong>Vencimiento:</strong>
                    <?= $fechaVencimiento ?>
                </p>

                <p>
                    <strong>Fecha de bloqueo:</strong>
                    <?= htmlspecialchars($fechaBloqueo) ?>
                </p>
            </div>

            <?php if ($entregaTardia): ?>
                <p class="mensaje-tarea mensaje-aviso">
                    La fecha de vencimiento ya pasó.
                </p>
            <?php endif; ?>

            <?php if ($bloqueada): ?>
                <p class="mensaje-tarea mensaje-aviso">
                    Esta tarea está cerrada y ya no acepta entregas.
                </p>
            <?php endif; ?>

            <h2>Descripción de la tarea</h2>

            <div class="descripcion-tarea">
                <?php if (!empty($tarea['Descripcion_Tarea'])): ?>
                    <?= nl2br(htmlspecialchars($tarea['Descripcion_Tarea'])) ?>
                <?php else: ?>
                    El docente no agregó una descripción.
                <?php endif; ?>
            </div>

            <?php if (!empty($tarea['Enlace_Tarea'])): ?>
                <?php
                $urlTarea = $tarea['Enlace_Tarea'];

                $esEnlaceValido = filter_var(
                    $urlTarea,
                    FILTER_VALIDATE_URL
                ) && in_array(
                    strtolower(parse_url($urlTarea, PHP_URL_SCHEME) ?? ''),
                    ['http', 'https']
                );
                ?>

                <?php if ($esEnlaceValido): ?>
                    <h2>Enlace de la actividad</h2>

                    <a class="boton-tarea"
                       href="<?= htmlspecialchars($urlTarea) ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        Abrir enlace
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <h2>Archivos adjuntos</h2>

            <?php if (!empty($archivos)): ?>
                <div class="lista-archivos-tarea">

                    <?php foreach ($archivos as $archivo): ?>
                        <div class="archivo-tarea">
                            <span>
                                📎 <?= htmlspecialchars($archivo['Nombre_Archivo']) ?>
                            </span>

                            <a href="subidas/archivos/<?= rawurlencode($archivo['Ruta_Archivo']) ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                Abrir archivo
                            </a>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <p>Esta tarea no tiene archivos adjuntos.</p>
            <?php endif; ?>

        </section>

        <!-- Sección del estudiante -->
        <?php if (($esAlumno || $esDocente) && $inscrito): ?>

            <section class="tarjeta-tarea">
                <h2>Mi entrega</h2>

                <?php if ($entrega): ?>

                    <p class="mensaje-tarea mensaje-exito">
                        Ya has realizado una entrega.
                    </p>

                    <p>
                        <strong>Fecha de entrega:</strong>
                        <?= date('d/m/Y H:i', strtotime($entrega['Fecha_Entrega'])) ?>
                    </p>

                    <?php if (!empty($entrega['Nombre_Archivo'])): ?>
                        <p>
                            <strong>Archivo entregado:</strong>
                            <a href="subidas/entregas/<?= rawurlencode($entrega['Nombre_Archivo']) ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                Ver archivo
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($entrega['Enlace_Entrega'])): ?>
                        <p>
                            <strong>Enlace entregado:</strong>
                            <a href="<?= htmlspecialchars($entrega['Enlace_Entrega']) ?>"
                               target="_blank"
                               rel="noopener noreferrer">
                                Abrir enlace
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($entrega['Comentario_Entrega'])): ?>
                        <p>
                            <strong>Comentario:</strong><br>
                            <?= nl2br(htmlspecialchars($entrega['Comentario_Entrega'])) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($entrega['Calificacion'] !== null): ?>

                        <p>
                            <strong>Calificación:</strong>
                            <?= round((float)$entrega['Calificacion']) ?>
                        </p>

                        <?php if (!empty($entrega['Comentario_Docente'])): ?>
                            <p>
                                <strong>Comentario del docente:</strong><br>
                                <?= nl2br(htmlspecialchars($entrega['Comentario_Docente'])) ?>
                            </p>
                        <?php endif; ?>

                    <?php else: ?>
                        <p>Tu entrega todavía no ha sido calificada.</p>
                    <?php endif; ?>

                <?php else: ?>

                    <p>Todavía no has entregado esta tarea.</p>

                <?php endif; ?>

                <?php if ($puedeEntregar): ?>

                    <form action="../PHP conexiones/entregarTarea.php"
                          method="POST"
                          enctype="multipart/form-data"
                          class="formulario-entrega">

                        <input type="hidden"
                               name="ID_Tarea"
                               value="<?= $idTarea ?>">

                        <label for="archivo_entrega">
                            Archivo (opcional)
                        </label>

                        <input type="file"
                               name="archivo_entrega"
                               id="archivo_entrega"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif">

                        <label for="enlace_entrega">
                            Enlace de tu trabajo (opcional)
                        </label>

                        <input type="url"
                               name="enlace_entrega"
                               id="enlace_entrega"
                               placeholder="https://..."
                               value="<?= htmlspecialchars($entrega['Enlace_Entrega'] ?? '') ?>">

                        <label for="comentario_entrega">
                            Comentario (opcional)
                        </label>

                        <textarea name="comentario_entrega"
                                  id="comentario_entrega"
                                  rows="4"
                                  placeholder="Escribe un comentario..."><?= htmlspecialchars($entrega['Comentario_Entrega'] ?? '') ?></textarea>

                        <p class="ayuda-entrega">
                            Puedes adjuntar un archivo, proporcionar un enlace
                            o escribir un comentario.

                            <?php if ($entrega): ?>
                                Si no seleccionas otro archivo, se conservará
                                el archivo que ya entregaste.
                            <?php endif; ?>
                        </p>

                        <button type="submit" class="boton-tarea">
                            <?= $entrega ? 'Actualizar entrega' : 'Entregar tarea' ?>
                        </button>

                    </form>

                <?php elseif ($bloqueada): ?>

                    <p class="mensaje-tarea mensaje-aviso">
                        El plazo para entregar esta tarea está cerrado.
                    </p>

                <?php endif; ?>

            </section>

        <?php endif; ?>

        <!-- Sección exclusiva del docente propietario -->
        <?php if ($esDocenteCurso): ?>

            <section class="tarjeta-tarea">
                <h2>Entregas de los estudiantes</h2>

                <?php if (!empty($entregasAlumnos)): ?>

                    <?php foreach ($entregasAlumnos as $entregaAlumno): ?>

                        <div class="entrega-alumno">
                            <?php if (!empty($entregaAlumno['Foto_Perfil'])): ?>
                                <img
                                    src="<?= htmlspecialchars($entregaAlumno['Foto_Perfil']) ?>"
                                    alt="Foto de perfil"
                                    class="foto-estudiante-entrega"
                                >
                            <?php else: ?>
                                <img
                                    src="../IMG/aprendomoUser.jpg"
                                    alt="Foto de perfil"
                                    class="foto-estudiante-entrega"
                                >
                            <?php endif; ?>

                            <h3>
                                <?= htmlspecialchars(
                                    $entregaAlumno['Nombre'] . ' ' .
                                    $entregaAlumno['Apellido']
                                ) ?>
                            </h3>

                            <p>
                                <strong>Fecha de entrega:</strong>
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime($entregaAlumno['Fecha_Entrega'])
                                ) ?>
                            </p>

                            <?php if (!empty($entregaAlumno['Nombre_Archivo'])): ?>
                                <p>
                                    <strong>Archivo:</strong>
                                    <a href="subidas/entregas/<?= rawurlencode($entregaAlumno['Nombre_Archivo']) ?>"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        Ver archivo entregado
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($entregaAlumno['Enlace_Entrega'])): ?>
                                <p>
                                    <strong>Enlace:</strong>
                                    <a href="<?= htmlspecialchars($entregaAlumno['Enlace_Entrega']) ?>"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        Abrir enlace entregado
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($entregaAlumno['Comentario_Entrega'])): ?>
                                <p>
                                    <strong>Comentario del estudiante:</strong><br>
                                    <?= nl2br(htmlspecialchars($entregaAlumno['Comentario_Entrega'])) ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($entregaAlumno['Calificacion'] !== null): ?>
                                <p>
                                    <strong>Calificación actual:</strong>
                                    <?= htmlspecialchars((string)$entregaAlumno['Calificacion']) ?>
                                </p>
                            <?php else: ?>
                                <p>Esta entrega todavía no tiene calificación.</p>
                            <?php endif; ?>

                            <form action="../PHP conexiones/guardarCalificacion.php"
                                  method="POST"
                                  class="formulario-entrega">

                                <input type="hidden"
                                       name="ID_Tarea"
                                       value="<?= $idTarea ?>">

                                <input type="hidden"
                                       name="ID_Entrega"
                                       value="<?= (int)$entregaAlumno['ID_Entrega'] ?>">

                                <label for="calificacion_<?= (int)$entregaAlumno['ID_Entrega'] ?>">
                                    Calificación
                                </label>

                                <input type="number"
                                    id="calificacion_<?= (int)$entregaAlumno['ID_Entrega'] ?>"
                                    name="Calificacion"
                                    min="0"
                                    step="1"
                                    value="<?= htmlspecialchars(
                                        (string)($entregaAlumno['Calificacion'] ?? '')
                                    ) ?>"
                                    required>

                                <label for="comentario_docente_<?= (int)$entregaAlumno['ID_Entrega'] ?>">
                                    Comentario del docente
                                </label>

                                <textarea id="comentario_docente_<?= (int)$entregaAlumno['ID_Entrega'] ?>"
                                          name="Comentario_Docente"
                                          rows="3"><?= htmlspecialchars(
                                              $entregaAlumno['Comentario_Docente'] ?? ''
                                          ) ?></textarea>

                                <button type="submit" class="boton-tarea">
                                    Guardar calificación
                                </button>

                            </form>

                            <hr>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>Todavía no hay entregas de estudiantes.</p>

                <?php endif; ?>

            </section>

        <?php endif; ?>

    </main>
</body>
</html>
