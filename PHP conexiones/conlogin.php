<?php

session_start();

require ('conexion.php');
require_once ('../PHP/clases/Usuario.php');

if(isset($_POST['Correo']) && isset($_POST['Contrasenia'])){

    $correo = trim($_POST['Correo']);
    $contrasenia = trim($_POST['Contrasenia']);

    $usuario = new Usuario($correo, $contrasenia);
    $datosUsuario = $usuario->autenticar($pdo);

    if($datosUsuario){

        $_SESSION['Sesion'] = $datosUsuario['Correo'];
        $_SESSION['ID_Usuario'] = $datosUsuario['ID_Usuario'];
        $_SESSION['Rol'] = strtolower($datosUsuario['Rol']);

        header("Location: ../PHP/main.php");
        exit();

    } else {

        echo "<script>
        alert('Correo o contraseña incorrectos.');
        window.location.href='../PHP/login.php?pagina=login';
        </script>";

        exit();
    }
}
