<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ruta de tu archivo de conexión
require_once __DIR__ . '/../../PHP conexiones/conexion.php';

try {
    // Consulta de cantidad total de cursos.
    $stmtCursos = $pdo->query("SELECT COUNT(*) AS total FROM cursos");
    $totalCursos = $stmtCursos->fetch()['total'];

    // Consulta de cantidad de usuarios registrados.
    $stmtUsuarios = $pdo->query("SELECT COUNT(*) AS total FROM usuarios");
    $totalUsuarios = $stmtUsuarios->fetch()['total'];

    // Consulta de cantidad de docentes
    $stmtDocentes = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE Rol = 'Docente'");
    $totalDocentes = $stmtDocentes->fetch()['total'];

    // Consulta de cantidad de estudiantes
    $stmtEstudiantes = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE Rol = 'Estudiante'");
    $totalEstudiantes = $stmtEstudiantes->fetch()['total'];

    // Consulta de cantidad de usuarios inscriptos
    $stmtInscripciones = $pdo->query("SELECT COUNT(*) AS total FROM inscripciones");
    $totalInscripciones = $stmtInscripciones->fetch()['total'];

    // Datos para la gráfica de barras: Inscripciones por Curso
    $stmtGraficaCursos = $pdo->query("
        SELECT c.Titulo_Curso, COUNT(i.ID_Usuario) AS total_inbound 
        FROM cursos c 
        LEFT JOIN inscripciones i ON c.ID_Curso = i.ID_Curso 
        GROUP BY c.ID_Curso, c.Titulo_Curso
    ");
    $datosGrafica = $stmtGraficaCursos->fetchAll();

    $nombresCursos = [];
    $totalesInscripcionesCurso = [];
    foreach ($datosGrafica as $fila) {
        $nombresCursos[] = $fila['Titulo_Curso'];
        $totalesInscripcionesCurso[] = $fila['total_inbound'];
    }

    // Últimos usuarios registrados (para la tabla inferior)
    $stmtUltimos = $pdo->query("SELECT Nombre, Rol, Fecha_De_Nacimiento FROM usuarios ORDER BY ID_Usuario DESC LIMIT 5");
    $ultimosUsuarios = $stmtUltimos->fetchAll();

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Estadísticas Educativas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../CSS/estilo.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .dashboard-container { max-width: 1100px; margin: auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .cards-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 25px; }
        .card { padding: 20px; border-radius: 10px; color: #fff; }
        .card-teal { background: #00b4d8; }
        .card-blue { background: #0077b6; }
        .card-yellow { background: #ffb703; color: #333; }
        .card-orange { background: #f77f00; }
        .card-green { background: #2a9d8f; }
        .card h3 { margin: 0; font-size: 13px; text-transform: uppercase; }
        .card p { font-size: 26px; font-weight: bold; margin: 10px 0 0 0; }
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .chart-box { background: #fff; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .section-title { font-size: 18px; margin-bottom: 15px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background-color: #f8f9fa; color: #555; }
    </style>
</head>
<body>

<?php 
include __DIR__ . '/../menu.php'; 
?>

<div class="dashboard-container">
    <!-- Cabecera -->
    <div class="header">
        <div>
            <h2>Estadísticas Educativas</h2>
            <p style="color: #666; margin: 0;">Plataforma de Cursos y Usuarios</p>
        </div>
        <div>
            <strong>Usuario Activo:</strong> Administrador
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="cards-grid">
        <div class="card card-teal">
            <h3>Cursos Totales</h3>
            <p><?php echo $totalCursos; ?></p>
        </div>
        <div class="card card-blue">
            <h3>Usuarios Registrados</h3>
            <p><?php echo $totalUsuarios; ?></p>
        </div>
        <div class="card card-yellow">
            <h3>Docentes</h3>
            <p><?php echo $totalDocentes; ?></p>
        </div>
        <div class="card card-orange">
            <h3>Estudiantes</h3>
            <p><?php echo $totalEstudiantes; ?></p>
        </div>
        <div class="card card-green">
            <h3>Inscripciones</h3>
            <p><?php echo $totalInscripciones; ?></p>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-box">
            <h3 class="section-title">Distribución de Usuarios por Rol</h3>
            <canvas id="userPieChart"></canvas>
        </div>

        <!-- Gráfica de Barras: Inscripciones por Curso -->
        <div class="chart-box">
            <h3 class="section-title">Inscripciones Recientes por Curso</h3>
            <canvas id="courseBarChart"></canvas>
        </div>
    </div>

    <div>
        <h3 class="section-title">Últimos Usuarios Registrados</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Fecha de Nacimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosUsuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Rol']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Fecha_De_Nacimiento']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Gráfica de Pastel (Docentes vs Estudiantes)
    const ctxPie = document.getElementById('userPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Docentes', 'Estudiantes'],
            datasets: [{
                data: [<?php echo $totalDocentes; ?>, <?php echo $totalEstudiantes; ?>],
                backgroundColor: ['#00b4d8', '#ffb703']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfica de Barras (Inscripciones por Curso)
    const ctxBar = document.getElementById('courseBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($nombresCursos); ?>,
            datasets: [{
                label: 'Inscripciones',
                data: <?php echo json_encode($totalesInscripcionesCurso); ?>,
                backgroundColor: '#2a9d8f'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>

<?php 
// Incluir el footer asegurando la ruta correcta si existe
@include __DIR__ . '/../footer.php'; 
?>

</body>
</html>