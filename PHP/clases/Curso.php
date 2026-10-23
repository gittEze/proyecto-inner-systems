<?php

class Curso
{
    private $titulo;
    private $descripcion;
    private $tipo;
    private $nivel;
    private $duracion;
    private $precio;
    private $imagen;
    private $idDocente;

    public function __construct($titulo, $descripcion, $tipo, $nivel, $duracion, $precio, $imagen, $idDocente)
    {
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->tipo = $tipo;
        $this->nivel = $nivel;
        $this->duracion = $duracion;
        $this->precio = $precio;
        $this->imagen = $imagen;
        $this->idDocente = $idDocente;
    }

    public function guardar($pdo)
    {
        $sql = "INSERT INTO cursos (Titulo_Curso, Descripcion_Curso, Tipo_Curso, Nivel_Curso, Duracion_Estimada, Precio, Dataso, ID_Docente)
        VALUES (:Titulo_Curso, :Descripcion_Curso, :Tipo_Curso, :Nivel_Curso, :Duracion_Estimada, :Precio, :Dataso, :ID_Docente)";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':Titulo_Curso' => $this->titulo,
            ':Descripcion_Curso' => $this->descripcion,
            ':Tipo_Curso' => $this->tipo,
            ':Nivel_Curso' => $this->nivel,
            ':Duracion_Estimada' => $this->duracion,
            ':Precio' => $this->precio,
            ':Dataso' => $this->imagen,
            ':ID_Docente' => $this->idDocente
        ]);
    }
}
?>