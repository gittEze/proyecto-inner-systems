<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    require_once '../PHP conexiones/conexion.php';  
}

if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion']; // Correo del usuario actual
    $sql = "SELECT Nombre, Apellido, Rol, Foto_Perfil FROM usuario WHERE Correo = :correo";
    $stmt = $pdo->prepare($sql);
    // Se ejecuta la consulta pasando el correo del usuario
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Variable con los datos del usuario
}
?>

<div class="contenedorMain">
    <a class="logo" id="logoMain" href="main.php">
        <img id="logoMainImg" src="../IMG/Logo.png" alt="Logo de Aprendomo">
    </a>


    <!-- Condicional que evalua si el usuario se logeo, en caso de que si, aplica el if y muestra todos los enlaces, en caso contrario muestra todos menos mis cursos. -->

    <?php if (isset($_SESSION['Sesion'])) { ?>
    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
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
        <!-- Icono del usuario con menú desplegable. -->
        <div class="usuario">
            <?php if (!empty($usuario['Foto_Perfil'])): ?>
                <img src="../IMG/perfiles/<?php echo htmlspecialchars($usuario['Foto_Perfil']); ?>" alt="Foto de perfil" class="userIcon">
            <?php else: ?>
                <img src="../IMG/aprendomoUser.jpg" alt="Foto de perfil" class="userIcon">
            <?php endif; ?>
            <div class="userMenu">
                <div class="userMenuBox">
                    <div class="userInfo">
                        <?php if (!empty($usuario['Foto_Perfil'])): ?>
                            <img src="../IMG/perfiles/<?php echo htmlspecialchars($usuario['Foto_Perfil']); ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <img src="../IMG/aprendomoUser.jpg" alt="Foto de perfil">
                        <?php endif; ?>
                        <h2><?php echo $usuario['Nombre'] . " " . $usuario['Apellido']; ?></h2>
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
                         <a href="mis_cursos.php">
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