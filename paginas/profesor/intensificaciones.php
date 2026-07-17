<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de acceso: Rol 2 = Docente
if (($_SESSION['usuario_rol'] ?? 0) != 2) {
    die("Acceso denegado. Este módulo es exclusivo para docentes.");
}

require_once __DIR__ . '/../../php/db.php';
require_once __DIR__ . '/../../php/profesor/funciones_calificaciones.php';

$id_docente = $_SESSION['usuario_id'] ?? null;
if (!$id_docente) {
    die("Error: Sesión docente no válida.");
}

// Cargar cursos y materias del docente
$cursos = obtenerCursosDocente($pdo, $id_docente);

// Parámetros seleccionados
$curso_sel = intval($_GET['id_curso'] ?? 0);
$materia_sel = intval($_GET['id_materia'] ?? 0);

$materias = [];
if ($curso_sel > 0) {
    $materias = obtenerMateriasCurso($pdo, $id_docente, $curso_sel);
}

// Encontrar id_curso_materia correspondiente
$id_cmd_actual = 0;
if ($curso_sel > 0 && $materia_sel > 0) {
    foreach ($materias as $m) {
        if ($m['id_curso_materia'] == $materia_sel) {
            $id_cmd_actual = $m['id_curso_materia'];
            break;
        }
    }
}

$mensaje_ok = "";
$mensaje_err = "";

// Procesar el guardado si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_trayectoria'])) {
    $id_alumno = intval($_POST['id_alumno'] ?? 0);
    $id_estado = intval($_POST['id_estado_materia'] ?? 0);
    $promedio = $_POST['promedio_final'] !== '' ? floatval($_POST['promedio_final']) : null;
    $observaciones = trim($_POST['observaciones'] ?? '');
    
    // Opciones extras
    $registrar_intensificacion = isset($_POST['chk_intensificacion']);
    $id_periodo_int = intval($_POST['id_periodo_int'] ?? 0);
    $motivo_int = trim($_POST['motivo_int'] ?? '');
    
    $registrar_recursada = isset($_POST['chk_recursada']);
    $motivo_rec = trim($_POST['motivo_rec'] ?? '');

    if ($id_alumno > 0 && $id_cmd_actual > 0 && $id_estado > 0) {
        $resultado = guardarTrayectoriaRite(
            $pdo,
            $id_docente,
            $id_alumno,
            $id_cmd_actual,
            $id_estado,
            $promedio,
            $observaciones,
            $registrar_intensificacion,
            $id_periodo_int,
            $motivo_int,
            $registrar_recursada,
            $motivo_rec,
            $curso_sel
        );
        if ($resultado['success']) {
            $mensaje_ok = $resultado['mensaje'];
        } else {
            $mensaje_err = $resultado['mensaje'];
        }
    } else {
        $mensaje_err = "Datos incompletos para actualizar la trayectoria.";
    }
}

// Cargar alumnos y sus trayectorias para el curso/materia seleccionado
$alumnos = [];
$stats = [
    'total' => 0,
    'aprobados' => 0,
    'proceso' => 0,
    'no_aprobados' => 0
];

if ($curso_sel > 0 && $id_cmd_actual > 0) {
    // Obtener id de la materia
    $stmtMat = $pdo->prepare("SELECT id_materia FROM curso_materia_docente WHERE id = :cmd");
    $stmtMat->execute([':cmd' => $id_cmd_actual]);
    $id_materia = $stmtMat->fetchColumn();

    if ($id_materia) {
        $alumnos = obtenerAlumnosTrayectoria($pdo, $curso_sel, $id_cmd_actual, $id_materia);

        // Calcular estadísticas
        $stats['total'] = count($alumnos);
        foreach ($alumnos as $al) {
            if ($al['id_estado_materia'] == 1) {
                $stats['aprobados']++;
            } elseif ($al['id_estado_materia'] == 2) {
                $stats['proceso']++;
            } elseif ($al['id_estado_materia'] == 3) {
                $stats['no_aprobados']++;
            } else {
                $stats['proceso']++;
            }
        }
    }
}

// Cargar periodos de intensificación para el formulario
$periodos_int = $pdo->query("SELECT id, nombre FROM periodos WHERE tipo = 'intensificacion'")->fetchAll(PDO::FETCH_ASSOC);
if (empty($periodos_int)) {
    $periodos_int = [
        ['id' => 1, 'nombre' => 'Período Intensificación Diciembre'],
        ['id' => 2, 'nombre' => 'Período Intensificación Febrero']
    ];
}
?>

<main class="main-content main">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>

  <div class="content-body">
    
    <!-- ═══ FILTROS DE SELECCIÓN ═══ -->
    <section class="table-card" style="padding: 24px;">
      <h2 class="table-card-title" style="margin-bottom: 16px;">Selección de Curso y Materia</h2>
      <form method="GET" action="index.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <input type="hidden" name="p" value="prof_intensificaciones">
        
        <div style="display: flex; flex-direction: column; gap: 6px;">
          <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">1. Seleccionar Curso</label>
          <select name="id_curso" onchange="this.form.submit()" class="filter-select" style="padding: 10px; border: 1px solid var(--border); border-radius: var(--r-md); background: var(--card); font-family: inherit; font-size: 13px;">
            <option value="">-- Seleccionar Curso --</option>
            <?php foreach ($cursos as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $curso_sel == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre'] . " " . $c['division'] . " (" . $c['anio_escolar'] . "° Año)") ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
          <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">2. Seleccionar Materia</label>
          <select name="id_materia" onchange="this.form.submit()" class="filter-select" style="padding: 10px; border: 1px solid var(--border); border-radius: var(--r-md); background: var(--card); font-family: inherit; font-size: 13px;" <?= empty($materias) ? 'disabled' : '' ?>>
            <option value="">-- Seleccionar Materia --</option>
            <?php foreach ($materias as $m): ?>
              <option value="<?= $m['id_curso_materia'] ?>" <?= $materia_sel == $m['id_curso_materia'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['materia_nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </section>

    <!-- Si hay curso y materia seleccionados, mostramos las estadísticas y la grilla -->
    <?php if ($curso_sel > 0 && $id_cmd_actual > 0): ?>
      
      <!-- ═══ TARJETAS RESUMEN (ESTILO ALUM_INICIO) ═══ -->
      <section class="stat-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="stat-card" data-accent="blue">
          <div class="stat-icon blue">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M9 12l2 2 4-4M9 21h6a2 2 0 002-2V7l-5-5H5a2 2 0 00-2 2v12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">Total Alumnos</span>
            <span class="stat-value"><?= $stats['total'] ?></span>
            <span class="stat-hint">Inscriptos activos</span>
          </div>
          <div class="stat-bar" style="background:var(--accent); height:3px; margin: 0 -18px;"></div>
        </div>

        <div class="stat-card" data-accent="green">
          <div class="stat-icon green">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">Trayectoria Aprobada</span>
            <span class="stat-value"><?= $stats['aprobados'] ?></span>
            <span class="stat-hint">Promedio >= 7.00</span>
          </div>
          <div class="stat-bar" style="background:var(--green); height:3px; margin: 0 -18px;"></div>
        </div>

        <div class="stat-card" data-accent="amber">
          <div class="stat-icon amber">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="7" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="13.5" r="1" fill="currentColor"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">En Proceso (RITE)</span>
            <span class="stat-value"><?= $stats['proceso'] ?></span>
            <span class="stat-hint">Requieren Intensificación</span>
          </div>
          <div class="stat-bar" style="background:var(--amber); height:3px; margin: 0 -18px;"></div>
        </div>

        <div class="stat-card" data-accent="red">
          <div class="stat-icon red">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><line x1="6" y1="6" x2="14" y2="14" stroke="currentColor" stroke-width="1.5"/><line x1="14" y1="6" x2="6" y2="14" stroke="currentColor" stroke-width="1.5"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">No Aprobados</span>
            <span class="stat-value"><?= $stats['no_aprobados'] ?></span>
            <span class="stat-hint">Deben recursar materia</span>
          </div>
          <div class="stat-bar" style="background:var(--red); height:3px; margin: 0 -18px;"></div>
        </div>
      </section>

      <!-- Mensajes de Estado POST -->
      <?php if (!empty($mensaje_ok)): ?>
        <div style="background: #eaf6ef; color: #1f8c45; border: 1px solid #d1ebd9; padding: 14px 18px; border-radius: var(--r-md); font-size: 13.5px; font-weight: 500;"><?= $mensaje_ok ?></div>
      <?php endif; ?>
      <?php if (!empty($mensaje_err)): ?>
        <div style="background: #fdecea; color: #c0302b; border: 1px solid #f9d2ce; padding: 14px 18px; border-radius: var(--r-md); font-size: 13.5px; font-weight: 500;"><?= $mensaje_err ?></div>
      <?php endif; ?>

      <!-- ═══ GRID CONTENIDO SECUNDARIO ═══ -->
      <div class="main-grid">
        
        <!-- Tabla de Alumnos (Izquierda) -->
        <section class="table-card" style="padding: 24px;">
          <h3 class="table-card-title" style="margin-bottom: 16px;">Calificaciones Finales y Estados RITE</h3>
          <div class="table-wrap" style="overflow-x: auto;">
            <table class="detail-table" style="width: 100%; border-collapse: collapse; text-align: left;">
              <thead>
                <tr style="border-bottom: 2px solid var(--border); font-size: 12px; text-transform: uppercase; color: var(--t3); letter-spacing: 0.5px;">
                  <th style="padding: 12px 8px;">Alumno</th>
                  <th style="padding: 12px 8px;">DNI</th>
                  <th style="padding: 12px 8px; text-align: center;">Promedio</th>
                  <th style="padding: 12px 8px; text-align: center;">Estado RITE</th>
                  <th style="padding: 12px 8px; text-align: center;">Observaciones</th>
                  <th style="padding: 12px 8px; text-align: center;">Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($alumnos as $al): ?>
                  <?php 
                    $estado_label = $al['estado_materia'] ?: 'Cursando';
                    $estado_class = 'badge-pendiente';
                    if ($al['id_estado_materia'] == 1) $estado_class = 'badge-aprobada';
                    elseif ($al['id_estado_materia'] == 2) $estado_class = 'badge-proceso';
                    elseif ($al['id_estado_materia'] == 3) $estado_class = 'badge-desaprobada';

                    $promedio_fmt = $al['promedio_final'] !== null ? number_format($al['promedio_final'], 2) : '—';
                  ?>
                  <tr style="border-bottom: 1px solid var(--border-soft); font-size: 13.5px;" class="student-row" data-id="<?= $al['id_alumno'] ?>">
                    <td style="padding: 14px 8px;"><strong><?= htmlspecialchars($al['apellido'] . ", " . $al['nombre']) ?></strong></td>
                    <td style="padding: 14px 8px; color: var(--t2);"><?= htmlspecialchars($al['dni']) ?></td>
                    <td style="padding: 14px 8px; text-align: center; font-weight: 700;"><?= $promedio_fmt ?></td>
                    <td style="padding: 14px 8px; text-align: center;">
                      <span class="badge <?= $estado_class ?>"><?= $estado_label ?></span>
                    </td>
                    <td style="padding: 14px 8px; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--t2);" title="<?= htmlspecialchars($al['observaciones'] ?? '') ?>">
                      <?= htmlspecialchars($al['observaciones'] ?? 'Sin observaciones') ?>
                    </td>
                    <td style="padding: 14px 8px; text-align: center;">
                      <button type="button" class="btn-action-edit" 
                              data-id="<?= $al['id_alumno'] ?>"
                              data-nombre="<?= htmlspecialchars($al['nombre'] . ' ' . $al['apellido']) ?>"
                              data-estado="<?= $al['id_estado_materia'] ?: 2 ?>"
                              data-promedio="<?= $al['promedio_final'] ?>"
                              data-obs="<?= htmlspecialchars($al['observaciones'] ?? '') ?>"
                              data-int="<?= $al['tiene_intensificacion'] ?>"
                              data-rec="<?= $al['tiene_recursada'] ?>"
                              style="padding: 6px 12px; background: var(--accent-light); color: var(--accent); border: none; border-radius: var(--r-sm); font-size: 11.5px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Evaluar / RITE
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Panel Lateral Informativo (Derecha) -->
        <aside class="right-panel-custom" style="display: flex; flex-direction: column; gap: 20px;">
          <section class="table-card" style="padding: 20px; background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg);">
            <h3 class="panel-title" style="font-size: 14px; font-weight: 700; color: var(--t1); margin-bottom: 12px;">Instrucciones del Régimen</h3>
            <div style="font-size: 12px; line-height: 1.6; color: var(--t2); display: flex; flex-direction: column; gap: 10px;">
              <p>El régimen institucional vigente (RITE) define tres trayectorias posibles:</p>
              <ul style="padding-left: 16px;">
                <li><strong style="color:var(--green)">Aprobada:</strong> Alumno con promedio final >= 7.</li>
                <li><strong style="color:var(--amber)">En Proceso:</strong> Alumno con promedio < 7. Debe realizar el período de intensificación en las fechas asignadas.</li>
                <li><strong style="color:var(--red)">No Aprobada:</strong> Alumno que ha reprobado la intensificación o adeuda la materia de manera definitiva. Debe recursar.</li>
              </ul>
              <p>Haciendo clic en <strong>Evaluar / RITE</strong> podés asentar las calificaciones de períodos de intensificación o habilitar recursadas para cada estudiante.</p>
            </div>
          </section>

          <section class="table-card" style="padding: 20px; background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg);">
            <h3 class="panel-title" style="font-size: 14px; font-weight: 700; color: var(--t1); margin-bottom: 12px;">Períodos de Intensificación</h3>
            <div style="font-size: 12px; line-height: 1.6; color: var(--t2); display: flex; flex-direction: column; gap: 8px;">
              <?php foreach ($periodos_int as $p_int): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: var(--page); border-radius: var(--r-sm);">
                  <span><?= htmlspecialchars($p_int['nombre']) ?></span>
                  <span class="badge badge-intensificacion" style="font-size:9px;">Activo</span>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        </aside>

      </div>

    <?php else: ?>
      <!-- Estado vacío si no seleccionó curso y materia -->
      <section class="table-card" style="padding: 60px; text-align: center; color: var(--t2);">
        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 16px; display: block; color: var(--t3);">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p style="font-weight: 600; font-size: 15px; margin-bottom: 4px;">Por favor, seleccioná un curso y una materia.</p>
        <p style="font-size: 13px; color: var(--t3);">Seleccioná las opciones en el buscador superior para comenzar a administrar la situación de los alumnos.</p>
      </section>
    <?php endif; ?>

  </div>
</main>

<!-- ═══ MODAL DE EVALUACIÓN RITE / INTENSIFICACIÓN ═══ -->
<div class="modal-overlay" id="riteModal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title">Evaluar Estudiante — RITE</h3>
      <button class="btn-close-modal" id="btnCloseModal">&times;</button>
    </div>
    
    <form method="POST" action="">
      <input type="hidden" name="id_alumno" id="modalAlumnoId">
      <input type="hidden" name="guardar_trayectoria" value="1">
      
      <div class="modal-body">
        <div style="margin-bottom: 14px;">
          <label class="modal-label">Estudiante</label>
          <input type="text" id="modalAlumnoNombre" class="modal-input" readonly style="background: var(--border-soft); font-weight: bold;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
          <div>
            <label class="modal-label">Estado RITE</label>
            <select name="id_estado_materia" id="modalEstadoRITE" class="modal-input" required>
              <option value="1">Aprobada</option>
              <option value="2">En proceso</option>
              <option value="3">No aprobada</option>
            </select>
          </div>
          <div>
            <label class="modal-label">Calificación Final</label>
            <input type="number" step="0.01" min="1" max="10" name="promedio_final" id="modalPromedio" class="modal-input" placeholder="ej. 7.50">
          </div>
        </div>

        <div style="margin-bottom: 14px;">
          <label class="modal-label">Observaciones Pedagógicas</label>
          <textarea name="observaciones" id="modalObservaciones" class="modal-textarea" rows="3" placeholder="Ingresá detalles de la trayectoria del alumno..."></textarea>
        </div>

        <!-- Opciones pedagógicas especiales -->
        <div style="border-top: 1px solid var(--border); padding-top: 14px; margin-top: 14px; display: flex; flex-direction: column; gap: 12px;">
          
          <!-- Intensificación -->
          <div>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: bold; font-size: 13px; cursor: pointer;">
              <input type="checkbox" name="chk_intensificacion" id="modalChkInt" style="width: 16px; height: 16px;">
              <span>Asignar Período de Intensificación</span>
            </label>
            <div id="modalIntFields" style="margin-top: 8px; padding-left: 24px; display: none; flex-direction: column; gap: 8px;">
              <div>
                <label class="modal-label">Período de Intensificación</label>
                <select name="id_periodo_int" class="modal-input">
                  <?php foreach ($periodos_int as $p_int): ?>
                    <option value="<?= $p_int['id'] ?>"><?= htmlspecialchars($p_int['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="modal-label">Motivo / Temas a Intensificar</label>
                <input type="text" name="motivo_int" class="modal-input" placeholder="ej. Temas de derivadas e integrales">
              </div>
            </div>
          </div>

          <!-- Recursada -->
          <div>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: bold; font-size: 13px; cursor: pointer;">
              <input type="checkbox" name="chk_recursada" id="modalChkRec" style="width: 16px; height: 16px;">
              <span style="color: var(--red);">Marcar para Recursada Completa</span>
            </label>
            <div id="modalRecFields" style="margin-top: 8px; padding-left: 24px; display: none;">
              <label class="modal-label">Motivo de Recursada</label>
              <input type="text" name="motivo_rec" class="modal-input" placeholder="ej. No aprobó instancias de intensificación">
            </div>
          </div>

        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn-secondary" id="btnCancelModal" style="padding: 10px 16px; border: 1px solid var(--border); border-radius: var(--r-md); background: var(--card); font-size: 13px; font-weight: 600; cursor: pointer;">Cancelar</button>
        <button type="submit" class="btn-primary" style="padding: 10px 18px; border: none; border-radius: var(--r-md); background: var(--accent); color: white; font-size: 13px; font-weight: 600; cursor: pointer;">Guardar Trayectoria</button>
      </div>
    </form>
  </div>
</div>

