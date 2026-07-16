<?php
require_once __DIR__ . "/../../php/db.php";
require_once __DIR__ . '/../../php/profesor/funciones_calificaciones.php';

// Obtener el ID del docente
$id_docente = $_SESSION['usuario_id'] ?? null;

if (!$id_docente) {
    echo "<p>Error: Sesión docente no válida.</p>";
    exit;
}

$cursos = obtenerCursosDocente($pdo, $id_docente);
?>

<main class="main">
  <header class="topbar">
    <div class="topbar-left">
      <div class="header-title-block">
        <h1 class="header-title">Registro de Calificaciones</h1>
        <div class="header-breadcrumb">
          <span class="breadcrumb-year">Panel Docente</span>
          <span class="breadcrumb-sep">•</span>
          <span class="breadcrumb-division">Ingreso de Notas</span>
        </div>
      </div>
    </div>
  </header>

  <div class="content">

    <!-- Acá se inyectan los mensajes de éxito/error que antes se mostraban con PHP -->
    <div id="mensajeEstado"></div>

    <section class="table-card" style="padding: 20px; margin-bottom: 20px;">
        <h2 class="table-card-title" style="margin-bottom: 14px;">Selección de Destino</h2>
        <div id="formFiltros" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Curso disponible</label>
                <select id="filtroCurso" name="filtro_curso" class="filter-select">
                    <option value="">-- Seleccionar Curso --</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nombre'] . " " . $c['division'] . " (" . $c['anio_escolar'] . "° Año)") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Materia asignada</label>
                <select id="filtroMateria" name="filtro_materia" class="filter-select" disabled>
                    <option value="">-- Seleccionar Materia --</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Acá se inyecta la tabla de alumnos vía AJAX (ajax/obtener_alumnos.php) -->
    <div id="contenedorAlumnos">
        <div class="table-card" style="padding: 40px; text-align: center; color: var(--t2); border: 2px dashed rgba(0,0,0,0.06);">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <p>Por favor, seleccione un Curso y una Materia para desplegar la planilla de alumnos.</p>
        </div>
    </div>

  </div>
</main>

<script src="js/profesor/profe_calificaciones.js"></script>