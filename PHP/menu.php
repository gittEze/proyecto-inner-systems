<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../PHP conexiones/conexion.php';  

$usuario = null;

if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion']; 
    $sql = "SELECT Nombre, Apellido, Rol, Foto_Perfil FROM usuarios WHERE Correo = :correo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="contenedorMain">
    <a class="logo" id="logoMain" href="/VSC/proyecto-inner-systems/PHP/main.php">
        <img id="logoMainImg" src="/VSC/proyecto-inner-systems/IMG/Logo.png" alt="Logo de Aprendomo">
    </a>
     
<?php if (
    isset($_SESSION['Sesion']) &&
    isset($usuario['Rol']) &&
    strtolower(trim($usuario['Rol'])) === 'administrador'
) { ?>
    <!-- Administrador -->
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/main.php">Inicio</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/cursos.php">Cursos</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/mentorías.php">Mentorías</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/PHPAdmin/menuadmin.php">Panel de Administración</a>

<?php } elseif (isset($_SESSION['Sesion']) && $usuario) { ?>

    <!-- Usuario normal -->
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/main.php">Inicio</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/cursos.php">Cursos</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/mentorías.php">Mentorías</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/carrito.php">
        Carrito
        <?php
        $cantCarrito = (
            isset($_SESSION['carrito']) &&
            is_array($_SESSION['carrito'])
        ) ? count($_SESSION['carrito']) : 0;

        if ($cantCarrito > 0) {
            echo " (" . $cantCarrito . ")";
        }
        ?>
    </a>

<?php } else { ?>

    <!-- Visitante sin sesión -->
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/main.php">Inicio</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/cursos.php">Cursos</a>
    <a class="Serv" href="/VSC/proyecto-inner-systems/PHP/proyectos.php">Proyectos educativos</a>

<?php } ?>

    <input id="buscador" type="search" placeholder="Buscar cursos, servicios...">

    <?php if (isset($_SESSION['Sesion'])) { ?>
        <div class="usuario">
            <?php if (!empty($usuario['Foto_Perfil'])): ?>
                <img src="/VSC/proyecto-inner-systems/IMG/perfiles/<?php echo htmlspecialchars($usuario['Foto_Perfil']); ?>" alt="Foto de perfil" class="userIcon">
            <?php else: ?>
                <img src="/VSC/proyecto-inner-systems/IMG/aprendomoUser.jpg" alt="Foto de perfil" class="userIcon">
            <?php endif; ?>

            <div class="userMenu">
                <div class="userMenuBox">
                    <div class="userInfo">
                        <?php if (!empty($usuario['Foto_Perfil'])): ?>
                            <img src="/VSC/proyecto-inner-systems/IMG/perfiles/<?php echo htmlspecialchars($usuario['Foto_Perfil']); ?>" alt="Foto de perfil">
                        <?php else: ?>
                            <img src="/VSC/proyecto-inner-systems/IMG/aprendomoUser.jpg" alt="Foto de perfil">
                        <?php endif; ?>
                        <h2><?php echo htmlspecialchars(($usuario['Nombre'] ?? '') . " " . ($usuario['Apellido'] ?? '')); ?></h2>
                    </div>
                    <hr>
                    <div class="userMenuOptions">
                         <a href="/VSC/proyecto-inner-systems/PHP/perfil.php">
                            <div>
                                <img src="/VSC/proyecto-inner-systems/IMG/MyProfileIcon.png" alt="Mi Perfil Icon">
                                <p>Mi perfil</p>
                                <div class="userSpanContenedor"><span> </span></div>
                            </div>
                         </a>
                         <a href="/VSC/proyecto-inner-systems/PHP/miscursos.php">
                            <div>
                                <img src="/VSC/proyecto-inner-systems/IMG/MisCursosIcon.jpg" alt="Mis Cursos Icon">
                                <p>Mis cursos</p>
                                <div class="userSpanContenedor"><span> </span></div>
                            </div>
                         </a>
                        <a href="/VSC/proyecto-inner-systems/PHP conexiones/logout.php">
                            <div>
                                <img src="/VSC/proyecto-inner-systems/IMG/ExitIcon.jpg" alt="Cerrar Sesión Icon">
                                <p>Cerrar sesión</p>
                                <div class="userSpanContenedor"><span> </span></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>        
    <?php } else { ?>
        <form action="/VSC/proyecto-inner-systems/PHP/login.php" method="POST">
            <button class="loginBtn" name="btnLogin" value="login" type="submit">Iniciar sesión</button>
        </form>

        <form action="/VSC/proyecto-inner-systems/PHP/login.php" method="POST">
            <button class="loginBtn" name="btnRegister" value="register" type="submit">Registrarse</button>
        </form>
    <?php } ?>
</div>

<script>
    const userIcon = document.querySelector('.userIcon');
    const menu = document.querySelector('.userMenu');

    if (userIcon && menu) {
        userIcon.addEventListener('click', () => {
            menu.classList.toggle('open-menu');
        });
    }
</script>