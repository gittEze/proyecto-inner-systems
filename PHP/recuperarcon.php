<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../CSS/estilo.css">

</head>
<body>
    <main>
    <div class="recuperar_Ultracontenedor">
        <div class="recuperar_contenedor">
            <div class="recuperar_header">
                <h1 class="recuperar_titulo">Recuperar Contraseña</h1>
                <p class="recuperar_subtitulo">Te enviaremos un enlace para restablecerla</p>
            </div>

            <div class="recuperar_form_contenedor">

            <!-- Contendor con info para recuperar la contraseña. -->
                <div class="recuperar_info">
                    Ingresá el correo electrónico asociado a tu cuenta y te enviaremos las instrucciones para recuperar tu contraseña.
                </div>
<!-- Formulario encargado de enviar los datos necesarios para que recovery funcione y pueda procesar la solicitud -->
                <form class="recuperar_form" action="../PHP conexiones/recovery.php" method="POST">
                    <div class="recuperar_grupo">
                        <label class="recuperar_label" for="email">Correo electrónico</label>
                        <input 
                            class="recuperar_input"
                            type="email" 
                            id="email" 
                            name="Correo" 
                            placeholder="ejemplo@correo.com" 
                            required
                            autocomplete="Correo"
                        >
                    </div>
                    <!-- Boton para procesar el envio del formulario -->
                    <button type="submit" class="recuperar_btn">
                        Enviar enlace de recuperación
                    </button>
                </form>
                <!-- Enlace para volver al login -->
                <div class="recuperar_links">
                    <a class="recuperar_link" href="login.php">← Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>