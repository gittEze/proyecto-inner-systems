<?php
// Ubicación del archivo que almacena la conexión de la base de datos.
require_once __DIR__ . '/../PHP conexiones/conexion.php';

// Verificar que exista $pdo y sea una instancia válida de PDO
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("No se pudo establecer la conexión con la base de datos.");
}

$token = $_GET['token'] ?? '';
$error = '';
$exito = '';

if (empty($token)) {
    die("Token no válido o ausente.");
}

// Consulta con sentencia preparada PDO (no se requiere real_escape_string)
$query = "SELECT ID_Usuario FROM usuarios WHERE token_recuperacion = :token AND token_expiracion > NOW()";
$stmt = $pdo->prepare($query);
$stmt->execute([':token' => $token]);
$usuario = $stmt->fetch();

if (!$usuario) {
    die("El enlace es inválido o ha expirado. Por favor solicita uno nuevo.");
}

$idUsuario = $usuario['ID_Usuario'];

// Procesar cuando el usuario presione el botón de Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevaClave = $_POST['password'] ?? '';
    $confirmarClave = $_POST['confirmar_password'] ?? '';

    if (empty($nuevaClave) || empty($confirmarClave)) {
        $error = "Por favor completa todos los campos.";
    } elseif ($nuevaClave !== $confirmarClave) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $claveHash = password_hash($nuevaClave, PASSWORD_DEFAULT);

        // Actualizar contraseña y limpiar el token con PDO
        $updateQuery = "UPDATE usuarios SET Contrasenia = :clave, token_recuperacion = NULL, token_expiracion = NULL WHERE ID_Usuario = :id";
        $updateStmt = $pdo->prepare($updateQuery);

        if ($updateStmt->execute([':clave' => $claveHash, ':id' => $idUsuario])) {
            $exito = "¡Contraseña actualizada con éxito! Redirigiendo al inicio de sesión...";
            header("refresh:3;url=login.php");
        } else {
            $error = "Error al actualizar la contraseña.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="../CSS/estilo.css">
</head>
<body>
    <div class="Contenedor_recuperar">
        <div class="recuperar_contenedor">
            <h2>Crear Nueva Contraseña</h2>

            <?php if ($error): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($exito): ?>
                <p style="color: green;"><?= htmlspecialchars($exito) ?></p>
            <?php else: ?>

            <form method="POST">
                <div class="recuperar_grupo">
                    <label for="password">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" class="recuperar_input" required minlength="6">
                </div>

                <div class="recuperar_grupo">
                    <label for="confirmar_contra">Confirmar Contraseña</label>
                    <input type="password" id="confirmar_contra" name="confirmar_password" class="recuperar_input" required minlength="6">
                </div>

                <button type="submit" class="recuperar_btn">Actualizar Contraseña</button>
            </form>

            <?php endif; ?>
        </div>
    </div>
</body>
</html>