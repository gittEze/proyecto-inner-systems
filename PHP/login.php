<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="../CSS/animaciones.css">
        <link rel="stylesheet" href="../CSS/estilo.css">
        <link rel="icon" href="../IMG/Icono.ico">
    </head>

    <body class="bodyLogin">
        <main>
            <section class="contenedorLogin">

                <div class="logo" id="logoLogin">
                    <img src="../IMG/LogoBlanco.png" alt="Logo de Aprendomo">
                </div>

                <a id="volver" href="main.php">Volver</a>


                <!-- Comprobante de envio de los datos por los meotods post y get -->
                <?php $envio = $_POST['btnRegister'] ?? $_POST['btnLogin'] ?? $_GET['pagina'] ?? 'login'; ?>

                <?php if ($envio == "login") { ?>

                    <title>Iniciar sesión - Aprendomo</title>
                <!-- Condicional que evalua si existe el parámetro exito en el método GET y si concuerda con lo dado en "conslogin" -->
                <?php  if (isset($_GET['exito']) && $_GET['exito'] == 1) { ?>
                <!-- Si se logra el coommprbante mmuestra un mensaje de que se logro el registro -->
                    <div class="logrado">  
                        Registro exitoso. Por favor, inicie sesión.
                    </div>
                <?php } ?>

                    <h2>Iniciar Sesión</h2>

                    <form id="login" action="../PHP conexiones/conlogin.php" method="post">

                        <label for="Correo">Correo electrónico:</label>
                        <input type="email" id="Correo" name="Correo" placeholder="Ingrese su correo">

                        <label for="Contrasena">Contraseña:</label>
                        <input type="password" id="Contrasena" name="Contrasena" placeholder="Ingrese su contraseña">

                        <a id="recuperarContrasenia" href="">Recuperar contraseña.</a>

                        <!-- Link que aparece en el apartado del formulario de login que envia al usuario a registrase si aun no lo ha hecho. -->
                        <p class="regisAqui">¿No tienes cuenta aún?
                            <a href="login.php?pagina=register" class="linkRegistro">
                                Regístrate aquí.
                            </a>
                        </p>                    
                        
                        <div class="botones">
                            <button type="submit" id="btnIniciarSesion">Iniciar Sesion</button>
                        </div>
                        
                    </form>


                <!-- En caso de que el parametro recibido sea 'register', se mostrara el formulario -->

                <?php } elseif ($envio == "register") { ?>

                    <title>Registro - Aprendomo</title>
                    <h2>Formulario de Registro</h2>

                    <form id="contenedorRegister" action="../PHP conexiones/consreg.php" method="post">
                        <label for="Nm">Nombre</label>
                        <input type="text" id="Nm" name="Nombre" placeholder="Ej: Juan">

                        <label for="Ap">Apellido</label>
                        <input type="text" id="Ap" name="Apellido" placeholder="Ej: Zorrilla">
                        
                        <label for="Cedula">Cédula</label>
                        <input type="text" id="Cedula" name="Cedula" placeholder="Ej: 1234567890">

                        <label for="Fecha">Fecha de nacimiento</label>
                        <input type="date" id="Fecha" name="Fecha_De_Nacimiento" placeholder="Ej: 01/01/2000" required>

                        <label for="Correo">Correo electrónico</label>
                        <input type="email" id="Correo" name="Correo" placeholder="ejemplo@correo.com" required>

                        <label for="Contrasena">Contraseña</label>
                        <input type="password" id="Contrasena" name="Contrasena" placeholder="Ingrese su contraseña" required>

                        <label for="Telefono">Teléfono</label>
                        <input type="tel" id="Telefono" name="Telefono" placeholder="099123456">

                        <label for="rol">Ocupación</label>
                        <select id="rol" name="Rol">
                            <option value="Docente">Docente</option>
                            <option value="Estudiante">Estudiante</option>
                        </select>

                        <label for="Gen">Género</label>
                        <select id="Gen" name="Genero">
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>

                        <div class="botones">
                            <a href="login.php" class="claseo"> 
                            <button type="submit" class="btnDato" >Registrarse</button>
                            <a>
                            <button type="reset" class="btnDato">Limpiar formulario</button>
                            </a>
                        </div>
                    </form>

                <?php } ?>

            </section>
        </main>
    </body>
</html>