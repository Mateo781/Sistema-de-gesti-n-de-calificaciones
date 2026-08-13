<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['usuario_rol'] ?? 0) != 3) {
    die("Acceso denegado. Este módulo es exclusivo para alumnos inscritos.");
}

require_once 'conexion.php';

$id_alumno = $_SESSION['usuario_id'] ?? 0;

$sql = "SELECT 
            m.nombre AS materia_nombre,
            te.nombre AS tipo_instancia,
            c.nota_numerica,
            c.nota_conceptual,
            ea.fecha_entrega,
            p.nombre AS periodo_nombre
        FROM entrega_alumnos ea
        INNER JOIN evaluaciones e ON ea.id_evaluacion = e.id
        INNER JOIN curso_materia_docente cmd ON e.id_curso_materia = cmd.id
        INNER JOIN materias m ON cmd.id_materia = m.id
        INNER JOIN tipos_evaluacion te ON e.id_tipo_evaluacion = te.id
        INNER JOIN periodos p ON e.id_periodo = p.id
        LEFT JOIN calificaciones c ON e.id = c.id_evaluacion AND c.id_alumno = ?
        WHERE ea.id_alumno = ?
        ORDER BY ea.fecha_entrega DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_alumno, $id_alumno);
$stmt->execute();
$resultado = $stmt->get_result();

$total_registros = $resultado->num_rows;
?>

<main class="main">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>
  <div class="content">
    <div class="main-grid">
      
      <section class="table-card">
        
        <div class="table-card-header">
          <div>
            <h2 class="table-card-title">Historial de evaluaciones</h2>
            <span class="table-card-count" id="rowCount"><?= $total_registros ?> entregas encontradas</span>
          </div>
          <div class="Cuatri-tabs">
            <button class="Cuatri-tab active" data-Cuatri="all">Todas</button>
            <button class="Cuatri-tab" data-Cuatri="1">1° Cuatrimestre</button>
            <button class="Cuatri-tab" data-Cuatri="2">2° Cuatrimestre</button>
          </div>
        </div>

        <div class="table-wrap">
          <table class="eval-table" id="evalTable">
            <thead>
              <tr>
                <th class="th-sortable" data-col="materia">
                  Materia
                  <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none">
                    <path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/>
                  </svg>
                </th>
                <th>Tipo</th>
                <th class="th-sortable" data-col="nota">
                  Nota
                  <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none">
                    <path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/>
                  </svg>
                </th>
                <th class="th-sortable" data-col="fecha">
                  Fecha Entrega
                  <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none">
                    <path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/>
                  </svg>
                </th>
                <th>Cuatrimestre</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody id="evalBody">
              <?php if ($total_registros === 0): ?>
                <tr>
                  <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">Aún no has realizado ninguna entrega en el sistema.</td>
                </tr>
              <?php else: ?>
                <?php while ($reg = $resultado->fetch_assoc()): 
                    $cuatri_num = "all";
                    $periodo_ui = $reg['periodo_nombre'];
                    if (stripos($periodo_ui, '1') !== false || stripos($periodo_ui, 'prim') !== false) {
                        $cuatri_num = "1";
                        $periodo_ui = "1° Cuatrimestre";
                    } elseif (stripos($periodo_ui, '2') !== false || stripos($periodo_ui, 'seg') !== false) {
                        $cuatri_num = "2";
                        $periodo_ui = "2° Cuatrimestre";
                    }

                    $tiene_nota = ($reg['nota_numerica'] !== null || !empty($reg['nota_conceptual']));
                ?>
                  <tr data-cuatrimestre="<?= $cuatri_num ?>">
                    <td><strong><?= htmlspecialchars($reg['materia_nombre']) ?></strong></td>
                    <td><span style="font-size: 13px; color: #475569;"><?= htmlspecialchars($reg['tipo_instancia']) ?></span></td>
                    
                    <td>
                      <?php if ($tiene_nota): ?>
                        <span style="font-weight: 700; color: #166534;">
                            <?= $reg['nota_numerica'] !== null ? number_format($reg['nota_numerica'], 1) : htmlspecialchars($reg['nota_conceptual']) ?>
                        </span>
                      <?php else: ?>
                        <span style="color: #94a3b8; font-style: italic; font-size: 13px;">—</span>
                      <?php endif; ?>
                    </td>
                    
                    <td><?= date('d/m/Y H:i', strtotime($reg['fecha_entrega'])) ?></td>
                    
                    <td><?= $periodo_ui ?></td>
                    
                    <td>
                      <?php if ($tiene_nota): ?>
                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; display: inline-block;">
                          Calificado
                        </span>
                      <?php else: ?>
                        <span style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; line-height: 1.2;">
                          🟢 Entregado<br><span style="font-size: 10px; font-weight: 400; color: #047857;">Esperando corrección</span>
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="table-footer">
          <span class="footer-note" id="footerNote">Mostrando los trabajos prácticos y exámenes enviados al profesor.</span>
          <div class="pagination" id="pagination"></div>
        </div>

      </section>
    </div>
  </div>
</main>

<?php 
$stmt->close(); 
?>