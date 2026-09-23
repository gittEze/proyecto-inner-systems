<?php 
require_once '../PHP conexiones/conexion.php';

// Esta consulta que permite obtener los cursos de la base de ddatos ordenados por su id y de forma descendente. 

$sql = "SELECT * FROM cursos ORDER BY idCurso DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprendomo - Cursos</title>
    
    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>

    <?php include 'menu.php'; ?>

    <main>
        <h1 class="h1cur"> Cursos </h1>

        <div id="Crearbot">
            <a href="crear.php">
                <button id="envcur" type="button"> Crear Curso</button>
            </a>
        </div>

        <div class="buscadoreishon">
            <input class="buscador2" type="search" placeholder="Buscar">
        </div>
    <!-- Contenedor que posee un condicional que en caso de que los datos traidos no esten vacios,
        toma la variable $cursos y guarda en cada recorrido los datos en #curso.
       -->
        <div class="contenedorCursos">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                    <!-- Al hacer clic en la tarjeta, se abre el modal asociado por el ID -->
                    <a href="#modal-<?= $curso['idCurso'] ?>" class="tarjeta-curso-link">
                        <div class="tarjeta-curso">
                            <h3 class="titulo-curso"><?php echo htmlspecialchars($curso['Titulo_curso']); ?></h3>

                            <div class="box-imagen">
                                <?php if (!empty($curso['Dataso'])): ?>
                                    <img src="../PHP conexiones/Imagenes/<?php echo htmlspecialchars($curso['Dataso']); ?>" alt="Curso">
                                <?php else: ?>
                                    <img src="../IMG/Curso1.png" alt="Curso">
                                <?php endif; ?>

                                <?php if (!empty($curso['Tipo_curso'])): ?>
                                    <span class="badge-tipo"><?php echo htmlspecialchars($curso['Tipo_curso']); ?></span>
                                <?php endif; ?>
                            </div>

                            <p class="desc-curso"><?php echo htmlspecialchars($curso['Descripcion_curso']); ?></p>

                            <div class="info-curso">
                                <div class="dato-item">
                                    <span class="label">Nivel</span>
                                    <span class="valor"><?php echo htmlspecialchars($curso['Nivel_Curso'] ?? $curso['Nivel_curso'] ?? ''); ?></span>
                                </div>

                                <div class="dato-item">
                                    <span class="label">Horas</span>
                                    <span class="valor"><?php echo htmlspecialchars($curso['Duracion_estimada']); ?>h</span>
                                </div>
                                    
                                <div class="dato-item precio-box">
                                    <span class="label">Precio</span>
                                    <span class="valor-precio">$<?php echo htmlspecialchars($curso['Precio']); ?></span>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Ventana emergente (Modal) con la información completa del curso -->
                    <div id="modal-<?= $curso['idCurso'] ?>" class="modal-overlay">
                        <div class="modal-box">
                            <h2>Confirmar Inscripción</h2>
                            
                            <div class="modal-detalles">
                                <h3><?php echo htmlspecialchars($curso['Titulo_curso']); ?></h3>
                                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($curso['Tipo_curso']); ?></p>
                                <p><strong>Nivel:</strong> <?php echo htmlspecialchars($curso['Nivel_Curso'] ?? $curso['Nivel_curso'] ?? ''); ?></p>
                                <p><strong>Duración:</strong> <?php echo htmlspecialchars($curso['Duracion_estimada']); ?> hs</p>
                                <p><strong>Precio:</strong> $<?php echo htmlspecialchars($curso['Precio']); ?></p>
                                <hr>
                                <p class="modal-desc"><?php echo htmlspecialchars($curso['Descripcion_curso']); ?></p>
                            </div>

                            <div class="modal-acciones">
                                <form action="../PHP conexiones/inscripcion.php" method="POST">
                                    <input type="hidden" name="idCurso" value="<?= $curso['idCurso'] ?>">
                                    <button type="submit" class="btn-confirmar">Inscribirme</button>
                                </form>
                                <a href="#" class="btn-cancelar">Cancelar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-cursos">No hay cursos registrados todavía.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
<?php include 'footer.php'; ?>

</body>
<script src="../JS/Botones.js"></script>
</html>