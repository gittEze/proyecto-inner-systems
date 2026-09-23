<?php

require 'conexion.php';


// Consulta que permite insertar datos del formulario de "crecur2 en la base de datos.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO cursos (Titulo_curso,Descripcion_curso,Tipo_curso, Nivel_Curso, Duracion_estimada, Precio, Dataso) VALUES (:Titulo_curso, :Descripcion_curso, :Tipo_curso, :Nivel_Curso, :Duracion_estimada, :Precio, :Dataso)';

    if(isset($_FILES['Dataso']) && $_FILES['Dataso']['error'] === 0){

        // Estructura que permite guardar los archivos de tipo imagen de manera temporal.
        $nombre_imagen= time(). "-" . $_FILES['Dataso']["name"];
        $tmp = $_FILES['Dataso']['tmp_name'];  
        // Compara la ubiación actual por medio del DIR y le adjunto la ruta de la Carpeta que guarda las imágenes traidas de $nombre_imagen.
        $ruta_destino= __DIR__ . "/Imagenes/" . $nombre_imagen;

        move_uploaded_file($tmp, $ruta_destino);
    }    
    // Envia los datos a la base de datos.
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

// Luego de terminar de publicar el curso el usuario sera llevado a los cursos generales para verlo.

header("Location: ../PHP/cursos.php");

?>