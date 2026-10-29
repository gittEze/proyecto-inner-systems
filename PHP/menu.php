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

$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminPanel = (strpos($scriptPath, '/PHPAdmin/') !== false);

$phpBase   = $isAdminPanel ? '../' : '';
$imgBase   = $isAdminPanel ? '../../IMG/' : '../IMG/';
$connBase  = $isAdminPanel ? '../../PHP conexiones/' : '../PHP conexiones/';
$adminBase = $isAdminPanel ? '' : 'PHPAdmin/';

$tieneSesion = isset($_SESSION['Sesion']);
$esAdmin = $tieneSesion && $usuario && strtolower(trim($usuario['Rol'] ?? '')) === 'administrador';
$fotoPerfil = (!empty($usuario['Foto_Perfil']))
    ? $imgBase . 'perfiles/' . htmlspecialchars($usuario['Foto_Perfil'])
    : $imgBase . 'aprendomoUser.jpg';
$nombreCompleto = htmlspecialchars(trim(($usuario['Nombre'] ?? '') . ' ' . ($usuario['Apellido'] ?? '')));
?>

<div class="contenedorMain">
    <a class="logo" id="logoMain" href="<?php echo $phpBase; ?>main.php">
        <img id="logoMainImg" src="<?php echo $imgBase; ?>Logo.png" alt="Logo de Aprendomo">
    </a>

<?php if ($esAdmin) { ?>
    <a class="Serv" href="<?php echo $phpBase; ?>main.php">Inicio</a>
    <a class="Serv" href="<?php echo $phpBase; ?>cursos.php">Cursos</a>
    <a class="Serv" href="<?php echo $phpBase; ?>proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="<?php echo $phpBase; ?>carrito.php">
    <a class="Serv" href="<?php echo $adminBase; ?>menuAdmin.php">Panel de Administración</a>

<?php } elseif ($tieneSesion) { ?>
    <a class="Serv" href="<?php echo $phpBase; ?>main.php">Inicio</a>
    <a class="Serv" href="<?php echo $phpBase; ?>cursos.php">Cursos</a>
    <a class="Serv" href="<?php echo $phpBase; ?>proyectos.php">Proyectos educativos</a>
    <a class="Serv" href="<?php echo $phpBase; ?>carrito.php">
        Carrito
        <?php
        $cantCarrito = (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) ? count($_SESSION['carrito']) : 0;
        if ($cantCarrito > 0) {
            echo ' (' . $cantCarrito . ')';
        }
        ?>
    </a>

<?php } else { ?>
    <a class="Serv" href="<?php echo $phpBase; ?>main.php">Inicio</a>
    <a class="Serv" href="<?php echo $phpBase; ?>cursos.php">Cursos</a>
    <a class="Serv" href="<?php echo $phpBase; ?>proyectos.php">Proyectos educativos</a>
<?php } ?>

    <form id="formBuscador" action="<?php echo $phpBase; ?>cursos.php" method="GET">
        <input id="buscador" name="buscar" type="search" placeholder="Buscar cursos..." value="<?php echo htmlspecialchars($_GET['buscar'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"aria-label="Buscar cursos">
    </form>

<?php if ($tieneSesion) { ?>
    <div class="usuario" id="menuUsuario">
        <button type="button" class="userIconBtn" id="btnMenuUsuario" aria-expanded="false" aria-controls="panelMenuUsuario" title="Menú de usuario">
            <img src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil" class="userIcon" width="55" height="55">
        </button>

        <div class="userMenu" id="panelMenuUsuario" hidden>
            <div class="userMenuBox">
                <div class="userInfo">
                    <img src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil">
                    <h2><?php echo $nombreCompleto !== '' ? $nombreCompleto : 'Usuario'; ?></h2>
                </div>
                <hr>
                <div class="userMenuOptions">
                    <a href="<?php echo $phpBase; ?>perfil.php">
                        <div>
                            <img src="<?php echo $imgBase; ?>MyProfileIcon.png" alt="">
                            <p>Mi perfil</p>
                        </div>
                    </a>
                    <a href="<?php echo $phpBase; ?>miscursos.php">
                        <div>
                            <img src="<?php echo $imgBase; ?>MisCursosIcon.jpg" alt="">
                            <p>Mis cursos</p>
                        </div>
                    </a>
                    <a href="<?php echo $connBase; ?>logout.php" class="link-cerrar-sesion">
                        <div>
                            <img src="<?php echo $imgBase; ?>ExitIcon.jpg" alt="">
                            <p>Cerrar sesión</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php } else { ?>
    <form action="<?php echo $phpBase; ?>login.php" method="POST">
        <button class="loginBtn" name="btnLogin" value="login" type="submit">Iniciar sesión</button>
    </form>
    <form action="<?php echo $phpBase; ?>login.php" method="POST">
        <button class="loginBtn" name="btnRegister" value="register" type="submit">Registrarse</button>
    </form>
<?php } ?>
</div>

<?php if ($tieneSesion) { ?>
<script>
(function () {
    var btn = document.getElementById('btnMenuUsuario');
    var panel = document.getElementById('panelMenuUsuario');
    var wrap = document.getElementById('menuUsuario');
    if (!btn || !panel) return;

    function openMenu() {
        panel.hidden = false;
        panel.classList.add('open-menu');
        btn.setAttribute('aria-expanded', 'true');
    }
    function closeMenu() {
        panel.hidden = true;
        panel.classList.remove('open-menu');
        btn.setAttribute('aria-expanded', 'false');
    }
    function toggleMenu(e) {
        e.preventDefault();
        e.stopPropagation();
        if (panel.hidden) openMenu();
        else closeMenu();
    }

    btn.addEventListener('click', toggleMenu);

    document.addEventListener('click', function (e) {
        if (wrap && !wrap.contains(e.target)) closeMenu();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
})();
</script>
<?php } ?>