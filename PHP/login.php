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

                <?php $envio = $_POST["btnRegister"] ?? $_POST["btnLogin"]; ?> 
                 

                <?php if ($envio == "login") { ?>

                    <title>Iniciar sesión</title>
                    <h2>Iniciar Sesión</h2>

                    <form id="login" action="" method="post">
                        <label for="username">Nombre de usuario:</label>
                        <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required>

                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>

                        <a id="recuperarContrasenia" href="">Recuperar contraseña</a>

                        <div class="botones">
                            <button type="submit" id="btnIniciarSesion">Iniciar Sesion</button>
                            <a href="main.php" class="btnDato">Cancelar</a>
                        </div>
                        
                    </form>

                <?php } elseif ($envio == "register") { ?>

                    <title>Registro</title>
                    <h2>Formulario de Registro</h2>

                    <form id="contenedorRegister" action="" method="post">
                        <label for="Nm">Nombre</label>
                        <input type="text" id="Nm" name="Nombre" placeholder="Ej: Juan">

                        <label for="Ap">Apellido</label>
                        <input type="text" id="Ap" name="Apellido" placeholder="Ej: Zorrilla">

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
                            <option value="docente">Docente</option>
                            <option value="estudiante">Estudiante</option>
                        </select>

                        <label for="Gen">Género</label>
                        <select id="Gen" name="Genero">
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                        </select>

                        <div class="botones">
                            <button type="submit" class="btnDato">Registrarse</button>
                            <button type="reset" class="btnDato">Limpiar formulario</button>
                        </div>
                    </form>

                <?php } ?>

            </section>
        </main>
    </body>
</html>