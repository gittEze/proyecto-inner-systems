<?php

class Usuario
{
    private $correo;
    private $contrasenia;

    public function __construct($correo, $contrasenia)
    {
        $this->correo = $correo;
        $this->contrasenia = $contrasenia;
    }

    public function autenticar($pdo)
    {
        $sql = "SELECT ID_Usuario, Correo, Contrasenia, Rol FROM usuarios WHERE Correo = :Correo";

        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':Correo' => $this->correo
            ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($this->contrasenia, $usuario['Contrasenia'])) {
            return $usuario;
        }
        
        return false;
    }
}
?>