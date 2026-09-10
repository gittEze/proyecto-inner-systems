<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aprendomo</title>

        <link rel="stylesheet" href="CSS/animaciones.css">
        <link rel="stylesheet" href="CSS/estilo.css">
        <link rel="icon" href="IMG/Icono.ico">
    </head>

    <body class="bodyMain">

        <?php include 'menu.php'; ?>  
      

        <?php if (isset($_SESSION['Sesion'])) { ?>
            <a class="LoginbBtn" href="../PHP conexiones/logout.php">Cerrar sesion</a>
        <?php } else { ?>    
            <a class="loginBtn" href="PHP/login.php?pagina=login">Iniciar sesion</a>
            <a class="loginBtn" href="PHP/login.php?pagina=register">Registrarse</a>
        <?php } ?>
        </div>

        <main>
            <div class="contenedorInicio">
                <div class="textoPrincipal">
                    <h2>
                        Tu conocimiento,<br><span>tu futuro</span>
                    </h2>

                    <p>En Aprendomo tienes acceso a cursos, proyectos educativos y mentorias al mejor precio. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Commodi omnis in eveniet, cumque assumenda eum nostrum reiciendis facere dolores recusandae consequatur placeat dolorum voluptatibus quisquam totam fugiat autem cupiditate expedita?</p>
                    
                    <a class="btnPrincipal" href="cursos.php">
                        Explorar cursos
                    </a>
                </div>

                <div class="imagenPrincipal">
                    <img src="../IMG/principal.png" alt="Estudiante realizando un curso online">
                </div>
            </div>

            <div class="cursosPopularesTitulo">
                <h2>Cursos populares</h2>
                <a href="cursos.php">Ver todos</a>
            </div>

            <div class="cursosPopulares">
                <a href="cursos.php" class="tarjetaCurso">
                    <div class="imagenCurso"><img src="../IMG/Curso1.png" alt="Curso 1"></div>

                    <div class="informacionCurso">
                        <h3>Curso 1</h3>
                        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptates perferendis possimus cumque aliquam numquam? Ex magnam voluptas sed doloremque dolores. Eum cupiditate ipsa, nulla exercitationem veniam optio fugit repellendus. Voluptate!</p>
                        <span class="nivelCurso">
                            Principiante
                        </span>
                    </div>
                </a>

                <a href="cursos.php" class="tarjetaCurso">
                    <div class="imagenCurso"><img src="../IMG/Curso1.png" alt="Curso 2"></div>

                    <div class="informacionCurso">
                        <h3>Curso 2</h3>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quasi quisquam nisi a nihil repudiandae recusandae, nesciunt id sequi modi numquam tempore atque debitis reprehenderit totam quaerat eum soluta temporibus natus.</p>
                        
                        <span class="nivelCurso">
                            Intermedio
                        </span>
                    </div>
                </a>

                <a href="cursos.php" class="tarjetaCurso">
                    <div class="imagenCurso"><img src="../IMG/Curso1.png" alt="Curso 3"></div>

                    <div class="informacionCurso">
                        <h3>Curso 3</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum quos at exercitationem, consequuntur veniam non ratione iusto nihil quis, odit eveniet laudantium dolorum? Temporibus, assumenda accusamus? Accusamus, consequatur? Officiis, corrupti?</p>

                        <span class="nivelCurso">
                            Principiante
                        </span>
                    </div>
                </a>

                <a href="cursos.php" class="tarjetaCurso">
                    <div class="imagenCurso"><img src="../IMG/Curso1.png" alt="Curso 4"></div>

                    <div class="informacionCurso">
                        <h3>Curso 4</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum quos at exercitationem, consequuntur veniam non ratione iusto nihil quis, odit eveniet laudantium dolorum? Temporibus, assumenda accusamus? Accusamus, consequatur? Officiis, corrupti?</p>

                        <span class="nivelCurso">
                            Principiante
                        </span>
                    </div>
                </a>
            </div>
        </main>

        <?php include 'footer.php'; ?>

    </body>
</html> 
