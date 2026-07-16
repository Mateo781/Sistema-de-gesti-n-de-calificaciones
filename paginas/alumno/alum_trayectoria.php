<main class="main-content">

  <header class="top-header">
    <div class="header-left">
      <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
        <svg viewBox="0 0 20 20" width="20" height="20"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <div class="header-title-block">
        <h1 class="header-title">Trayectoria Educativa</h1>
        <div class="header-breadcrumb">
          <span class="breadcrumb-year">7° 2° Grupo B</span>
          <span class="breadcrumb-sep">•</span>
          <span class="breadcrumb-division">Orientación Programacion</span>
        </div>
      </div>
    </div>
    <div class="header-right">
      <div class="header-meta">
        <span class="ciclo-badge">Ciclo 2026</span>
      </div>
    </div>
  </header>

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
