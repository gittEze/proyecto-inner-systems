<?php
session_start();
require_once '../PHP conexiones/conexion.php';

$tieneSesion = isset($_SESSION['Sesion']) && isset($_SESSION['ID_Usuario']);

// Obtener los 4 cursos mejor valorados
$sqlPopulares = "
    SELECT
        c.*,
        ROUND(AVG(v.Puntuacion), 1) AS promedio_valoracion,
        COUNT(v.ID_Valoracion) AS total_valoraciones
    FROM cursos c
    INNER JOIN valoraciones v
        ON v.ID_Curso = c.ID_Curso
    GROUP BY c.ID_Curso
    ORDER BY
        AVG(v.Puntuacion) DESC,
        COUNT(v.ID_Valoracion) DESC,
        c.ID_Curso DESC
    LIMIT 4
";

$stmtPopulares = $pdo->prepare($sqlPopulares);
$stmtPopulares->execute();

$cursosPopulares = $stmtPopulares->fetchAll(PDO::FETCH_ASSOC);


// Obtener comentarios y valoraciones de los cursos
$sqlComentarios = "
    SELECT
        v.Comentario,
        v.Puntuacion,
        v.Fecha,
        u.Nombre,
        u.Apellido,
        u.Foto_Perfil,
        c.ID_Curso,
        c.Titulo_Curso
    FROM valoraciones v
    INNER JOIN usuarios u
        ON u.ID_Usuario = v.ID_Usuario
    INNER JOIN cursos c
        ON c.ID_Curso = v.ID_Curso
    WHERE v.Comentario IS NOT NULL
      AND TRIM(v.Comentario) <> ''
    ORDER BY v.Fecha DESC
";

$stmtComentarios = $pdo->prepare($sqlComentarios);
$stmtComentarios->execute();

$comentariosCursos = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);

$sqlDocentes = "
    SELECT
        u.ID_Usuario,
        u.Nombre,
        u.Apellido,
        u.Foto_Perfil,
        COUNT(c.ID_Curso) AS Cantidad_Cursos
    FROM usuarios u
    LEFT JOIN cursos c
        ON c.ID_Docente = u.ID_Usuario
    WHERE LOWER(TRIM(u.Rol)) = 'docente'
    GROUP BY
        u.ID_Usuario,
        u.Nombre,
        u.Apellido,
        u.Foto_Perfil
    ORDER BY Cantidad_Cursos DESC, u.Nombre ASC
    LIMIT 6
";

$stmtDocentes = $pdo->prepare($sqlDocentes);
$stmtDocentes->execute();
$docentesDestacados = $stmtDocentes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inicio | Aprendomo</title>

        <link rel="stylesheet" href="../CSS/animaciones.css">
        <link rel="stylesheet" href="../CSS/estilo.css">
        <link rel="icon" href="../IMG/Icono.ico">
    </head>

    <body class="bodyMain">

        <?php include 'menu.php'; ?>

        <main>
            <div class="contenedorInicio">
                <div class="textoPrincipal">
                    <h2>
                        Tu conocimiento,<br><span>tu futuro</span>
                    </h2>

                    <p>En Aprendomo tienes acceso a cursos, proyectos educativos y mentorias al mejor precio. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Commodi omnis in eveniet, cumque assumenda eum nostrum reiciendis facere dolores recusandae consequatur placeat dolorum voluptatibus quisquam totam fugiat autem cupiditate expedita?</p>
                    
                    <a class="btnPrincipal" href="cursos.php">
                        Explorar cursos
                    </a>
                </div>

                <div class="imagenPrincipal">
                    <img src="../IMG/principal.png" alt="Estudiante realizando un curso online">
                </div>
            </div>

            <div class="cursosPopularesTitulo">
                <h2>Cursos populares</h2>
                <a href="cursos.php">Ver todos</a>
            </div>

            <div class="cursosPopulares">

                <?php if (!empty($cursosPopulares)): ?>

                    <?php foreach ($cursosPopulares as $curso): ?>

                        <?php
                            $promedio = (float) $curso['promedio_valoracion'];
                            $totalValoraciones = (int) $curso['total_valoraciones'];
                            $estrellasLlenas = (int) round($promedio);
                        ?>

                        <a
                            href="vercursos.php?ID_Curso=<?= (int) $curso['ID_Curso'] ?>"
                            class="tarjetaCurso"
                        >

                            <div class="imagenCurso">
                                <?php if (!empty($curso['Dataso'])): ?>
                                    <img
                                        src="../PHP conexiones/Imagenes/<?= htmlspecialchars($curso['Dataso'], ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($curso['Titulo_Curso'], ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                <?php else: ?>
                                    <img
                                        src="../IMG/Curso1.png"
                                        alt="Imagen del curso"
                                    >
                                <?php endif; ?>
                            </div>

                            <div class="informacionCurso">

                                <h3>
                                    <?= htmlspecialchars($curso['Titulo_Curso'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($curso['Descripcion_Curso'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <div class="popular-valoracion">

                                    <div class="popular-estrellas" aria-label="Promedio de <?= number_format($promedio, 1) ?> sobre 5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= $estrellasLlenas ? 'estrella-activa' : 'estrella-inactiva' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>

                                    <span class="popular-promedio">
                                        <?= number_format($promedio, 1) ?>/5
                                    </span>

                                    <span class="popular-cantidad">
                                        (<?= $totalValoraciones ?> valoraciones)
                                    </span>

                                </div>

                                <?php if (!empty($curso['Nivel_Curso'])): ?>
                                    <span class="nivelCurso">
                                        <?= htmlspecialchars($curso['Nivel_Curso'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>

                            </div>

                        </a>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="sin-cursos-populares">
                        Todavía no hay cursos valorados. ¡Sé de los primeros en valorar uno!
                    </p>

                <?php endif; ?>

            </div>
            
            <!-- Comentarios y valoraciones de la comunidad -->
            <section class="seccionComentarios">

                <div class="comentariosTitulo">
                    <div>
                        <h2>Valoraciones de cursos</h2>
                        <p>Experiencias y opiniones de quienes ya realizaron nuestros cursos.</p>
                    </div>
                </div>

                <?php if (!empty($comentariosCursos)): ?>

                    <div class="carruselComentarios">

                        <div class="pistaComentarios">

                            <?php
                            // Duplicamos las tarjetas para conseguir un desplazamiento continuo.
                            for ($repeticion = 0; $repeticion < 2; $repeticion++):
                                foreach ($comentariosCursos as $comentario):
                                    $puntuacion = (int) $comentario['Puntuacion'];
                            ?>

                                <article class="tarjetaComentario">

                                    <div class="comentarioCabecera">
                                        <div class="comentarioAvatar">
                                            <?php if (!empty($comentario['Foto_Perfil'])): ?>
                                                <img
                                                    src="../IMG/perfiles/<?= htmlspecialchars(
                                                        basename($comentario['Foto_Perfil']),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                    alt="Foto de <?= htmlspecialchars(
                                                        $comentario['Nombre'] . ' ' . $comentario['Apellido'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                    loading="lazy"
                                                >
                                            <?php else: ?>
                                                <img
                                                    src="../IMG/aprendomoUser.jpg"
                                                    alt="Foto de perfil predeterminada"
                                                    loading="lazy"
                                                >
                                            <?php endif; ?>
                                        </div>

                                        <div class="comentarioAutor">
                                            <h3>
                                                <?= htmlspecialchars(
                                                    $comentario['Nombre'] . ' ' . $comentario['Apellido'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </h3>

                                            <span>Estudiante de Aprendomo</span>
                                        </div>
                                    </div>

                                    <div class="comentarioEstrellas"
                                        aria-label="Valoración de <?= $puntuacion ?> sobre 5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= $puntuacion ? 'estrellaLlena' : 'estrellaVacia' ?>">★</span>
                                        <?php endfor; ?>

                                        <strong><?= $puntuacion ?>/5</strong>
                                    </div>

                                    <p class="comentarioTexto">
                                        “<?= nl2br(htmlspecialchars(
                                            $comentario['Comentario'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )) ?>”
                                    </p>

                                    <a
                                        class="comentarioCurso"
                                        href="<?= $tieneSesion
                                            ? 'vercursos.php?ID_Curso=' . (int) $comentario['ID_Curso'] . '&tab=valoraciones'
                                            : 'login.php'
                                        ?>"
                                        title="<?= $tieneSesion
                                            ? 'Ver las valoraciones de este curso'
                                            : 'Iniciá sesión para ver el curso'
                                        ?>"
                                    >
                                        <span class="comentarioCursoIcono">↗</span>

                                        <span>
                                            <small>Comentario sobre</small>
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $comentario['Titulo_Curso'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>
                                        </span>
                                    </a>

                                </article>

                            <?php
                                endforeach;
                            endfor;
                            ?>

                        </div>

                    </div>

                <?php else: ?>

                    <div class="comentariosVacios">
                        <span>💬</span>
                        <h3>¡La comunidad está comenzando!</h3>
                        <p>Cuando los estudiantes dejen comentarios en sus cursos, aparecerán aquí.</p>
                        <a href="cursos.php">Explorar cursos</a>
                    </div>

                <?php endif; ?>
            </section>

            <!-- Cómo funciona Aprendomo -->
            <section class="comoFunciona" id="como-funciona">

                <div class="comoFuncionaEncabezado">

                    <h2>¿Cómo funciona Aprendomo?</h2>

                    <p>
                        Aprender algo nuevo es más sencillo de lo que pensás.<br>
                        Seguí estos tres pasos y comenzá tu camino.
                    </p>
                </div>

                <div class="comoFuncionaPasos">

                    <!-- Paso 1 -->
                    <article class="pasoAprendizaje">

                        <div class="pasoIcono" aria-hidden="true">🔎</div>

                        <h3>Elegí tu curso</h3>

                        <p>
                            Explorá los cursos disponibles y encontrá el que mejor
                            se adapte a tus intereses y objetivos.
                        </p>

                    </article>

                    <!-- Paso 2 -->
                    <article class="pasoAprendizaje">

                        <div class="pasoIcono" aria-hidden="true">📚</div>

                        <h3>Aprendé a tu ritmo</h3>

                        <p>
                            Accedé a los contenidos de tus cursos y avanzá paso a
                            paso mientras desarrollás nuevos conocimientos.
                        </p>

                    </article>

                    <!-- Paso 3 -->
                    <article class="pasoAprendizaje">

                        <div class="pasoIcono" aria-hidden="true">🎯</div>

                        <h3>Seguí creciendo</h3>

                        <p>
                            Aplicá lo aprendido, descubrí nuevas áreas de interés
                            y continuá desarrollando tus habilidades.
                        </p>

                    </article>

                </div>

                <!-- Botón para explorar cursos -->
                <div class="comoFuncionaAccion">
                    <a href="cursos.php" class="btnComoFunciona">
                        Explorar cursos
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

            </section>

            <!-- Docentes destacados -->
            <section class="docentesDestacados">

                <div class="docentesEncabezado">
                    <div>
                        <h2>Docentes en Aprendomo</h2>
                        <p>
                            Conocé a quienes comparten sus conocimientos
                            y te acompañan en tu aprendizaje.
                        </p>
                    </div>

                    <a href="cursos.php" class="docentesVerTodos">
                        Explorar cursos <span aria-hidden="true">→</span>
                    </a>
                </div>

                <?php if (!empty($docentesDestacados)): ?>

                    <div class="docentesLista">

                        <?php foreach ($docentesDestacados as $docente): ?>

                            <article class="tarjetaDocente">

                                <div class="docenteFoto">
                                    <?php if (!empty($docente['Foto_Perfil'])): ?>

                                        <img
                                            src="../IMG/perfiles/<?= htmlspecialchars(
                                                basename($docente['Foto_Perfil']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            alt="Foto de <?= htmlspecialchars(
                                                trim($docente['Nombre'] . ' ' . $docente['Apellido']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <img
                                            src="../IMG/aprendomoUser.jpg"
                                            alt="Foto de perfil predeterminada"
                                            loading="lazy"
                                        >

                                    <?php endif; ?>
                                </div>

                                <span class="docenteRol">DOCENTE</span>

                                <h3>
                                    <?= htmlspecialchars(
                                        trim($docente['Nombre'] . ' ' . $docente['Apellido']),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h3>

                                <div class="docenteCursos">
                                    <span aria-hidden="true">📚</span>

                                    <?= (int) $docente['Cantidad_Cursos'] ?>

                                    <?= (int) $docente['Cantidad_Cursos'] === 1
                                        ? 'curso publicado'
                                        : 'cursos publicados' ?>
                                </div>

                                <a href="cursos.php" class="docenteBoton">
                                    Ver cursos
                                    <span aria-hidden="true">→</span>
                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="docentesVacio">
                        <span aria-hidden="true">👨‍🏫</span>
                        <h3>Próximamente, nuevos docentes</h3>
                        <p>Estamos preparando nuevas oportunidades para aprender.</p>
                    </div>

                <?php endif; ?>

            </section>
        </main>

        <?php include 'footer.php'; ?>

    </body>
</html> 
