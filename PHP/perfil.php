<?php

session_start();
require '../PHP conexiones/conexion.php';

if (!isset($_SESSION['Sesion'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT Nombre, Apellido, Rol FROM usuario WHERE Correo = :Correo";

$consulta = $pdo->prepare($sql);
$consulta->bindParam(':Correo', $_SESSION['Sesion'], PDO::PARAM_STR);
$consulta->execute();

$usuario = $consulta->fetch();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
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
            <img src="../IMG/aprendomoUser.jpg" alt="Foto de perfil">
        </div>
        <div class="nombreYApaellidoUsuario">
            <h1><?php echo $usuario->Nombre . " " . $usuario->Apellido; ?></h1>
            <p class="rolUsuario"><?php echo $usuario->Rol; ?></p>

    <a href="editarPerfil.php" class="editarPerfil">
        Cambiar foto de perfil
    </a>

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
        <a href="#">Ver mis cursos</a>
    </section>
</main>
</body>
</html>
