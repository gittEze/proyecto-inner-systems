<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manual de Usuario - Aprendomo</title>

        <link rel="stylesheet" href="../CSS/animaciones.css">
        <link rel="stylesheet" href="../CSS/estilo.css">
        <link rel="icon" href="../IMG/Icono.ico">
    </head>

    <body>
        <?php include 'menu.php'; ?>

        <main class="manualUsuario">

            <section>
                <h2>Introducción</h2>
                <p>
                    Aprendomo es una plataforma web destinada a la creación, gestión,
                    publicación y acceso a cursos virtuales. El sistema permite que los
                    usuarios consulten contenidos educativos y que los usuarios con
                    permisos de creación puedan desarrollar y publicar sus propios cursos.
                </p>
                <p>
                    Este manual explica las principales funciones disponibles para los
                    usuarios y sirve como guía básica para utilizar la plataforma.
                </p>
            </section>

            <section>
                <h2>Acceso a la plataforma</h2>
                <div class="paso"><strong>Paso 1:</strong> Abrir el navegador web.</div>
                <div class="paso"><strong>Paso 2:</strong> Ingresar a la dirección donde se encuentra publicado Aprendomo.</div>
                <div class="paso"><strong>Paso 3:</strong> Desde la página principal, utilizar el menú de navegación para acceder a las diferentes secciones.</div>
            </section>

            <section>
                <h2>Registro de usuario</h2>
                <p>
                    Para crear una cuenta en Aprendomo, se debe ingresar a la opción
                    <strong>Registrarse</strong> y completar los datos solicitados por el sistema.
                </p>
                <ol>
                    <li>Ingresar el <strong>Nombre</strong> del usuario.</li>
                    <li>Ingresar el <strong>Apellido</strong> del usuario.</li>
                    <li>Ingresar la <strong>Cédula</strong> de identidad.</li>
                    <li>Seleccionar la <strong>Fecha de nacimiento</strong>.</li>
                    <li>Ingresar un <strong>Correo electrónico</strong> válido.</li>
                    <li>Crear una <strong>Contraseña</strong> para la cuenta.</li>
                    <li>Ingresar un <strong>Teléfono</strong> de contacto.</li>
                    <li>Seleccionar la <strong>Ocupación</strong> correspondiente.</li>
                    <li>Seleccionar el <strong>Género</strong>.</li>
                    <li>Confirmar el registro mediante el botón correspondiente.</li>
                    <li>Ingresar posteriormente utilizando las credenciales creadas.</li>
                </ol>

                <div class="nota">
                    La contraseña debe tener al menos 8 caracteres. Los datos ingresados deben ser
                    correctos y corresponder al usuario que está realizando el registro.
                </div>
            </section>

            <section>
                <h2>Inicio de sesión</h2>
                <ol>
                    <li>Seleccionar la opción <strong>Iniciar sesión</strong>.</li>
                    <li>Ingresar las credenciales correspondientes.</li>
                    <li>Confirmar el acceso.</li>
                </ol>
                <div class="nota">
                    Por seguridad, el sistema contempla el bloqueo temporal de una cuenta
                    después de 5 intentos fallidos consecutivos.
                </div>
            </section>

            <section>
                <h2>Recuperación y actualización de contraseña</h2>
                <p>
                    Aprendomo contempla mecanismos para recuperar y actualizar las
                    contraseñas de los usuarios.
                </p>
                <ol>
                    <li>Acceder a la opción correspondiente a recuperación o modificación de contraseña.</li>
                    <li>Completar los datos solicitados.</li>
                    <li>Seguir las instrucciones recibidas por el medio de recuperación.</li>
                    <li>Establecer una nueva contraseña válida.</li>
                </ol>
                <div class="nota">
                    El sistema no permite reutilizar ninguna de las últimas 3 contraseñas
                    utilizadas por el usuario.
                </div>
            </section>

            <section>
                <h2>Navegación y búsqueda de cursos</h2>
                <p>
                    Desde la plataforma es posible explorar los cursos disponibles mediante
                    el buscador y el sistema de categorización mediante etiquetas (tags).
                </p>
                <ol>
                    <li>Ingresar al apartado de cursos.</li>
                    <li>Utilizar el buscador para introducir el contenido que se desea localizar.</li>
                    <li>Utilizar las etiquetas disponibles para filtrar los resultados.</li>
                    <li>Seleccionar el curso que se desea consultar.</li>
                </ol>
            </section>

            <section>
                <h2>Perfil de estudiante</h2>
                <p>
                    El usuario estudiante puede utilizar las funciones relacionadas con
                    la administración de su perfil, búsqueda de contenido y
                    gestión de sus inscripciones.
                </p>
                <p>
                    El apartado <strong>Mis Cursos</strong> permite consultar las
                    inscripciones activas del estudiante desde un espacio centralizado.
                </p>
            </section>

            <section>
                <h2>Perfil de creador o docente</h2>
                <p>
                    Los usuarios con perfil de creador o docente cuentan con funciones
                    adicionales destinadas a la gestión de contenidos educativos.
                </p>
                <ul>
                    <li>Crear cursos.</li>
                    <li>Estructurar cursos.</li>
                    <li>Editar cursos propios.</li>
                    <li>Publicar cursos.</li>
                    <li>Gestionar sus cursos desde el panel correspondiente.</li>
                    <li>Consultar usuarios inscriptos en cursos activos o publicados.</li>
                </ul>
                <div class="nota">
                    Un creador solamente puede modificar o eliminar los cursos de los
                    cuales es autor principal.
                </div>
            </section>

            <section>
                <h2>Creación y publicación de cursos</h2>
                <ol>
                    <li>Ingresar con una cuenta que posea permisos de creador.</li>
                    <li>Acceder al módulo de gestión de cursos.</li>
                    <li>Crear un nuevo curso.</li>
                    <li>Completar y organizar la información y los materiales correspondientes.</li>
                    <li>Guardar los cambios.</li>
                    <li>Publicar el curso cuando se encuentre preparado.</li>
                </ol>
                <p>
                    Los estudiantes no pueden acceder a las herramientas de autoría ni
                    publicar cursos.
                </p>
            </section>

            <section>
                <h2>Inscripciones y prerrequisitos</h2>
                <p>
                    Los usuarios pueden gestionar sus inscripciones desde el apartado
                    correspondiente de la plataforma.
                </p>
                <div class="nota">
                    Para inscribirse a un curso avanzado, el usuario debe tener el estado
                    de <strong>Aprobado</strong> en el curso introductorio correspondiente,
                    de acuerdo con las reglas definidas para el sistema.
                </div>
            </section>

            <section>
                <h2>Seguridad de la cuenta</h2>
                <ul>
                    <li>No compartir las credenciales de acceso con otras personas.</li>
                    <li>Utilizar contraseñas seguras.</li>
                    <li>Cerrar la sesión al terminar de utilizar la plataforma en equipos compartidos.</li>
                    <li>Evitar ingresar credenciales en sitios diferentes al sistema oficial.</li>
                </ul>
                <p>
                    Las sesiones de usuario contemplan un cierre automático después de
                    20 minutos de inactividad continua.
                </p>
            </section>

            <section>
                <h2>Compatibilidad y dispositivos</h2>
                <p>
                    Aprendomo está planteado como una aplicación web multiplataforma.
                    Puede utilizarse principalmente desde computadoras y laptops y,
                    mediante diseño responsivo, también desde dispositivos móviles.
                </p>
            </section>

            <section>
                <h2>Problemas frecuentes</h2>

                <h3>No puedo iniciar sesión</h3>
                <p>
                    Verifique que el correo o usuario y la contraseña hayan sido ingresados
                    correctamente. Si se alcanzan varios intentos fallidos consecutivos,
                    puede producirse un bloqueo temporal.
                </p>

                <h3>No puedo crear un curso</h3>
                <p>
                    Compruebe que la cuenta tenga el rol de creador o docente. Las
                    herramientas de autoría no están disponibles para estudiantes.
                </p>

                <h3>No encuentro un curso</h3>
                <p>
                    Utilice el buscador y las etiquetas disponibles para localizar el
                    contenido.
                </p>

                <h3>No puedo modificar un curso</h3>
                <p>
                    Los creadores solamente pueden editar o eliminar los cursos de los
                    cuales son autores principales.
                </p>
            </section>

            <section>
                <h2>Protección de datos</h2>
                <p>
                    Aprendomo contempla el tratamiento de los datos personales de acuerdo
                    con la normativa indicada en la documentación del proyecto. Los datos
                    deben utilizarse para las funciones correspondientes al funcionamiento
                    de la plataforma y mantenerse protegidos frente a accesos no autorizados.
                </p>
            </section>

            <section>
                <h2>Cierre de sesión</h2>
                <p>
                    Para finalizar el uso de la plataforma, utilizar la opción de
                    <strong>Cerrar sesión</strong> disponible en la interfaz del usuario.
                </p>
            </section>

            <section>
                <h2>Soporte</h2>
                <p>
                    Ante inconvenientes con el funcionamiento de la plataforma, se debe
                    informar el problema al equipo responsable de Aprendomo para que pueda
                    ser analizado y corregido.
                </p>
            </section>

        </main>

        <?php include 'footer.php'; ?>
    <body>
    
</body>
</html>