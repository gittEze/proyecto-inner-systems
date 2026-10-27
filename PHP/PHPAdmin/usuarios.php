<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../PHP conexiones/conexion.php';

$esAdministrador = false;
$usuario = null;

if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion'];
    $sql = "SELECT Nombre, Apellido, Rol FROM usuarios WHERE Correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && strtolower(trim($usuario['Rol'])) === 'administrador') {
        $esAdministrador = true;
    }
}

if (!$esAdministrador) {
    header("Location: ../main.php");
    exit;
}

$mensaje = '';
$tipoMsg = '';
if (isset($_GET['actualizado']) && $_GET['actualizado'] === 'success') {
    $mensaje = 'Usuario actualizado correctamente.';
    $tipoMsg = 'ok';
} elseif (isset($_GET['eliminado']) && $_GET['eliminado'] === 'success') {
    $mensaje = 'Usuario eliminado correctamente.';
    $tipoMsg = 'ok';
} elseif (isset($_GET['error'])) {
    $errores = [
        'self'   => 'No puedes eliminarte a ti mismo.',
        'correo' => 'El correo ya está en uso por otro usuario.',
        'datos'  => 'Datos incompletos o rol no válido.',
        'db'     => 'Error en la base de datos.',
        'id'     => 'ID de usuario no válido.',
    ];
    $mensaje = $errores[$_GET['error']] ?? 'Ocurrió un error.';
    $tipoMsg = 'error';
}

$stmt = $pdo->query("SELECT ID_Usuario, Nombre, Apellido, Correo, Rol FROM usuarios ORDER BY ID_Usuario ASC");
$listaUsuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rolesValidos = ['Administrador', 'Docente', 'Estudiante'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Usuarios - Panel Administrador</title>
    <link rel="stylesheet" href="../../CSS/estilo.css">
    <link rel="icon" href="../../IMG/Icono.ico">
    <style>
        .admin-msg { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-weight: 500; }
        .admin-msg.ok { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .admin-msg.error { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .tabla-usuarios { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.05); border-radius: 12px; overflow: hidden; }
        .tabla-usuarios th { background: #0d47a1; color: #fff; padding: 12px 14px; text-align: left; font-size: .9rem; }
        .tabla-usuarios td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .tabla-usuarios tr:hover td { background: #f5f9ff; }
        .input-editable, .select-rol { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: .9rem; background: #f8fafc; box-sizing: border-box; }
        .input-editable:not([readonly]), .select-rol:not([disabled]) { background: #fff; border-color: #1e88e5; outline: none; }
        .btn-accion { display: inline-block; padding: 7px 14px; border-radius: 6px; font-size: .85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; margin: 2px; }
        .btn-editar { background: #e3f2fd; color: #1565c0; }
        .btn-editar:hover { background: #bbdefb; }
        .btn-guardar { background: #2e7d32; color: #fff; }
        .btn-guardar:hover { background: #1b5e20; }
        .btn-cancelar-edit { background: #f1f5f9; color: #475569; }
        .btn-eliminar { background: #ffebee; color: #c62828; }
        .btn-eliminar:hover { background: #ffcdd2; }
    </style>
</head>
<body class="admin-active">

<?php include __DIR__ . '/../menu.php'; ?>

<div class="admin-sidebar">
    <div class="admin-title">Panel de Administrador</div>
    <a class="admin-link" href="menuAdmin.php">Inicio panel</a>
    <a class="admin-link" href="estadisticas.php">Estadísticas</a>
    <a class="admin-link active" href="usuarios.php">Gestor de usuarios</a>
</div>

<div class="admin-main-content">
    <h1 style="color:#0d47a1; font-size:26px; margin-bottom:5px;">Gestión de usuarios</h1>
    <p style="color:#64748b; margin-bottom:20px;">Visualiza, edita o elimina usuarios registrados en el sistema.</p>

    <?php if ($mensaje): ?>
        <div class="admin-msg <?php echo $tipoMsg; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <div style="overflow-x:auto;">
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th style="width:140px;">Rol</th>
                    <th style="width:160px; text-align:center;">Acciones</th>
                    <th style="width:90px; text-align:center;">Eliminar</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($listaUsuarios as $u):
                $id = (int)$u['ID_Usuario'];
                $nombre = $u['Nombre'] ?? '';
                $apellido = $u['Apellido'] ?? '';
                $correoUser = $u['Correo'] ?? '';
                $rol = $u['Rol'] ?? '';
            ?>
                <tr data-id="<?php echo $id; ?>">
                    <td><?php echo $id; ?></td>
                    <td>
                        <form action="actualizarUsuario.php" method="POST" class="form-usuario" id="form-<?php echo $id; ?>">
                            <input type="hidden" name="ID_Usuario" value="<?php echo $id; ?>">
                            <input type="text" class="input-editable" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" readonly required>
                    </td>
                    <td>
                            <input type="text" class="input-editable" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" readonly required>
                    </td>
                    <td>
                            <input type="email" class="input-editable" name="correo" value="<?php echo htmlspecialchars($correoUser); ?>" readonly required>
                    </td>
                    <td>
                            <select class="select-rol" name="rol" disabled>
                                <?php foreach ($rolesValidos as $r): ?>
                                    <option value="<?php echo $r; ?>" <?php echo (strcasecmp($rol, $r) === 0) ? 'selected' : ''; ?>>
                                        <?php echo $r; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    </td>
                    <td style="text-align:center;">
                            <button type="button" class="btn-accion btn-editar" onclick="habilitarEdicion(<?php echo $id; ?>)">Editar</button>
                            <button type="submit" class="btn-accion btn-guardar" style="display:none;">Guardar</button>
                            <button type="button" class="btn-accion btn-cancelar-edit" style="display:none;" onclick="cancelarEdicion(<?php echo $id; ?>)">Cancelar</button>
                        </form>
                    </td>
                    <td style="text-align:center;">
                        <a href="eliminarUsuario.php?id=<?php echo $id; ?>"
                           class="btn-accion btn-eliminar"
                           onclick="return confirm('¿Eliminar al usuario <?php echo htmlspecialchars(addslashes($nombre . ' ' . $apellido)); ?>? Esta acción no se puede deshacer.');">
                           Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const valoresOriginales = {};

function habilitarEdicion(id) {
    const tr = document.querySelector('tr[data-id="' + id + '"]');
    const inputs = tr.querySelectorAll('.input-editable');
    const select = tr.querySelector('.select-rol');
    const btnEditar = tr.querySelector('.btn-editar');
    const btnGuardar = tr.querySelector('.btn-guardar');
    const btnCancelar = tr.querySelector('.btn-cancelar-edit');

    valoresOriginales[id] = {
        nombre: inputs[0].value,
        apellido: inputs[1].value,
        correo: inputs[2].value,
        rol: select.value
    };

    inputs.forEach(i => i.removeAttribute('readonly'));
    select.removeAttribute('disabled');

    btnEditar.style.display = 'none';
    btnGuardar.style.display = 'inline-block';
    btnCancelar.style.display = 'inline-block';
}

function cancelarEdicion(id) {
    const tr = document.querySelector('tr[data-id="' + id + '"]');
    const inputs = tr.querySelectorAll('.input-editable');
    const select = tr.querySelector('.select-rol');
    const btnEditar = tr.querySelector('.btn-editar');
    const btnGuardar = tr.querySelector('.btn-guardar');
    const btnCancelar = tr.querySelector('.btn-cancelar-edit');
    const orig = valoresOriginales[id];

    if (orig) {
        inputs[0].value = orig.nombre;
        inputs[1].value = orig.apellido;
        inputs[2].value = orig.correo;
        select.value = orig.rol;
    }

    inputs.forEach(i => i.setAttribute('readonly', true));
    select.setAttribute('disabled', true);

    btnEditar.style.display = 'inline-block';
    btnGuardar.style.display = 'none';
    btnCancelar.style.display = 'none';
}

document.querySelectorAll('.form-usuario').forEach(form => {
    form.addEventListener('submit', function () {
        const select = this.querySelector('.select-rol');
        if (select) select.removeAttribute('disabled');
    });
});
</script>

<?php include __DIR__ . '/../footer.php'; ?>

</body>
</html>