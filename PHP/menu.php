<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="contenedorMain">
    <a class="logo" id="logoMain" href="main.php">
        <img id="logoMainImg" src="../IMG/Logo.png" alt="Logo de Aprendomo">
    </a>

    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
    <a class="Serv" href="proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="mentorías.php">Mentorías</a>

    <input id="buscador" type="search" placeholder="Buscar cursos, servicios...">

    <?php if (isset($_SESSION['Sesion'])) { ?>
        <!-- Icono del usuario con menú desplegable. -->
        <div class="usuario">
            <img src="../IMG/aprendomoUser.jpg" alt="userIcon" class="userIcon">
            <div class="userMenu">
                <div class="userMenuBox">
                    <div class="userInfo">
                        <img src="../IMG/aprendomoUser.jpg" alt="Foto del usuario" class="userIcon">
                        <h2>Aprendomo User</h2>
                    </div>
                    <hr>
                    <div class="userMenuOptions">
                        <!-- Mi Perfil -->
                         <a href="perfil.php">
                            <div>
                                <img src="../IMG/MyProfileIcon.png" alt="Mi Perfil Icon">
                                <p>Mi perfil</p>
                                <div class="userSpanContenedor">
                                    <span> </span>
                                </div>
                            </div>
                         </a>
                        <!-- Mis Cursos -->
                         <a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR4nPptYxnzR73BjRoIAmoBtwhPU54WkqYy0MNJVhiy28nxvEN10iX-FnAF&s=10" target="_blank">
                            <div>
                                <img src="../IMG/MisCursosIcon.jpg" alt="Mis Cursos Icon">
                                <p>Mis cursos</p>
                                <div class="userSpanContenedor">
                                    <span> </span>
                                </div>
                            </div>
                         </a>
                        <!-- Cerrar sesión -->
                        <a href="../PHP conexiones/logout.php">
                            <div>
                                <img src="../IMG/ExitIcon.jpg" alt="Cerrar Sesión Icon">
                                <p>Cerrar sesión</p>
                                <div class="userSpanContenedor">
                                    <span> </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>        
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
<!-- Este Script hace que el menú pueda aparecer y ocultarse tras presionar en el icono de usuario
 De ser posible, luego el contenido de esté script puede moverse a un archivo .js aparte. -->
<script>
    const userIcon = document.querySelector('.userIcon');
    const menu = document.querySelector('.userMenu');

    userIcon.addEventListener('click', () => {
    menu.classList.toggle('open-menu');
})
</script>