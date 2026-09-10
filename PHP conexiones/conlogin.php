 <?php
session_start();
require 'conexion.php';


 if(isset($_POST['Correo']) && isset($_POST['Contrasena'])){
                $sql = "SELECT Correo FROM usuario WHERE Correo = :Correo AND Contraseña = :Contrasena";

                $consulta = $pdo->prepare($sql);

                $consulta->bindParam(':Correo', $_POST['Correo'], PDO::PARAM_STR);
                $consulta->bindParam(':Contrasena', $_POST['Contrasena'], PDO::PARAM_STR);

                $consulta->execute();

                $Correo = $consulta->fetch(PDO::FETCH_ASSOC);
            }
                if($Correo){
                    $_SESSION['Sesion'] = $Correo['Correo'];
                    header("Location: ../PHP/main.php");
                    exit();

                } else {
                    echo "<script>
                    alert('Correo o contraseña incorrectos..');
                    window.location.href='../PHP/login.php?pagina=login';
                    </script>";
                    exit();
                }

?>

