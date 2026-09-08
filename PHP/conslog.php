<?php

require 'conexion.php';


//2. INSERTAR TUPLA
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO usuarios (ci, nombre, apellido) VALUES (:idDo, :nombre, :apellido)';
    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':ci', $_POST['ci'], PDO::PARAM_INT);
    $consulta->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
    $consulta->bindParam(':apellido', $_POST['apellido'], PDO::PARAM_STR);

    $consulta->execute();
}



?>