 <?php
session_start();
require 'conexion.php';

// Aqui el condicional evalua si existelos parametros enviados por post y get de 'Corre' y 'Contrasena'
 if(isset($_POST['Correo']) && isset($_POST['Contrasena'])){
    // La consulta trae el correo y contraseña de la base de datos.
                $sql = "SELECT Correo, Rol FROM usuarios WHERE Correo = :Correo AND Contrasenia = :Contrasena";
                // Se prepara la consulta para mayor seguridad.
                $consulta = $pdo->prepare($sql);

                $consulta->bindParam(':Correo', $_POST['Correo'], PDO::PARAM_STR);
                $consulta->bindParam(':Contrasena', $_POST['Contrasena'], PDO::PARAM_STR);

                $consulta->execute();

                $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
            }
            // Condicional que verifica si la sesión de correo contiene el parametro de 'Correo'.
            if($usuario){
                $_SESSION['Sesion'] = $usuario['Correo'];
                $_SESSION['Rol'] = strtolower($usuario['Rol']);

                header("Location: ../PHP/main.php");
                exit();
             //En caso de que la contraseña sea incorrecta despligue un alert/mensaje que muestra un mensaje 
            } else {
                echo "<script>
                alert('Correo o contraseña incorrectos..');
                window.location.href='../PHP/login.php?pagina=login';  
                </script>";
                //Una vez le das al boton de aceptar del alert, el usuario se ira al login nuevamente.
                exit();
            }


?>
