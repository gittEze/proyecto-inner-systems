<?php

// Condicional para iniciar la sesion en caso no este activa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos
require_once __DIR__ . '/../../PHP conexiones/conexion.php';

$esAdministrador = false;
$usuario = null;

// Isset encargado de verificar la sessión
if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion'];

    $sql = "SELECT Nombre, Apellido, Rol FROM usuarios WHERE Correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['Rol'] === 'Administrador') {
        $esAdministrador = true;
    }
}

// Solo administradores pueden acceder
if (!$esAdministrador) {
    header("Location: ../main.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Usuarios - Panel Administrador</title>
    <link rel="stylesheet" href="../../CSS/estilo.css">
    <style>
        /* Estilos críticos integrados para evitar superposiciones por caché */
        .admin-sidebar {
            position: fixed !important;
            top: 90px !important;
            left: 0 !important;
            width: 260px !important;
            height: calc(100vh - 90px) !important;
            background-color: #ffffff !important;
            border-right: 1px solid #e2e8f0 !important;
            z-index: 200 !important;
            box-shadow: 4px 0 18px rgba(0, 0, 0, 0.06) !important;
            overflow-y: auto !important;
            box-sizing: border-box !important;
            padding: 0 0 20px 0 !important;
        }
        .admin-main-content {
            margin-left: 280px !important; /* Espacio obligatorio para que no se superponga */
            padding: 40px 30px !important;
            box-sizing: border-box !important;
            min-height: 80vh !important;
        }
        @media (max-width: 900px) {
            .admin-sidebar {
                position: relative !important;
                top: 0 !important;
                width: 100% !important;
                height: auto !important;
                box-shadow: none !important;
            }
            .admin-main-content {
                margin-left: 0 !important;
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body class="admin-active">

    <!-- Menú superior -->
    <?php include __DIR__ . '/../menu.php'; ?>

    <!-- Sidebar fijo del administrador -->
    <div class="admin-sidebar">
        <div class="admin-title" style="font-weight:bold; font-size:1.1rem; color:#0d47a1; padding: 22px 16px 18px; text-align:center; border-bottom: 1px solid #e2e8f0; background: linear-gradient(to right, #e3f2fd, #f0f7ff); margin-bottom: 12px;">Panel de Administrador</div>
        <a class="admin-link" href="estadisticas.php" style="display: flex; align-items: center; width: calc(100% - 24px); margin: 0 12px; padding: 14px 18px; text-decoration: none; color: #334155; font-weight: 500; font-size: 15px; border-radius: 10px;">Estadísticas</a>
        <a class="admin-link" href="usuarios.php" style="display: flex; align-items: center; width: calc(100% - 24px); margin: 0 12px; padding: 14px 18px; text-decoration: none; color: #ffffff; font-weight: 500; font-size: 15px; border-radius: 10px; background: #1e88e5;">Gestor de usuarios</a>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main-content">
        <h1 style="color: #0d47a1; font-size: 26px; margin-bottom: 5px;">GESTIÓN DE USUARIOS</h1>
        <p style="color: #64748b; margin-bottom: 20px;">Visualiza, edita los datos o elimina usuarios registrados en el sistema.</p>
<!-- Estructura en formato de tabla para los usuarios -->
        <div style="overflow-x: auto;">
            <table class="tabla-usuarios">
                <thead>
                    <tr>
                        <th style="width: 50px;">Id</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th style="width: 90px; text-align:center;">Editar</th>
                        <th style="width: 90px; text-align:center;">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta para obtener todos los usuarios de la base de datos.
                    $sql = "SELECT * FROM usuarios";
                    $stmt = $pdo->query($sql);
                    $numeroCorrelativo = 1;

                    // while encargado de recorrer los resultados de la consulta y mostrar los datos en la tabla..

                    while ($mostrar = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = $mostrar['id'] ?? $mostrar['ID'] ?? $mostrar['ID_Usuario'] ?? '';
                        $nombre = $mostrar['Nombre'] ?? $mostrar['nombre'] ?? '';
                        $apellido = $mostrar['Apellido'] ?? $mostrar['apellido'] ?? '';
                        $correoUser = $mostrar['Correo'] ?? $mostrar['correo'] ?? '';
                        
                        // Variable encargada de obtene el rol desde la bd.
                        $rol = $mostrar['Rol'] ?? $mostrar['rol'] ?? '';
                        ?>
                        <form action="actualizarUsuario.php" method="POST">
                            <tr>
                                <td><?php echo $numeroCorrelativo; ?></td>
                                <input type="hidden" name="ID_Usuario" value="<?php echo htmlspecialchars($id); ?>">
                                
                                <td><input type="text" class="input-editable" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" readonly required></td>
                                <td><input type="text" class="input-editable" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" readonly required></td>
                                <td><input type="email" class="input-editable" name="correo" value="<?php echo htmlspecialchars($correoUser); ?>" readonly required></td>
                                
                                <!-- Campo de texto que muestra el rol real de la BD -->
                                <td><input type="text" class="input-editable" name="rol" value="<?php echo htmlspecialchars($rol); ?>" readonly required></td>

                                <td style="text-align: center;">
                                    <button type="button" class="btn-accion btn-editar" onclick="habilitarEdicion(this)">Editar</button>
                                    <button type="submit" class="btn-accion btn-guardar" style="display: none;">Guardar</button>
                                </td>
                                
                                <td style="text-align: center;">
                                    <a href="eliminar_usuario.php?id=<?php echo $id; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                </td>
                            </tr>
                        </form>   
                    <?php
                        $numeroCorrelativo++;
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

  <!-- Estructura con Jvascript que permite editar los campos por meddio de un boton -->
   <script>
   function habilitarEdicion(button) {
       const tr = button.closest('tr');
       const inputs = tr.querySelectorAll('.input-editable');
       const btnGuardar = tr.querySelector('.btn-guardar');
       
       inputs.forEach(input => input.removeAttribute('readonly'));
       
       button.style.display = 'none';
       btnGuardar.style.display = 'inline-block';
   }
   </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../footer.php'; ?>

</body>
</html>