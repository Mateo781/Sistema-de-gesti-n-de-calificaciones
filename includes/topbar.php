<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$seccion_actual = $_GET['p'] ?? 'inicio';

$id_rol = $_SESSION['usuario_rol'] ?? null;

if ($id_rol == 2) {
    $header_curso = "Panel Docente";
    $header_orientacion = "Gestión Académica";
} else {
    $header_curso = $_SESSION['alumno_curso'] ?? "7° 2° Grupo B"; 
    $header_orientacion = $_SESSION['alumno_orientacion'] ?? "Orientación Programacion";
}
$header_titulo = "Panel de Control";
$mostrar_filtros = false; 

switch ($seccion_actual) {
    case 'prof_calificaciones':
        $header_titulo = "Registro de Calificaciones";
        $header_orientacion = "Ingreso de Notas";
        $mostrar_filtros = false;
        break;

    case 'prof_evaluaciones':
        $header_titulo = "Planificar Actividades";
        $header_orientacion = "Planificación";
        $mostrar_filtros = false;
        break;

    case 'alum_actividades': 
        $header_titulo = "Mis Actividades y Evaluaciones";
        $mostrar_filtros = true;
        break;
        
    case 'alum_calificaciones': 
        $header_titulo = "Calificaciones";
        $mostrar_filtros = true;
        break;

    case 'alum_inicio':
    case 'inicio':
        $header_titulo = "Bienvenido al Sistema";
        $mostrar_filtros = false;
        break;

    case 'alum_alertas':
        $header_titulo = "Alertas";
        $mostrar_filtros = false;
        break;

    case 'alum_situacion':
        $header_titulo = "Situación Académica";
        $mostrar_filtros = false;
        break;

    case 'trayectoria':
        $header_titulo = "Trayectoria Educativa";
        $mostrar_filtros = false;
        break;
}

$filtro_activo = $_GET['cuatrimestre'] ?? 'todas';
?>

<link rel="stylesheet" href="css/topbar.css">

<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <svg viewBox="0 0 20 20" width="20" height="20">
                <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
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
            <span class="ciclo-badge">Ciclo 2026</span>
            
            <?php if ($seccion_actual === 'alum_situacion'): ?>
                <button class="btn-export" id="btnExport">
                    <svg viewBox="0 0 16 16" width="13" height="13" fill="none"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M2.5 12h11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    <span>Descargar Analítico</span>
                </button>
            <?php endif; ?>

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