<?php 
session_start();
require_once '../PHP conexiones/conexion.php';

// Término de búsqueda recibido desde el buscador
$busqueda = trim($_GET['buscar'] ?? '');

// Consulta de cursos con promedio y cantidad de valoraciones
$sql = "SELECT c.*,
               ROUND(AVG(v.Puntuacion), 1) AS promedio_valoracion,
               COUNT(v.ID_Valoracion) AS total_valoraciones
        FROM cursos c
        LEFT JOIN valoraciones v ON v.ID_Curso = c.ID_Curso";

$params = [];

if ($busqueda !== '') {
    $sql .= " WHERE (
        c.Titulo_Curso LIKE :titulo
        OR c.Descripcion_Curso LIKE :descripcion
        OR c.Tipo_Curso LIKE :tipo
        OR c.Nivel_Curso LIKE :nivel
    )";

    $termino = '%' . $busqueda . '%';

    $params = [
        ':titulo'      => $termino,
        ':descripcion' => $termino,
        ':tipo'        => $termino,
        ':nivel'       => $termino
    ];
}

$sql .= " GROUP BY c.ID_Curso
          ORDER BY c.ID_Curso DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Evaluamos el rol del usuario (normalizado a minúsculas)
$rolper = strtolower(trim($_SESSION['Rol'] ?? ''));
$acesso = !empty($rolper) && ($rolper === 'docente' || $rolper === 'administrador');
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

    <main class="main-cursos">
        <div class="cabecera-cursos">
            <h1 class="h1cur">Cursos</h1>
             
            <?php if ($acesso): ?>
            <div id="Crearbot">
                <a href="crear.php">
                    <button id="envcur" type="button">Crear Curso</button>
                </a>
            </div>
            <?php endif; ?>

            <div class="buscadoreishon">
                <form action="cursos.php" method="GET">
                    <input class="buscador2" type="search" name="buscar" placeholder="Buscar cursos por nombre o categoría..." value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Buscar cursos">
                </form>
            </div>
        </div>

        <div class="contenedorCursos">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                    <a href="#modal-<?= $curso['ID_Curso'] ?>" class="tarjeta-curso-link">
                        <div class="tarjeta-curso">
                            <h3 class="titulo-curso"><?php echo htmlspecialchars($curso['Titulo_Curso']); ?></h3>

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
                                    <span class="valor"><?php echo htmlspecialchars($curso['Nivel_Curso'] ?? ''); ?></span>
                                </div>

                                <div class="dato-item">
                                    <span class="label">Horas</span>
                                    <span class="valor"><?php echo htmlspecialchars($curso['Duracion_Estimada']); ?>h</span>
                                </div>
                                    
                                <div class="dato-item precio-box">
                                    <span class="label">Precio</span>
                                    <span class="valor-precio">
                                        <?= $curso['Precio'] == 0 ? 'GRATIS' : '$' . htmlspecialchars($curso['Precio']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Modal del Curso -->
                    <div id="modal-<?= $curso['ID_Curso'] ?>" class="modal-overlay">
                        <div class="modal-box <?= $curso['Precio'] == 0 ? 'modal-box-gratis' : 'modal-box-pago' ?>">
                            <h2>Confirmar Inscripción</h2>
                            
                            <div class="modal-detalles">
                                <h3><?php echo htmlspecialchars($curso['Titulo_Curso']); ?></h3>
                                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($curso['Tipo_Curso']); ?></p>
                                <p><strong>Nivel:</strong> <?php echo htmlspecialchars($curso['Nivel_Curso'] ?? ''); ?></p>
                                <p><strong>Duración:</strong> <?php echo htmlspecialchars($curso['Duracion_Estimada']); ?> hs</p>
                                <p><strong>Precio:</strong> 
                                    <span class="<?= $curso['Precio'] == 0 ? 'precio-gratis-badge' : 'precio-pago-badge' ?>">
                                        <?= $curso['Precio'] == 0 ? '¡GRATIS!' : '$' . htmlspecialchars($curso['Precio']) ?>
                                    </span>
                                </p>
                                <p><strong>Valoración:</strong>
                                    <?php if ($totalCard > 0): ?>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: <?= $i <= $estrellasLlenas ? '#ffb400' : '#ccc' ?>; font-size: 22px;">★</span>
                                        <?php endfor; ?>
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
                                    
                                    <?php if ($curso['Precio'] > 0): ?>
                                        <!-- Si el curso tiene costo -->
                                        <form action="../PHP conexiones/agregarCarrito.php" method="POST">
                                            <input type="hidden" name="ID_Curso" value="<?= $curso['ID_Curso'] ?>">
                                            <button type="submit" class="btn-confirmar">Agregar al carrito</button>
                                        </form>

                                        <!-- Botón de PayPal para compra individual -->
                                        <div id="paypal-button-curso-<?= $curso['ID_Curso'] ?>" style="margin-top: 10px; width: 100%;"></div>
                                    <?php else: ?>
                                        <!-- Si el curso es gratuito -->
                                        <form action="../PHP conexiones/inscripcion.php" method="POST">
                                            <input type="hidden" name="ID_Curso" value="<?= $curso['ID_Curso'] ?>">
                                            <button type="submit" class="btn-confirmar btn-inscribir-gratis">Inscribirme gratis</button>
                                        </form>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <a href="login.php" class="btn-confirmar">Inicia sesión para inscribirte</a>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['Rol']) && strtolower(trim($_SESSION['Rol'])) === 'administrador'): ?>
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
                <p class="no-cursos">
                    <?php if ($busqueda !== ''): ?>
                        No se encontraron cursos para
                        "<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>".
                        Probá con otra palabra.
                    <?php else: ?>
                        No hay cursos registrados todavía.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- SDK de JavaScript de PayPal con tu Client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=BAAqOpzuTQECH3Aw2j_Q0MM9gNQIkhNZ93folDgVlvFmwlZonAz1gj_NK7-x15H8vNX5Pt71Pqmhf_VSx8&currency=USD"></script>
    <script src="../JS/Botones.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php foreach ($cursos as $curso): ?>
                <?php if ($curso['Precio'] > 0): ?>
                    if (document.getElementById("paypal-button-curso-<?= $curso['ID_Curso'] ?>")) {
                        paypal.Buttons({
                            createOrder: function(data, actions) {
                                return actions.order.create({
                                    purchase_units: [{
                                        amount: {
                                            value: '<?= $curso['Precio'] ?>'
                                        }
                                    }]
                                });
                            },
                            onApprove: function(data, actions) {
                                return actions.order.capture().then(function(details) {
                                    window.location.href = '../PHP conexiones/inscribirCursoIndividual.php?ID_Curso=<?= $curso['ID_Curso'] ?>&paymentID=' + data.orderID;
                                });
                            }
                        }).render('#paypal-button-curso-<?= $curso['ID_Curso'] ?>');
                    }
                <?php endif; ?>
            <?php endforeach; ?>
        });
    </script>
</body>
</html>