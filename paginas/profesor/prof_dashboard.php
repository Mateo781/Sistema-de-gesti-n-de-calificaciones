<?php
require_once __DIR__ . '/../../php/db.php';
require_once __DIR__ . '/../../php/profesor/funciones_dashboard.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_docente = $_SESSION['usuario_id'] ?? null;

if (!$id_docente) {
    echo "<p>Error: Sesión docente no válida.</p>";
    exit;
}

$nombre_docente = trim(($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? ''));
if ($nombre_docente === '') {
    $nombre_docente = 'Docente';
}

// --- Datos reales ---
$stats             = obtenerEstadisticasGenerales($pdo, $id_docente);
$promedioPorCurso  = obtenerPromedioPorCurso($pdo, $id_docente);
$estadoCarga       = obtenerEstadoCargaCalificaciones($pdo, $id_docente);
$evolucion         = obtenerEvolucionPromedio($pdo, $id_docente);
$proximas          = obtenerProximasEvaluaciones($pdo, $id_docente);
$notificaciones    = obtenerNotificaciones($pdo, $id_docente);
$cursosResumen     = obtenerCursosResumen($pdo, $id_docente);
$actividad         = obtenerActividadReciente($pdo, $id_docente);
$eventosCalendario = obtenerEventosCalendario($pdo, $id_docente);

// Todo lo que necesita el JS (gráficos, calendario) va en un único bloque JSON.
$datosDashboard = [
    'promedioPorCurso'  => $promedioPorCurso,
    'estadoCarga'       => $estadoCarga,
    'evolucion'         => $evolucion,
    'eventosCalendario' => $eventosCalendario,
];

$mesesLargo = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$diasLargo  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$fechaHoy   = new DateTime();
$fechaTexto = $diasLargo[(int)$fechaHoy->format('w')] . ' ' . $fechaHoy->format('d') . ' de ' . $mesesLargo[(int)$fechaHoy->format('n') - 1] . ' de ' . $fechaHoy->format('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel del Docente · SGC</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>

<!-- Bloque de datos reales para gráficos y calendario -->
<script id="dashboardData" type="application/json"><?= json_encode($datosDashboard, JSON_UNESCAPED_UNICODE) ?></script>

<div class="loader-overlay" id="loaderOverlay">
    <div class="loader-spinner"></div>
</div>

<main class="dash">

    <!-- ============ ENCABEZADO ============ -->
    <header class="dash-header reveal">
        <nav class="breadcrumb">
            <span>Panel Docente</span>
            <i class="fa-solid fa-chevron-right"></i>
            <span class="breadcrumb-current">Inicio</span>
        </nav>

        <div class="dash-header-row">
            <div>
                <h1 class="dash-title">Bienvenido, <?= htmlspecialchars($nombre_docente) ?></h1>
                <p class="dash-subtitle">Este es tu panel de control principal. Desde acá podés gestionar calificaciones, consultar tus cursos, revisar evaluaciones y acceder a las principales funciones del sistema.</p>
            </div>

            <div class="dash-header-meta">
                <div class="meta-date">
                    <i class="fa-regular fa-calendar"></i>
                    <span><?= htmlspecialchars($fechaTexto) ?></span>
                </div>
                <div class="meta-clock" id="relojEnVivo">--:--:--</div>
                <div class="header-actions">
                    <button class="btn-icon" id="btnActualizar" title="Actualizar datos" aria-label="Actualizar datos">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </button>
                    <button class="btn-icon" id="btnFullscreen" title="Pantalla completa" aria-label="Pantalla completa">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- ============ TARJETAS DE ESTADÍSTICAS ============ -->
    <section class="stats-grid">
        <article class="stat-card reveal" style="--accent:#1E4FD8;">
            <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-count="<?= (int) $stats['alumnos_a_cargo'] ?>">0</span>
                <span class="stat-label">Alumnos a cargo</span>
            </div>
        </article>

        <article class="stat-card reveal" style="--accent:#0EA5E9;">
            <div class="stat-icon"><i class="fa-solid fa-chalkboard"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-count="<?= (int) $stats['cursos_asignados'] ?>">0</span>
                <span class="stat-label">Cursos asignados</span>
            </div>
        </article>

        <article class="stat-card reveal" style="--accent:#6366F1;">
            <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-count="<?= (int) $stats['materias_asignadas'] ?>">0</span>
                <span class="stat-label">Materias asignadas</span>
            </div>
        </article>

        <article class="stat-card reveal" style="--accent:#D97706;">
            <div class="stat-icon"><i class="fa-solid fa-clipboard-question"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-count="<?= (int) $stats['evaluaciones_pendientes'] ?>">0</span>
                <span class="stat-label">Evaluaciones pendientes</span>
            </div>
        </article>

        <article class="stat-card reveal" style="--accent:#15A26B;">
            <div class="stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-count="<?= (int) $stats['calificaciones_cargadas'] ?>">0</span>
                <span class="stat-label">Calificaciones cargadas</span>
            </div>
        </article>

        <article class="stat-card reveal" style="--accent:#0B2559;">
            <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-decimal data-count="<?= $stats['promedio_general'] ?? 0 ?>">0</span>
                <span class="stat-label">Promedio general<?= $stats['promedio_general'] === null ? ' (sin datos aún)' : '' ?></span>
            </div>
        </article>
    </section>

    <!-- ============ GRÁFICOS ============ -->
    <section class="charts-grid">
        <div class="chart-card reveal">
            <h2 class="card-title">Promedio por curso</h2>
            <?php if (empty($promedioPorCurso['labels'])): ?>
                <p class="empty-state">Todavía no hay promedios calculados en tus trayectorias.</p>
            <?php else: ?>
                <canvas id="chartPromedioCurso"></canvas>
            <?php endif; ?>
        </div>

        <div class="chart-card reveal">
            <h2 class="card-title">Estado de carga de calificaciones</h2>
            <?php if (array_sum($estadoCarga) === 0): ?>
                <p class="empty-state">No tenés evaluaciones registradas todavía.</p>
            <?php else: ?>
                <canvas id="chartEstadoCarga"></canvas>
            <?php endif; ?>
        </div>

        <div class="chart-card chart-card-wide reveal">
            <h2 class="card-title">Evolución del promedio</h2>
            <?php if (empty($evolucion['labels'])): ?>
                <p class="empty-state">Todavía no hay calificaciones numéricas cargadas para graficar la evolución.</p>
            <?php else: ?>
                <canvas id="chartEvolucion"></canvas>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============ ACTIVIDADES + NOTIFICACIONES ============ -->
    <section class="two-col-grid">
        <div class="panel-card reveal">
            <h2 class="card-title">Próximas actividades</h2>
            <?php if (empty($proximas)): ?>
                <p class="empty-state">No tenés evaluaciones programadas próximamente.</p>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($proximas as $act): ?>
                        <li class="activity-item prioridad-<?= $act['prioridad'] ?>">
                            <div class="activity-marker"></div>
                            <div class="activity-content">
                                <strong><?= htmlspecialchars($act['titulo']) ?></strong>
                                <span><?= htmlspecialchars($act['materia']) ?> · <?= htmlspecialchars($act['curso']) ?> · <?= htmlspecialchars($act['tipo_evaluacion']) ?></span>
                            </div>
                            <div class="activity-date">
                                <?= (new DateTime($act['fecha_evaluacion']))->format('d/m') ?>
                                <small><?= $act['dias_restantes'] == 0 ? 'Hoy' : ($act['dias_restantes'] == 1 ? 'Mañana' : $act['dias_restantes'] . ' días') ?></small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="panel-card reveal">
            <h2 class="card-title">Notificaciones</h2>
            <?php if (empty($notificaciones)): ?>
                <p class="empty-state">No tenés notificaciones pendientes. Todo al día.</p>
            <?php else: ?>
                <ul class="notif-list">
                    <?php foreach ($notificaciones as $n): ?>
                        <li class="notif-item notif-<?= $n['color'] ?>">
                            <i class="fa-solid <?= htmlspecialchars($n['icono']) ?>"></i>
                            <div class="notif-content">
                                <span><?= htmlspecialchars($n['texto']) ?></span>
                                <small><?= (new DateTime($n['fecha']))->format('d/m/Y') ?></small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============ TABLA DE CURSOS ============ -->
    <section class="table-card reveal">
        <div class="table-card-header">
            <h2 class="card-title">Tus cursos y materias</h2>
            <div class="table-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="buscadorCursos" placeholder="Buscar curso o materia...">
            </div>
        </div>

        <?php if (empty($cursosResumen)): ?>
            <p class="empty-state">Todavía no tenés cursos ni materias asignadas.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="dash-table" id="tablaCursos">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Materia</th>
                            <th>Alumnos</th>
                            <th>Promedio</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cursosResumen as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['curso']) ?></td>
                                <td><?= htmlspecialchars($c['materia']) ?></td>
                                <td><?= (int) $c['alumnos'] ?></td>
                                <td><?= $c['promedio'] !== null ? number_format($c['promedio'], 2) : '—' ?></td>
                                <td>
                                    <span class="badge badge-<?= $c['estado'] ?>">
                                        <?= ['aprobado' => 'Promedio ≥ 7', 'en-proceso' => 'En proceso', 'sin-datos' => 'Sin datos'][$c['estado']] ?>
                                    </span>
                                </td>
                                <td class="td-actions">
                                    <a href="../../index.php?p=prof_calificaciones&filtro_curso_materia=<?= $c['id_curso_materia'] ?>" class="btn-table" title="Cargar notas">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="../../index.php?p=prof_evaluaciones&filtro_curso_materia=<?= $c['id_curso_materia'] ?>" class="btn-table" title="Ver evaluaciones">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination" id="paginacionCursos"></div>
        <?php endif; ?>
    </section>

    <!-- ============ ACCESOS RÁPIDOS ============ -->
    <section class="panel-card reveal">
        <h2 class="card-title">Accesos rápidos</h2>
        <div class="quick-actions">
            <a href="../../index.php?p=prof_calificaciones" class="quick-btn">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Cargar calificaciones</span>
            </a>
            <a href="../../index.php?p=prof_evaluaciones" class="quick-btn">
                <i class="fa-solid fa-file-circle-plus"></i>
                <span>Evaluaciones</span>
            </a>
            <a href="../../index.php?p=prof_calificaciones" class="quick-btn">
                <i class="fa-solid fa-users"></i>
                <span>Ver alumnos</span>
            </a>
            <button type="button" class="quick-btn" id="btnVerCalendario">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Calendario</span>
            </button>
        </div>
        <p class="quick-note">Nota: "Boletines", "Reportes" y "Comunicaciones" todavía no tienen una página propia en el sistema — se agregan como accesos cuando existan esas pantallas.</p>
    </section>

    <!-- ============ ACTIVIDAD RECIENTE + CALENDARIO ============ -->
    <section class="two-col-grid">
        <div class="panel-card reveal">
            <h2 class="card-title">Actividad reciente</h2>
            <?php if (empty($actividad)): ?>
                <p class="empty-state">Todavía no hay actividad registrada.</p>
            <?php else: ?>
                <ul class="timeline-list">
                    <?php foreach ($actividad as $a): ?>
                        <li class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span><?= htmlspecialchars($a['descripcion']) ?></span>
                                <small><?= (new DateTime($a['fecha']))->format('d/m/Y H:i') ?> hs</small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="panel-card reveal" id="panelCalendario">
            <div class="calendar-header">
                <h2 class="card-title">Calendario</h2>
                <div class="calendar-nav">
                    <button type="button" id="calPrev" aria-label="Mes anterior"><i class="fa-solid fa-chevron-left"></i></button>
                    <span id="calMesLabel"></span>
                    <button type="button" id="calNext" aria-label="Mes siguiente"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
            <div class="calendar-detail" id="calendarDetail"></div>
        </div>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="../../js/dashboard.js"></script>
</body>
</html>