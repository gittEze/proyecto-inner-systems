<?php
session_start();
require_once '../PHP conexiones/conexion.php';

// Verifica que exista una sesión iniciada
if (!isset($_SESSION['Sesion'])) {
    header("Location: login.php");
    exit();
}


$correo = $_SESSION['Sesion'];// Correo del usuario actual
$sql = "SELECT Nombre, Apellido, Rol, Foto_Perfil 
        FROM usuarios
        WHERE Correo = :correo";

$stmt = $pdo->prepare($sql);
// Se ejecuta la consulta pasando el correo del usuario
$stmt->execute([':correo' => $correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);// Variable con los datos del usuario
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

<div class="contenedorPerfil">
    <div class="logo">
        <a class="logo" id="logoPerfil" href="main.php">
            <img id="logoPerfilImg" src="../IMG/Logo.png" alt="Logo de Aprendomo">
        </a>
    </div>
    <div class="profileBtns">
        <a class="profileBtn" href="main.php" ><img class = "profileIcon" src="../IMG/volverIcon.png" alt="Volver"></a>
        <a class="profileBtn" href="#"><img class = "profileIcon" src="../IMG/mensajesIcon.png" alt="Mensajes"></a>
        <a class="profileBtn" href="#"><img class = "profileIcon" src="../IMG/notificacionesIcon.png" alt="Notificaciones"></a>
    </div>
</div>

<main class="perfilMain">
    <!-- Información principal y tareas pendientes -->
    <section class="perfilUsuario">
        <div class="fotoPerfil">
            <?php if (!empty($usuario['Foto_Perfil'])): ?>
                <img src="../IMG/perfiles/<?php echo htmlspecialchars($usuario['Foto_Perfil']); ?>" alt="Foto de perfil">
            <?php else: ?>
                <img src="../IMG/aprendomoUser.jpg" alt="Foto de perfil">
            <?php endif; ?>
        </div>
        <div class="nombreYApaellidoUsuario">
            <h1><?php echo $usuario['Nombre'] . " " . $usuario['Apellido']; ?></h1>
            <p class="rolUsuario"><?php echo $usuario['Rol']; ?></p>

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
                <div class="tareaPendiente">
                    <span>✓</span>
                    <div>
                        <h3>Nombre del curso</h3>
                        <p>Nombre de la tarea</p>
                        <small>Vence el: dd/mm/yyyy a las: hh:mm</small>
                    </div>
                </div>

                <div class="tareaPendiente">
                    <span>✓</span>
                    <div>
                        <h3>Nombre del curso</h3>
                        <p>Nombre de la tarea</p>
                        <small>Vence el: dd/mm/yyyy a las: hh:mm</small>
                    </div>
                </div>

                <div class="tareaPendiente">
                    <span>✓</span>
                    <div>
                        <h3>Nombre del curso</h3>
                        <p>Nombre de la tarea</p>
                        <small>Vence el: dd/mm/yyyy a las: hh:mm</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cursos aprobados -->
    <section class="cursosPerfil">
        <h2>Cursos aprobados</h2>
        <div class="listaCursosPerfil">
            <div class="cursoPerfil">
                <h3>Curso de ejemplo</h3>
                <p>Curso completado</p>
            </div>
            <div class="cursoPerfil">
                <h3>Curso de ejemplo 2</h3>
                <p>Curso completado</p>
            </div>
            <div class="cursoPerfil">
                <h3>Curso de ejemplo 3</h3>
                <p>Curso completado</p>
            </div>
        </div>
    </section>


    <!-- Cursos en realización -->
    <section class="cursosPerfil">
        <h2>Cursos en realización</h2>
        <div class="listaCursosPerfil">
            <div class="cursoPerfil">
                <h3>Curso de HTML & CSS</h3>
                <span class="nivelCurso">En curso</span>
            </div>
            <div class="cursoPerfil">
                <h3>JavaScript Básico</h3>
                <span class="nivelCurso">En curso</span>
            </div>
            <div class="cursoPerfil">
                <h3>Diseño Web Responsivo</h3>
                <span class="nivelCurso">En curso</span>
            </div>
        </div>
    </section>

    <!-- Ir a mis cursos -->
    <section class="botonMisCursos">
        <a href="mis_cursos.php">Ver mis cursos</a>
    </section>
</main>
</body>
</html>
