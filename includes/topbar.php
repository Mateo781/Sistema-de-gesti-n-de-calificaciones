<?php
// Asegurar conexión a la BD (ajusta según tu configuración)
// require_once 'config/db.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_alumno = $_SESSION['id_usuario'] ?? null;
$seccion_actual = $_GET['p'] ?? 'inicio';
$header_titulo = "Panel de Control";
$mostrar_filtros = false;

// Variables por defecto
$header_curso = "Sin asignar";
$header_orientacion = "";
$ciclo_activo = "2026";

if ($id_alumno) {
    // 1. Obtener datos de curso, orientación y ciclo activo del alumno
    // Se une inscripciones, cursos y ciclos_lectivos
    $stmt = $pdo->prepare("
        SELECT c.nombre as curso_nombre, c.division, cl.anio 
        FROM inscripciones i
        JOIN cursos c ON i.id_curso = c.id
        JOIN ciclos_lectivos cl ON c.id_ciclo = cl.id
        WHERE i.id_alumno = ? AND i.activo = 1 AND cl.activo = 1
        LIMIT 1
    ");
    $stmt->execute([$id_alumno]);
    $datos = $stmt->fetch();

    if ($datos) {
        $header_curso = $datos['curso_nombre'] . " " . $datos['division'];
        $header_orientacion = "Ciclo Lectivo " . $datos['anio']; // O ajusta según tu lógica
        $ciclo_activo = $datos['anio'];
    }
}

// Configuración de títulos según página
switch ($seccion_actual) {
    case 'alum_actividades': 
        $header_titulo = "Mis Actividades y Evaluaciones";
        $mostrar_filtros = true;
        break;
    case 'alum_calificaciones': 
        $header_titulo = "Calificaciones";
        $mostrar_filtros = true;
        break;
    case 'alum_alertas':
        $header_titulo = "Alertas";
        break;
    case 'alum_inicio':
    case 'inicio':
    default:
        $header_titulo = "Bienvenido al Sistema";
        break;
}

$filtro_activo = $_GET['cuatrimestre'] ?? 'todas';
?>

<link rel="stylesheet" href="css/topbar.css">

<header class="top-header">
    <div class="header-left">
<!-- Agrega esto en tu topbar.php en la sección izquierda -->
<button class="menu-toggle" id="menuToggle" aria-label="Abrir menú de navegación">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 12h18M3 6h18M3 18h18" stroke-linecap="round"/>
    </svg>
</button>
        
        <div class="header-title-block">
            <h1 class="header-title"><?= htmlspecialchars($header_titulo) ?></h1>
            <div class="header-breadcrumb">
                <span class="breadcrumb-year"><?= htmlspecialchars($header_curso) ?></span>
                <span class="breadcrumb-sep">•</span>
                <span class="breadcrumb-division"><?= htmlspecialchars($header_orientacion) ?></span>
            </div>
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-meta">
            <span class="ciclo-badge">Ciclo <?= htmlspecialchars($ciclo_activo) ?></span>
            
            <?php if ($mostrar_filtros): ?>
                <div class="filter-tabs">
                    <a href="?p=<?= $seccion_actual ?>&cuatrimestre=todas" class="tab-btn <?= $filtro_activo === 'todas' ? 'active' : '' ?>">Todas</a>
                    <a href="?p=<?= $seccion_actual ?>&cuatrimestre=1" class="tab-btn <?= $filtro_activo === '1' ? 'active' : '' ?>">1° Cuatrimestre</a>
                    <a href="?p=<?= $seccion_actual ?>&cuatrimestre=2" class="tab-btn <?= $filtro_activo === '2' ? 'active' : '' ?>">2° Cuatrimestre</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>