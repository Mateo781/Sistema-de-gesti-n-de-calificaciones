<main class="main-content">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>

  <div class="demo-wrap">
    <div class="timeline-scroll-wrapper" id="timelineWrapper">
      <div class="timeline-track" id="timelineTrack"></div>
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
          <div class="detail-title" id="detailTitle">—</div>
          <div class="detail-sub" id="detailSub"></div>
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
              <th>Observaciones / Docente</th>
            </tr>
          </thead>
          <tbody id="detailBody">
            <tr><td colspan="4" class="empty-state">Seleccioná un año para ver el detalle.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
