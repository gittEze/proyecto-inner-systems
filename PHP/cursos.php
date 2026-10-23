<?php 
session_start();
require_once '../PHP_conexiones/conexion.php';

// Esta consulta que permite obtener los cursos de la base de datos ordenados por su id y de forma descendente. 

// Cursos con promedio y cantidad de valoraciones (LEFT JOIN para incluir cursos sin valoraciones)
$sql = "SELECT c.*,
               ROUND(AVG(v.Puntuacion), 1) AS promedio_valoracion,
               COUNT(v.ID_Valoracion) AS total_valoraciones
        FROM cursos c
        LEFT JOIN valoraciones v ON v.ID_Curso = c.ID_Curso
        GROUP BY c.ID_Curso
        ORDER BY c.ID_Curso DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Evaluamos el rol del usuario mediante una sessión.
$rolper = strtolower(trim($_SESSION['Rol'] ?? ''));
$acesso = !empty($rolper) && $rolper !== 'estudiante' && ($rolper === 'docente' || $rolper === 'administrador');
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
         
        <!-- Solo se mostrar el boton en caso que lo que esta en acceso se cumpla -->
        <?php if ($acesso): ?>
        <div id="Crearbot">
            <a href="crear.php">
                <button id="envcur" type="button"> Crear Curso</button>
            </a>
        </div>
        <?php endif; ?>
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
                    <a href="#modal-<?= $curso['ID_Curso'] ?>" class="tarjeta-curso-link">
                        <div class="tarjeta-curso">
                            <h3 class="titulo-curso"><?php echo htmlspecialchars($curso['Titulo_Curso']); ?></h3>

                            <!-- Estrcutura para mostrar la imagen de los cursos traidos -->
                            <div class="box-imagen">
                                <?php if (!empty($curso['Dataso'])): ?>
                                    <img src="../PHP conexiones/Imagenes/<?php echo htmlspecialchars($curso['Dataso']); ?>" alt="Curso">
                                <?php else: ?>
                                    <img src="../IMG/Curso1.png" alt="Curso">
                                <?php endif; ?>

                                <?php if (!empty($curso['Tipo_Curso'])): ?>
                                    <span class="badge-tipo"><?php echo htmlspecialchars($curso['Tipo_Curso']); ?></span>
                                <?php endif; ?>
                            </div>

                            <p class="desc-curso"><?php echo htmlspecialchars($curso['Descripcion_Curso']); ?></p>

                            <!-- Valoración promedio del curso -->
                            <?php
                                $promedioCard = isset($curso['promedio_valoracion']) ? (float)$curso['promedio_valoracion'] : 0;
                                $totalCard    = isset($curso['total_valoraciones']) ? (int)$curso['total_valoraciones'] : 0;
                                $estrellasLlenas = $promedioCard > 0 ? (int)round($promedioCard) : 0;
                            ?>
                            <div class="valoracion-tarjeta">
                                <?php if ($totalCard > 0): ?>
                                    <div class="estrellas-tarjeta">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= $estrellasLlenas ? 'estrella-llena-card' : 'estrella-vacia-card' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="texto-valoracion-card">
                                        <?= number_format($promedioCard, 1) ?>
                                        <span class="total-val-card">(<?= $totalCard ?>)</span>
                                    </span>
                                <?php else: ?>
                                    <span class="sin-valoracion-card">Sin valoraciones aún</span>
                                <?php endif; ?>
                            </div>

                            <div class="info-curso">
                                <div class="dato-item">
                                    <span class="label">Nivel</span>
                                    <span class="valor"><?php echo htmlspecialchars($curso['Nivel_Curso'] ?? $curso['Nivel_Curso'] ?? ''); ?></span>
                                </div>

                                <div class="dato-item">
                                    <span class="label">Horas</span>
                                    <span class="valor"><?php echo htmlspecialchars($curso['Duracion_Estimada']); ?>h</span>
                                </div>
                                    
                                <div class="dato-item precio-box">
                                    <span class="label">Precio</span>
                                    <span class="valor-precio">$<?php echo htmlspecialchars($curso['Precio']); ?></span>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Ventana emergente (Modal) con la información completa del curso -->
                    <div id="modal-<?= $curso['ID_Curso'] ?>" class="modal-overlay">
                        <div class="modal-box">
                            <h2>Confirmar Inscripción</h2>
                            
                            <!-- Modal dedicado a mostrar la información de la tarjeta del curso -->
                            <div class="modal-detalles">
                                <h3><?php echo htmlspecialchars($curso['Titulo_Curso']); ?></h3>
                                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($curso['Tipo_Curso']); ?></p>
                                <p><strong>Nivel:</strong> <?php echo htmlspecialchars($curso['Nivel_Curso'] ?? $curso['Nivel_Curso'] ?? ''); ?></p>
                                <p><strong>Duración:</strong> <?php echo htmlspecialchars($curso['Duracion_Estimada']); ?> hs</p>
                                <p><strong>Precio:</strong> $<?php echo htmlspecialchars($curso['Precio']); ?></p>
                                <p><strong>Valoración:</strong>
                                    <?php if ($totalCard > 0): ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: <?= $i <= $estrellasLlenas ? '#ffb400' : '#ccc' ?>; font-size: 22px;">★</span>                                          <?php endfor; ?>
                                        <?= number_format($promedioCard, 1) ?>
                                        (<?= $totalCard ?> valoración<?= $totalCard !== 1 ? 'es' : '' ?>)
                                    <?php else: ?>
                                        Sin valoraciones aún
                                    <?php endif; ?>
                                </p>
                                <hr>
                                <p class="modal-desc"><?php echo htmlspecialchars($curso['Descripcion_Curso']); ?></p>
                            </div>

                            <div class="modal-acciones">
                                <?php if (isset($_SESSION['Sesion'])): ?>
                                    <!-- Agregar al carrito -->
                                    <form action="../PHP conexiones/agregarCarrito.php" method="POST">
                                        <input type="hidden" name="ID_Curso" value="<?= $curso['ID_Curso'] ?>">
                                        <button type="submit" class="btn-confirmar">Agregar al carrito</button>
                                    </form>

                                    <!-- Inscripción directa (un solo curso) -->
                                    <form action="../PHP conexiones/inscripcion.php" method="POST">
                                        <input type="hidden" name="ID_Curso" value="<?= $curso['ID_Curso'] ?>">
                                        <button type="submit" class="btn-confirmar" style="background:#0d47a1;">Inscribirme ahora</button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="btn-confirmar">Inicia sesión para inscribirte</a>
                                <?php endif; ?>
                                
                                <!-- Condicional que evlua si el rol es de tipo administrador para permitirle al usuario eliminar cursos. -->
                                <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] === 'administrador'): ?>
                                    <form action="../PHP conexiones/eliminarcurso.php" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este curso?');">
                                        <input type="hidden" name="ID_Curso" value="<?= $curso['ID_Curso'] ?>">
                                        <button type="submit" class="btn-elim">Eliminar Curso</button>
                                    </form>
                                <?php endif; ?>

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

</body>
<script src="../JS/Botones.js"></script>
</html>