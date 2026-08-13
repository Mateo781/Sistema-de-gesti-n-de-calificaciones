<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['usuario_rol'] ?? 0) != 3) {
    die("Acceso denegado.");
}

require_once __DIR__ . '/../../php/db.php';

$id_alumno = $_SESSION['usuario_id'] ?? 0;

try {
    $sql = "SELECT ciclo, estado, materia, promedio_final, fecha_actualizacion
            FROM v_estado_academico
            WHERE id_alumno = :id_alumno
            ORDER BY ciclo ASC, materia ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_alumno' => $id_alumno]);
    $registros = $stmt->fetchAll();

    $trayectoria = [];
    foreach ($registros as $reg) {
        $anio = $reg['ciclo'];
        
        $estado_crudo = $reg['estado'];
        $estado_general = 'Pendiente';
        
        if ($estado_crudo === 'Aprobada') {
            $estado_general = 'Aprobado';
        } elseif ($estado_crudo === 'En proceso') {
            $estado_general = 'En proceso';
        } elseif ($estado_crudo === 'No aprobada') {
            $estado_general = 'Con deuda';
        }

        if (!isset($trayectoria[$anio])) {
            $trayectoria[$anio] = [
                'anio' => $anio . "º Ciclo",
                'estado_general' => $estado_general,
                'materias' => []
            ];
        }
        
        if ($estado_general === 'Con deuda') {
            $trayectoria[$anio]['estado_general'] = 'Con deuda';
        } elseif ($estado_general === 'En proceso' && $trayectoria[$anio]['estado_general'] !== 'Con deuda') {
            $trayectoria[$anio]['estado_general'] = 'En proceso';
        }

        $trayectoria[$anio]['materias'][] = [
            'materia' => $reg['materia'],
            'rite' => $reg['estado'],
            'nota' => $reg['promedio_final'] !== null ? $reg['promedio_final'] : '—',
            'observacion' => 'Última actualización: ' . date('d/m/Y', strtotime($reg['fecha_actualizacion'])),
            'docente' => 'Docente Asignado'
        ];
    }
} catch (PDOException $e) {
    die("Error al cargar la trayectoria: " . $e->getMessage());
}
?>

<main class="main-content">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>
  <div class="demo-wrap">
    <div class="timeline-scroll-wrapper" id="timelineWrapper">
      <div class="timeline-track" id="timelineTrack" style="display: flex; justify-content: flex-start; align-items: center; padding: 20px 10px;">
        <?php if (empty($trayectoria)): ?>
            <p style="padding: 20px; color: #64748b;">No se encontraron ciclos registrados para tu usuario.</p>
        <?php else: ?>
            <?php foreach ($trayectoria as $cicloAnio => $infoAnio): 
                $clase_nodo = 'gray';
                $estado = strtolower($infoAnio['estado_general']);
                if (strpos($estado, 'aprobado') !== false) $clase_nodo = 'green';
                elseif (strpos($estado, 'proceso') !== false) $clase_nodo = 'yellow';
                elseif (strpos($estado, 'deuda') !== false) $clase_nodo = 'red';
            ?>
                <div class="timeline-node" onclick="mostrarDetalleAnio('<?= $cicloAnio ?>')" style="cursor: pointer; margin: 0 25px; text-align: center; min-width: 80px;">
                    <div class="node-dot <?= $clase_nodo ?>" style="width: 22px; height: 22px; border-radius: 50%; margin: 0 auto 8px; box-shadow: 0 0 0 4px #fff, 0 2px 4px rgba(0,0,0,0.1);"></div>
                    <span class="node-label" style="font-weight: 600; font-size: 14px; color: #334155; display: block;"><?= $cicloAnio ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="timeline-legend">
      <span class="legend-item"><span class="legend-dot green"></span>Aprobado</span>
      <span class="legend-item"><span class="legend-dot yellow"></span>En proceso</span>
      <span class="legend-item"><span class="legend-dot red"></span>Con deuda</span>
      <span class="legend-item"><span class="legend-dot gray"></span>Pendiente</span>
    </div>

    <div class="detail-panel" id="detailPanel">
      <div class="detail-header">
        <div>
          <div class="detail-title" id="detailTitle">Seleccioná un ciclo lectivo</div>
          <div class="detail-sub" id="detailSub">Hacé clic en los años de la línea de tiempo superior para desglosar tus materias.</div>
        </div>
        <span class="badge-status" id="detailBadge"></span>
      </div>
      <div class="detail-table-wrap">
        <table class="detail-table">
          <thead>
            <tr>
              <th>Materia</th>
              <th>Estado RITE</th>
              <th>Calificación Final</th>
              <th>Observaciones</th>
            </tr>
          </thead>
          <tbody id="detailBody">
            <tr><td colspan="4" class="empty-state" style="text-align: center; color: #64748b; padding: 30px;">Seleccioná un año para ver el detalle.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<script>
const datosTrayectoria = <?= json_encode($trayectoria, JSON_UNESCAPED_UNICODE) ?>;

function mostrarDetalleAnio(anioKey) {
    const info = datosTrayectoria[anioKey];
    if (!info) return;

    document.getElementById('detailTitle').innerText = "Ciclo Lectivo " + anioKey;
    document.getElementById('detailSub').innerText = "Rendimiento general: " + info.estado_general;
    
    const badge = document.getElementById('detailBadge');
    badge.innerText = info.estado_general;
    
    let claseBadge = 'pendiente';
    if(info.estado_general === 'Aprobado') claseBadge = 'aprobado';
    if(info.estado_general === 'En proceso') claseBadge = 'en-proceso';
    if(info.estado_general === 'Con deuda') claseBadge = 'con-deuda';
    badge.className = 'badge-status ' + claseBadge;

    const tbody = document.getElementById('detailBody');
    tbody.innerHTML = '';

    info.materias.forEach(mat => {
        let claseRite = 'gray';
        if (mat.rite === 'Aprobada') claseRite = 'green';
        if (mat.rite === 'En proceso') claseRite = 'yellow';
        if (mat.rite === 'No aprobada') claseRite = 'red';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${escapeHtml(mat.materia)}</strong></td>
            <td><span class="legend-dot ${claseRite}"></span> <span>${escapeHtml(mat.rite)}</span></td>
            <td><span style="font-weight: bold; font-size: 1.1em;">${escapeHtml(String(mat.nota))}</span></td>
            <td>
                <small style="color: #64748b; display: block;">${escapeHtml(mat.observacion)}</small>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function escapeHtml(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>