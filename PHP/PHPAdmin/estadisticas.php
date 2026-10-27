<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../PHP conexiones/conexion.php';

$esAdministrador = false;
$usuarioSesion = null;

if (isset($_SESSION['Sesion'])) {
    $correo = $_SESSION['Sesion'];
    $sql = "SELECT Nombre, Apellido, Rol FROM usuarios WHERE Correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuarioSesion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioSesion && strtolower(trim($usuarioSesion['Rol'])) === 'administrador') {
        $esAdministrador = true;
    }
}

if (!$esAdministrador) {
    header("Location: ../main.php");
    exit;
}

try {
    $totalCursos = (int)$pdo->query("SELECT COUNT(*) AS total FROM cursos")->fetch()['total'];
    $totalUsuarios = (int)$pdo->query("SELECT COUNT(*) AS total FROM usuarios")->fetch()['total'];
    $totalDocentes = (int)$pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE LOWER(Rol) = 'docente'")->fetch()['total'];
    $totalEstudiantes = (int)$pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE LOWER(Rol) = 'estudiante'")->fetch()['total'];
    $totalAdmins = (int)$pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE LOWER(Rol) = 'administrador'")->fetch()['total'];

    $totalInscripciones = 0;
    try {
        $totalInscripciones = (int)$pdo->query("SELECT COUNT(*) AS total FROM inscripciones")->fetch()['total'];
    } catch (PDOException $e) {
        $totalInscripciones = 0;
    }

    $nombresCursos = [];
    $totalesInscripcionesCurso = [];
    try {
        $stmtGrafica = $pdo->query("
            SELECT c.Titulo_Curso, COUNT(i.ID_Usuario) AS total_inbound
            FROM cursos c
            LEFT JOIN inscripciones i ON c.ID_Curso = i.ID_Curso
            GROUP BY c.ID_Curso, c.Titulo_Curso
            ORDER BY total_inbound DESC
            LIMIT 12
        ");
        foreach ($stmtGrafica->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $nombresCursos[] = $fila['Titulo_Curso'];
            $totalesInscripcionesCurso[] = (int)$fila['total_inbound'];
        }
    } catch (PDOException $e) {
        // sin datos de gráfica
    }

    $ultimosUsuarios = $pdo->query("
        SELECT Nombre, Apellido, Rol, Fecha_De_Nacimiento
        FROM usuarios
        ORDER BY ID_Usuario DESC
        LIMIT 8
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Panel Administrador</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../CSS/estilo.css">
    <link rel="icon" href="../../IMG/Icono.ico">
    <style>
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: #fff;
            border: 2px solid #bbdefb;
            border-radius: 14px;
            padding: 18px 16px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,.05);
        }
        .stat-card .num {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0d47a1;
            margin: 4px 0;
        }
        .stat-card .label {
            color: #64748b;
            font-size: .9rem;
        }
        .charts-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }
        .chart-box {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,.04);
        }
        .chart-box h3 {
            margin: 0 0 14px;
            color: #0d47a1;
            font-size: 1.05rem;
        }
        .tabla-mini {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.05);
        }
        .tabla-mini th {
            background: #0d47a1;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: .9rem;
        }
        .tabla-mini td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: .92rem;
        }
        .tabla-mini tr:hover td { background: #f5f9ff; }
    </style>
</head>
<body class="admin-active">

<?php include __DIR__ . '/../menu.php'; ?>

<div class="admin-sidebar">
    <div class="admin-title">Panel de Administrador</div>
    <a class="admin-link" href="menuAdmin.php">Inicio panel</a>
    <a class="admin-link active" href="estadisticas.php">Estadísticas</a>
    <a class="admin-link" href="usuarios.php">Gestor de usuarios</a>
</div>

<div class="admin-main-content">
    <h1 style="color:#0d47a1; font-size:26px; margin-bottom:5px;">Estadísticas</h1>
    <p style="color:#64748b; margin-bottom:22px;">Resumen general de la plataforma Aprendomo.</p>

    <div class="stat-cards">
        <div class="stat-card">
            <div class="label">Usuarios</div>
            <div class="num"><?php echo $totalUsuarios; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Docentes</div>
            <div class="num"><?php echo $totalDocentes; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Estudiantes</div>
            <div class="num"><?php echo $totalEstudiantes; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Administradores</div>
            <div class="num"><?php echo $totalAdmins; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Cursos</div>
            <div class="num"><?php echo $totalCursos; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Inscripciones</div>
            <div class="num"><?php echo $totalInscripciones; ?></div>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart-box">
            <h3>Distribución por rol</h3>
            <canvas id="userPieChart" height="220"></canvas>
        </div>
        <div class="chart-box">
            <h3>Inscripciones por curso</h3>
            <?php if (count($nombresCursos) > 0): ?>
                <canvas id="courseBarChart" height="220"></canvas>
            <?php else: ?>
                <p style="color:#64748b;">No hay datos de inscripciones para mostrar.</p>
            <?php endif; ?>
        </div>
    </div>

    <h3 style="color:#0d47a1; margin-bottom:12px;">Últimos usuarios registrados</h3>
    <div style="overflow-x:auto;">
        <table class="tabla-mini">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Fecha de nacimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ultimosUsuarios)): ?>
                    <tr><td colspan="3">No hay usuarios.</td></tr>
                <?php else: ?>
                    <?php foreach ($ultimosUsuarios as $u): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(trim(($u['Nombre'] ?? '') . ' ' . ($u['Apellido'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($u['Rol'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($u['Fecha_De_Nacimiento'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const ctxPie = document.getElementById('userPieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: ['Docentes', 'Estudiantes', 'Administradores'],
        datasets: [{
            data: [<?php echo $totalDocentes; ?>, <?php echo $totalEstudiantes; ?>, <?php echo $totalAdmins; ?>],
            backgroundColor: ['#00b4d8', '#ffb703', '#1e88e5']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

<?php if (count($nombresCursos) > 0): ?>
const ctxBar = document.getElementById('courseBarChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($nombresCursos, JSON_UNESCAPED_UNICODE); ?>,
        datasets: [{
            label: 'Inscripciones',
            data: <?php echo json_encode($totalesInscripcionesCurso); ?>,
            backgroundColor: '#2a9d8f'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/../footer.php'; ?>

</body>
</html>