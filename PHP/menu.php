<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solución al error de ruta usando __DIR__ para un acceso absoluto y seguro
require_once __DIR__ . '/../PHP conexiones/conexion.php';  

// Inicializar la variable $usuario por seguridad
$usuario = null;

// Condicional para evaluar el inicio de la sesión
if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion']; 
    // Correo del usuario actual
    $sql = "SELECT Nombre, Apellido, Rol, Foto_Perfil FROM usuarios WHERE Correo = :correo";
    $stmt = $pdo->prepare($sql);

    // Se ejecuta la consulta pasando el correo del usuario
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Variable con los datos del usuario
}
?>

<div class="contenedorMain">
    <a class="logo" id="logoMain" href="../PHP/main.php">
        <img id="logoMainImg" src="../IMG/Logo.png" alt="Logo de Aprendomo">
    </a>
     
     <!-- Enlaces según el rol -->
<?php if (
    isset($_SESSION['Sesion']) &&
    isset($usuario['Rol']) &&
    $usuario['Rol'] === 'Administrador'
) { ?>

    <!-- Administrador -->
    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
    <a class="Serv" href="proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="mentorías.php">Mentorías</a>
    <a class="Serv" href="PHPAdmin/menuadmin.php">
        Panel de Administración
    </a>

<?php } elseif (isset($_SESSION['Sesion']) && $usuario) { ?>

    <!-- Usuario normal -->
    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
    <a class="Serv" href="proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="mentorías.php">Mentorías</a>
    <a class="Serv" href="carrito.php">
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
    <a class="Serv" href="main.php">Inicio</a>
    <a class="Serv" href="cursos.php">Cursos</a>
    <a class="Serv" href="proyectos.php">Proyectos educativos</a>

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
                        <h2><?php echo htmlspecialchars(($usuario['Nombre'] ?? '') . " " . ($usuario['Apellido'] ?? '')); ?></h2>
                    </div>
                    <hr>
                    <div class="userMenuOptions">
                        <!-- Mi Perfil -->
                         <a href="../PHP/perfil.php">
                            <div>
                                <img src="../IMG/MyProfileIcon.png" alt="Mi Perfil Icon">
                                <p>Mi perfil</p>
                                <div class="userSpanContenedor">
                                    <span> </span>
                                </div>
                            </div>
                         </a>
                        <!-- Mis Cursos -->
                         <a href="../PHP/miscursos.php">
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
        <form action="../PHP/login.php" method="POST">
            <button class="loginBtn" name="btnLogin" value="login" type="submit">
                Iniciar sesión
            </button>
        </form>

        <form action="../PHP/login.php" method="POST">
            <button class="loginBtn" name="btnRegister" value="register" type="submit">
                Registrarse
            </button>
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