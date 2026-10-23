<?php
session_start();
require_once __DIR__ . '/../../PHP_conexiones/conexion.php';

// Verificar si el usuario es administrador (igual que en tus otros archivos)
// ... (puedes agregar tu validación de seguridad aquí)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los datos enviados por el formulario de la tabla
    $id = $_POST['ID_Usuario'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if (!empty($id)) {
        try {
            // Consulta SQL para actualizar los datos del usuario en la base de datos
            $sql = "UPDATE usuarios SET Nombre = :nombre, Apellido = :apellido, Correo = :correo, Rol = :rol WHERE ID_Usuario = :ID_Usuario";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':correo' => $correo,
                ':rol' => $rol,
                ':ID_Usuario' => $id
            ]);

            // Redirección exitosa de regreso al gestor de usuarios
            header("Location: usuarios.php?actualizado=success");
            exit();

        } catch (PDOException $e) {
            echo "Error al actualizar: " . $e->getMessage();
        }
    } else {
        echo "ID de usuario no válido.";
    }
}
else {
    // Si intentan entrar por URL directamente sin enviar el formulario
    header("Location: usuarios.php");
    exit();
}
?>