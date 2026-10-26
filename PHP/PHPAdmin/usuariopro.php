<?php
// Creación de sessión en caso de no estar activa.
session_start();

// Conexión a la base de datos
include(__DIR__ . '/../PHP conexiones/conexion.php');

// Variables que almacenan nombre de usuario y contraseña.

$usuario = $_POST['usuario'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

// Condicional para verificar si  la contraseña y el usuario estan vacios.

if (empty($usuario) || empty($contraseña)) {
    include("index.html");
    echo '<h1 class="bad">Por favor completa todos los campos</h1>';
    exit();
}

try {
    // Consulta encargada de obtener el usuario y la contraseña de la base de datos,para luego compararlos con los datos ingresados por el usuario.
    $sql = "SELECT * FROM usuarios WHERE (Correo = :usuario OR Nombre = :usuario) AND Rol != ''";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['usuario' => $usuario]);
    $filas = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y comprobar la contraseña cifrada
    if ($filas && password_verify($contraseña, $filas['Contrasenia'])) {
        
        // Guardamos los datos necesarios en la sesión
        $_SESSION['Sesion'] = $filas['Correo'];
        $_SESSION['Rol'] = $filas['Rol'];
        $_SESSION['usuario'] = $filas['Nombre'];

        // Redirección basada en el rol del usuario.
        if ($filas['Rol'] === 'Administrador') {
            header("location: ../menuadmin.php"); 
            exit();
        } elseif ($filas['Rol'] === 'Docente' || $filas['Rol'] === 'Estudiante') {
            header("location: main.php"); 
            exit();
        } else {
            include("index.html");
            echo '<h1 class="bad">ERROR EN LA AUTENTIFICACION (Rol no válido)</h1>';
        }
    } else {
        include("index.html");
        echo '<h1 class="bad">ERROR EN LA AUTENTIFICACION</h1>';
    }

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
?>