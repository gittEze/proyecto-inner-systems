<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulario de Registro</title>
        
        <link rel="stylesheet" href="../CSS/animaciones.css">
        <link rel="stylesheet" href="../CSS/estilo.css">
        <link rel="icon" href="../IMG/Icono.ico">
    </head>
    
    <body class="bodyLogin">
        <main>
            <section class="contenedorLogin">

                <div class="logo" id="logoLogin"><img src="../IMG/LogoBlanco.png"></div>

                <a  id="volver" href="../main.php">Volver</a> <h2>Formulario de Registro</h2> 

                <?php
                $envio = $_GET["pagina"];
                ?>


                <?php if ($envio == "login"){

                ?>    
                <form id="login" action="" method="post">
                    <label for="username">Nombre de usuario:</label>
                    <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                    <a id="recuperarContrasenia" href="">Recuperar contraseña</a>

                    <button type="submit" id="btnIniciarSesion">Iniciar Sesion</button>
                </form>

                <?php
                }
                ?>

                <?php if ($envio == "register"){

                ?>    
                <form id="contenedorRegister" action="" method="post">
                    
                    <label for="Nm">Nombre</label>
                    <input type="text" id="Nm" name="Nombre" placeholder="Ej: Juan">

                    <label for="Ap">Apellido</label>
                    <input type="text" id="Ap" name="Apellido" placeholder="Ej: Zorrila">

                    <label for="Usuario">Nombre de Usuario</label>
                    <input type="text" id="Usuario" name="Usuario" placeholder="Ingrese su usuario" required>
                    
                    <label for="Correo">Correo electrónico</label>
                    <input type="email" id="Correo" name="Correo" placeholder="ejemplo@correo.com" required>

                    <label for="Contrasena">Contraseña</label>
                    <input type="password" id="Contrasena" name="Contrasena" placeholder="Ingrese su contraseña" required>

                    <label for="Telefono">Teléfono</label>
                    <input type="tel" id="Telefono" name="Telefono" placeholder="099123456">

                    <label for="Ocupacion">Ocupación</label>
                    <select id="Ocupacion" name="Ocupacion">
                        <option value="opcion2"> Docente </option>
                        <option value="opcion3"> Estudiante</option>
                    </select>

                    <label for="Gen">Genero</label>
                        <select id="Gen" name="Genero">
                        <option value="opcion2"> Masculino </option>
                        <option value="opcion3"> Femenino</option>
                    </select>

                    <button type="submit" class="btndato">Registrarse</button>

                </form> 

                <?php
                }
                ?>

            </section>
        </main>
    </body>
</html>

