<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Verifica que exista una sesión iniciada
if (!isset($_SESSION['Sesion'])) {
    header("Location: login.php");
    exit();
}

// Consulta que permite traer los datos del usuario para el perfil
$correo = $_SESSION['Sesion'];
$sql = "SELECT ID_Usuario, Nombre, Apellido, Rol, Foto_Perfil 
        FROM usuarios 
        WHERE Correo = :correo";

$stmt = $pdo->prepare($sql);
$stmt->execute([':correo' => $correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: login.php");
    exit();
}


$idUsuario = $usuario['ID_Usuario'];

// Buscar tareas de los cursos inscritos que todavía no se entregaron
$sqlTareas = "SELECT t.ID_Tarea,
                     t.Nombre_Tarea,
                     t.Fecha_Vencimiento,
                     c.Titulo_Curso
              FROM inscripciones i
              INNER JOIN tareas t
                  ON t.ID_Curso = i.ID_Curso
              INNER JOIN cursos c
                  ON c.ID_Curso = t.ID_Curso
              LEFT JOIN entregas_tareas e
                  ON e.ID_Tarea = t.ID_Tarea
                  AND e.ID_Usuario = i.ID_Usuario
              WHERE i.ID_Usuario = :ID_Usuario
                AND e.ID_Entrega IS NULL
              ORDER BY t.Fecha_Vencimiento ASC";

$stmtTareas = $pdo->prepare($sqlTareas);
$stmtTareas->execute([
    ':ID_Usuario' => $idUsuario
]);

$tareasPendientes = $stmtTareas->fetchAll(PDO::FETCH_ASSOC);

// Buscar los cursos en los que está inscrito el usuario
$sqlCursos = "SELECT c.ID_Curso, c.Titulo_Curso
              FROM inscripciones i
              INNER JOIN cursos c
                  ON c.ID_Curso = i.ID_Curso
              WHERE i.ID_Usuario = :ID_Usuario
              ORDER BY c.Titulo_Curso ASC";

$stmtCursos = $pdo->prepare($sqlCursos);
$stmtCursos->execute([
    ':ID_Usuario' => $idUsuario
]);

$cursosEnRealizacion = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil | Aprendomo</title>
    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>

<?php include 'menu.php'; ?>

<main class="perfilMain">
    <!-- Información principal -->
    <section class="perfilUsuario">
        <div class="fotoPerfil">
            <?php if (!empty($usuario['Foto_Perfil'])): ?>
                <img src="../IMG/perfiles/<?php echo htmlspecialchars($usuario['Foto_Perfil']); ?>" alt="Foto de perfil">
            <?php else: ?>
                <img src="../IMG/aprendomoUser.jpg" alt="Foto de perfil">
            <?php endif; ?>
        </div>

        <div class="nombreYApaellidoUsuario">
            <h1><?php echo htmlspecialchars($usuario['Nombre'] . " " . $usuario['Apellido']); ?></h1>
            <p class="rolUsuario"><?php echo htmlspecialchars($usuario['Rol']); ?></p>

            <form action="../PHP conexiones/guardarFoto.php" method="POST" enctype="multipart/form-data">
                <label for="fotoPerfil" class="editarPerfil">
                    Cambiar foto de perfil
                </label>
                <input type="file" id="fotoPerfil" name="fotoPerfil" accept="image/*" hidden onchange="this.form.submit()">
            </form>
        </div>

        <div class="tareasPendientes">
            <h2>Tareas pendientes</h2>

            <div class="listaTareas">
                <?php if (!empty($tareasPendientes)): ?>

                    <?php foreach ($tareasPendientes as $tarea): ?>
                        <a href="tarea.php?ID_Tarea=<?= (int)$tarea['ID_Tarea'] ?>"
                        class="tareaPendiente"
                        style="text-decoration: none; color: inherit;">

                            <span>✓</span>

                            <div>
                                <h3>
                                    <?= htmlspecialchars($tarea['Titulo_Curso']) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($tarea['Nombre_Tarea']) ?>
                                </p>

                                <small>
                                    Vence el:
                                    <?= date('d/m/Y', strtotime($tarea['Fecha_Vencimiento'])) ?>
                                    a las:
                                    <?= date('H:i', strtotime($tarea['Fecha_Vencimiento'])) ?>
                                </small>
                            </div>
                        </a>
                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="tareaPendiente">
                        <span>✓</span>

                        <div>
                            <h3>No hay tareas pendientes</h3>
                            <p>¡Todo al día!</p>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Cursos aprobados (placeholder) -->
    <!-- <section class="cursosPerfil">
        <h2>Cursos aprobados</h2>
        <div class="listaCursosPerfil">
            <div class="cursoPerfil">
                <h3>Curso de ejemplo</h3>
                <p>Curso completado</p>
            </div>
        </div>
    </section> -->

    <!-- Cursos en realización -->
<section class="cursosPerfil">
    <h2>Cursos en realización</h2>

    <div class="listaCursosPerfil">

        <?php if (!empty($cursosEnRealizacion)): ?>

            <?php foreach ($cursosEnRealizacion as $curso): ?>
                <a href="vercursos.php?ID_Curso=<?= (int)$curso['ID_Curso'] ?>"
                   class="cursoPerfil">

                    <h3>
                        <?= htmlspecialchars($curso['Titulo_Curso']) ?>
                    </h3>

                    <span class="nivelCurso">En curso</span>

                </a>
            <?php endforeach; ?>

        <?php else: ?>

            <div class="cursoPerfil">
                <h3>No estás inscrito en ningún curso</h3>
                <p>¡Explora los cursos disponibles para comenzar!</p>
            </div>

        <?php endif; ?>

    </div>
</section>

    <!-- Ir a mis cursos -->
    <section class="botonMisCursos">
        <a href="miscursos.php">Ver mis cursos</a>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>