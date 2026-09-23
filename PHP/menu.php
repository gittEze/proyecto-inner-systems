<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="contenedorMain">
    <a class="logo" id="logoMain" href="main.php">
        <img id="logoMainImg" src="../IMG/Logo.png" alt="Logo de Aprendomo">
    </a>


    // condicional que evalua si el usuario se logeo, en caso de que si, aplica el if y muestra todos los enlaces, en caso contrario muestra todos menos mis cursos.

    <?php if (isset($_SESSION['Sesion'])) { ?>
    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
    <a class="Serv" href="mis_cursos.php">Mis cursos</a>
    <a class="Serv" href="proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="mentorías.php">Mentorías</a>
    <?php } else { ?>
    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
    <a class="Serv" href="proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="mentorías.php">Mentorías</a>
    <?php } ?>
 



    <input id="buscador" type="search" placeholder="Buscar cursos, servicios...">

    <?php if (isset($_SESSION['Sesion'])) { ?>
        <a class="loginBtn" href="../PHP conexiones/logout.php">
            Cerrar sesión
        </a>  
    <?php } else { ?>
        <form action="login.php" method="POST">
            <button class="loginBtn" name="btnLogin" value="login" type="submit">
                Iniciar sesión
            </button>
        </form>

        <form action="login.php" method="POST">
            <button class="loginBtn" name="btnRegister" value="register" type="submit">
                Registrarse
            </button>
        </form>
    <?php } ?>
</div>
