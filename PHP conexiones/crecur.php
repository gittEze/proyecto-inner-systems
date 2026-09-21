<?php

require 'conexion.php';



if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO cursos (Titulo_curso,Descripcion_curso,Tipo_curso, Nivel_Curso, Duracion_estimada, Precio, Dataso) VALUES (:Titulo_curso, :Descripcion_curso, :Tipo_curso, :Nivel_Curso, :Duracion_estimada, :Precio, :Dataso)';

    if(isset($_FILES['Dataso']) && $_FILES['Dataso']['error'] === 0){

        $nombre_imagen= time(). "-" . $_FILES['Dataso']["name"];
        $tmp = $_FILES['Dataso']['tmp_name'];  
        
        $ruta_destino= __DIR__ . "/Imagenes/" . $nombre_imagen;

        move_uploaded_file($tmp, $ruta_destino);
    }    

    $consulta = $pdo->prepare($sql);

    $consulta->bindParam(':Titulo_curso', $_POST['Titulo_curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Descripcion_curso', $_POST['Descripcion_curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Tipo_curso', $_POST['Tipo_curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Nivel_Curso', $_POST['Nivel_Curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Duracion_estimada', $_POST['Duracion_estimada'], PDO::PARAM_INT);

    $consulta->bindParam(':Precio', $_POST['Precio'], PDO::PARAM_INT);

    $consulta->bindParam(':Dataso', $nombre_imagen, PDO::PARAM_STR);


    $consulta->execute();
   
}

header("Location: ../PHP/cursos.php");

?>