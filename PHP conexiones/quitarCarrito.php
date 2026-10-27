<?php
session_start();

if (!isset($_SESSION['Sesion'])) {
    header('Location: ../PHP/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['ID_Curso'])) {
    header('Location: ../PHP/carrito.php');
    exit();
}

$idCurso = intval($_POST['ID_Curso']);

if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array_values(
        array_filter($_SESSION['carrito'], function ($id) use ($idCurso) {
            return (int)$id !== $idCurso;
        })
    );
}

header('Location: ../PHP/carrito.php');
exit();
?>