<?php
require_once __DIR__ . "/../../php/db.php";
require_once __DIR__ . '/../../php/funciones_calificaciones.php';

// Obtener el ID del docente
$id_docente = $_SESSION['usuario_id'] ?? null;

if (!$id_docente) {
    echo "<p>Error: Sesión docente no válida.</p>";
    exit;
}

$cursos = obtenerCursosDocente($pdo, $id_docente);
?>

<main class="main-content main" id="vistaCalificaciones" data-id-docente="<?= htmlspecialchars($id_docente) ?>">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>

  <div class="content" style="padding: 24px;">

    <div id="mensajeEstado" style="margin-bottom: 16px;"></div>

    <!-- SECCIÓN DE SELECCIÓN DE DESTINO (Filtros intactos) -->
    <section class="table-card" style="background: var(--bg-card, #fff); border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <h2 class="table-card-title" style="margin-bottom: 16px; font-size: 18px; font-weight: 600; color: var(--t1, #333);">Selección de Destino</h2>
        <div id="formFiltros" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2, #666);">Curso disponible</label>
                <select id="filtroCurso" name="filtro_curso" class="filter-select" style="padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border-color, #ccc); background: var(--bg-input, #fff); font-size: 14px;">
                    <option value="">-- Seleccionar Curso --</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nombre'] . " " . $c['division'] . " (" . $c['anio_escolar'] . "° Año)") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2, #666);">Materia asignada</label>
                <select id="filtroMateria" name="filtro_materia" class="filter-select" disabled style="padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border-color, #ccc); background: var(--bg-input, #fff); font-size: 14px;">
                    <option value="">-- Seleccionar Materia --</option>
                </select>
            </div>
        </div>
    </section>

    <!-- CONTENEDOR DE LA PLANILLA DE ALUMNOS CON LOS 5 CRITERIOS -->
    <div id="contenedorAlumnos">
        <div class="table-card" style="background: var(--bg-card, #fff); border-radius: 12px; padding: 40px; text-align: center; color: var(--t2, #666); border: 2px dashed rgba(0,0,0,0.08); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <p style="margin: 0; font-size: 15px;">Por favor, seleccione un Curso y una Materia para desplegar la planilla de alumnos.</p>
        </div>
    </div>

  </div>
</main>