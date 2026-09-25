<?php
session_start();
require 'conexion.php';


// Consulta que permite insertar datos del formulario de "crecur2 en la base de datos.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = 'INSERT INTO cursos (ID_Docente, Titulo_Curso, Descripcion_Curso, Tipo_Curso, Nivel_Curso, Duracion_Estimada, Precio, Dataso) VALUES (:ID_Docente, :Titulo_Curso, :Descripcion_Curso, :Tipo_Curso, :Nivel_Curso, :Duracion_Estimada, :Precio, :Dataso)';

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

    $consulta->bindParam(':ID_Docente', $_SESSION['ID_Usuario'], PDO::PARAM_INT);

    $consulta->bindParam(':Titulo_Curso', $_POST['Titulo_Curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Descripcion_Curso', $_POST['Descripcion_Curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Tipo_Curso', $_POST['Tipo_Curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Nivel_Curso', $_POST['Nivel_Curso'], PDO::PARAM_STR);

    $consulta->bindParam(':Duracion_Estimada', $_POST['Duracion_Estimada'], PDO::PARAM_INT);

    $consulta->bindParam(':Precio', $_POST['Precio'], PDO::PARAM_INT);

    $consulta->bindParam(':Dataso', $nombre_imagen, PDO::PARAM_STR);


    $consulta->execute();

    
   
}

// Luego de terminar de publicar el curso el usuario sera llevado a los cursos generales para verlo.

header("Location: ../PHP/cursos.php");

?>

