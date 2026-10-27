<?php
// Creación de sesión en caso de no estar activa.
session_start();

// Conexión a la base de datos (ruta corregida)
require_once __DIR__ . '/../../PHP conexiones/conexion.php';

// Variables que almacenan nombre de usuario y contraseña.
$usuario     = $_POST['usuario'] ?? '';
$contraseña  = $_POST['contraseña'] ?? '';

// Condicional para verificar si la contraseña y el usuario están vacíos.
if (empty($usuario) || empty($contraseña)) {
    echo '<h1 class="bad">Por favor completa todos los campos</h1>';
    exit();
}

try {
    // Consulta encargada de obtener el usuario
    $sql = "SELECT * FROM usuarios WHERE (Correo = :usuario OR Nombre = :usuario) AND Rol != ''";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['usuario' => $usuario]);
    $filas = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y comprobar la contraseña cifrada
    if ($filas && password_verify($contraseña, $filas['Contrasenia'])) {
        
        // Guardamos los datos necesarios en la sesión
        $_SESSION['Sesion']   = $filas['Correo'];
        $_SESSION['Rol']      = $filas['Rol'];
        $_SESSION['usuario']  = $filas['Nombre'];
        $_SESSION['ID_Usuario'] = $filas['ID_Usuario'];

        // Redirección basada en el rol del usuario (normalizado a minúsculas)
        $rol = strtolower(trim($filas['Rol']));

        if ($rol === 'administrador') {
            header("Location: menuadmin.php"); 
            exit();
        } elseif ($rol === 'docente' || $rol === 'estudiante') {
            header("Location: ../main.php"); 
            exit();
        } else {
            echo '<h1 class="bad">ERROR EN LA AUTENTIFICACION (Rol no válido)</h1>';
        }
    } else {
        echo '<h1 class="bad">ERROR EN LA AUTENTIFICACION</h1>';
    }

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>