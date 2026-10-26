<?php
session_start();
require_once '../PHP conexiones/conexion.php';

if (!isset($_SESSION['Sesion']) || !isset($_SESSION['ID_Usuario'])) {
    header('Location: login.php');
    exit();
}

$carritoIds = isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])
    ? array_map('intval', $_SESSION['carrito'])
    : [];

$cursosCarrito = [];
$totalPrecio   = 0;

if (!empty($carritoIds)) {
    // Placeholders seguros para IN (...)
    $placeholders = implode(',', array_fill(0, count($carritoIds), '?'));
    $sql = "SELECT c.*,
                   ROUND(AVG(v.Puntuacion), 1) AS promedio_valoracion,
                   COUNT(v.ID_Valoracion) AS total_valoraciones
            FROM cursos c
            LEFT JOIN valoraciones v ON v.ID_Curso = c.ID_Curso
            WHERE c.ID_Curso IN ($placeholders)
            GROUP BY c.ID_Curso
            ORDER BY c.Titulo_Curso ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($carritoIds);
    $cursosCarrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cursosCarrito as $c) {
        $totalPrecio += (float)($c['Precio'] ?? 0);
    }
}

$cantidad = count($cursosCarrito);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito | Aprendomo</title>
    <link rel="stylesheet" href="../CSS/animaciones.css">
    <link rel="stylesheet" href="../CSS/estilo.css">
    <link rel="icon" href="../IMG/Icono.ico">
</head>
<body>

<?php include 'menu.php'; ?>

<main class="carrito-main">
    <h1 class="h1cur">Mi carrito</h1>

    <?php if ($cantidad === 0): ?>
        <div class="carrito-vacio card-widget">
            <img src="../IMG/carrito.png" alt="Carrito vacío" class="img-carrito-vacio">
            <p>Tu carrito está vacío.</p>
            <a href="cursos.php" class="btn-confirmar">Ver cursos</a>
        </div>
    <?php else: ?>
        <p class="carrito-resumen-top">
            Tienes <strong><?= $cantidad ?></strong> curso<?= $cantidad !== 1 ? 's' : '' ?> en el carrito.
        </p>

        <div class="carrito-lista">
            <?php foreach ($cursosCarrito as $curso): ?>
                <?php
                    $promedio = isset($curso['promedio_valoracion']) ? (float)$curso['promedio_valoracion'] : 0;
                    $totalVal = isset($curso['total_valoraciones']) ? (int)$curso['total_valoraciones'] : 0;
                    $estrellas = $promedio > 0 ? (int)round($promedio) : 0;
                ?>
                <article class="carrito-item">
                    <div class="carrito-item-img">
                        <?php if (!empty($curso['Dataso'])): ?>
                            <img src="../PHP conexiones/Imagenes/<?= htmlspecialchars($curso['Dataso']) ?>" alt="">
                        <?php else: ?>
                            <img src="../IMG/Curso1.png" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="carrito-item-info">
                        <h3><?= htmlspecialchars($curso['Titulo_Curso']) ?></h3>
                        <p class="text-muted">
                            <?= htmlspecialchars($curso['Tipo_Curso'] ?? '') ?>
                            · <?= htmlspecialchars($curso['Nivel_Curso'] ?? '') ?>
                            · <?= htmlspecialchars($curso['Duracion_Estimada'] ?? '') ?>h
                        </p>
                        <?php if ($totalVal > 0): ?>
                            <p class="carrito-valoracion">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="<?= $i <= $estrellas ? 'estrella-llena' : 'estrella-vacia' ?>">★</span>
                                <?php endfor; ?>
                                <?= number_format($promedio, 1) ?> (<?= $totalVal ?>)
                            </p>
                        <?php endif; ?>
                        <p class="carrito-precio">$<?= htmlspecialchars($curso['Precio']) ?></p>
                    </div>
                    <div class="carrito-item-acciones">
                        <form action="../PHP conexiones/quitarCarrito.php" method="POST">
                            <input type="hidden" name="ID_Curso" value="<?= (int)$curso['ID_Curso'] ?>">
                            <button type="submit" class="btn-elim">Quitar</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="carrito-footer card-widget">
            <div class="carrito-total">
                <span>Total estimado:</span>
                <strong>$<?= number_format($totalPrecio, 0, ',', '.') ?></strong>
            </div>

            <div id="paypal-button-container"></div>

            <a href="cursos.php" class="btn-cancelar">Seguir explorando</a>
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

<script src="https://www.paypal.com/sdk/js?client-id=AeYzyWAkXpGUqHvAmSxNOfK66DfK3fJ-JIGDGsVLFXnpt-8WuhV9-YBGZnRvN_HP_HGrQ7zdXRszlorK&currency=USD"></script>


<!-- Estructura del SDK para la funcionalidad de Paypal -->
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo number_format($totalPrecio, 2, '.', ''); ?>'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Redirige al backend pasando el ID de la orden de pago
                window.location.href = '../PHP conexiones/inscribirCarrito.php?paymentID=' + data.orderID;
            });
        },
        onCancel: function (data) {
            alert('El pago fue cancelado.');
        },
        onError: function (err) {
            console.error(err);
            alert('Hubo un error al conectar con PayPal.');
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>