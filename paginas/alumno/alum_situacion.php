<main class="main-content main">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>

  <!-- ═══ CONTENT ═══ -->
  <div class="content">

    <!-- OVERVIEW STATS -->
    <section class="stat-cards" aria-label="Resumen de Trayectoria">
      
      <div class="stat-card" data-accent="blue">
        <div class="stat-icon blue">
          <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><path d="M10 2l2.2 4.5 5 .73-3.6 3.52.85 4.97L10 13.25l-4.45 2.47.85-4.97L2.8 7.23l5-.73L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-body">
          <span class="stat-label">Promedio Acumulado</span>
          <span class="stat-value" id="promedioAcumulado">7.6</span>
          <span class="stat-hint">Historial completo</span>
        </div>
        <div class="stat-bar" style="--c:var(--accent)"></div>
      </div>

      <div class="stat-card" data-accent="green">
        <div class="stat-icon green">
          <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><path d="M4 10.5l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <div class="stat-body">
          <span class="stat-label">Aprobadas / Totales</span>
          <span class="stat-value" id="aprobadasTotales">8 / 12</span>
          <span class="stat-hint">Ciclo lectivo actual</span>
        </div>
        <div class="stat-bar" style="--c:var(--green)"></div>
      </div>

      <div class="stat-card" data-accent="amber">
        <div class="stat-icon amber">
          <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><rect x="3" y="4" width="14" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14" stroke="currentColor" stroke-width="1.3"/><path d="M7 2v3M13 2v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-body">
          <span class="stat-label">Instancias Activas</span>
          <span class="stat-value" id="instanciasActivas">1</span>
          <span class="stat-hint">Intensificación dic 2025</span>
        </div>
        <div class="stat-bar" style="--c:var(--amber)"></div>
      </div>

      <div class="stat-card" data-accent="red">
        <div class="stat-icon red">
          <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="7" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="13.5" r="1.1" fill="currentColor"/></svg>
        </div>
        <div class="stat-body">
          <span class="stat-label">Materias Adeudadas</span>
          <span class="stat-value" id="materiasDeuda">3</span>
          <span class="stat-hint">Años anteriores</span>
        </div>
        <div class="stat-bar" style="--c:var(--red)"></div>
      </div>

    </section>

    <!-- CYCLE NAVIGATION TABS -->
    <div class="cycle-tabs-container">
      <h2 class="section-title-small">Seleccionar Trayectoria Anual</h2>
      <div class="cycle-tabs" id="cycleTabs">
        <!-- Generado por JS: Pestañas para 1°, 2°, 3°, 4°, 5°, 6°, 7° año -->
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="main-grid">

      <!-- DETAIL SECTION -->
      <section class="table-card">
        <div class="table-card-header">
          <div>
            <h2 class="table-card-title" id="selectedCycleTitle">Detalle de Calificaciones</h2>
            <span class="table-card-count" id="selectedCycleSubtitle">Cargando...</span>
          </div>
          <span class="badge-status-cycle" id="cycleStatusBadge">Regular</span>
        </div>

        <div class="table-wrap">
          <table class="eval-table" id="trajectoryTable">
            <thead>
              <tr>
                <th>Asignatura / Materia</th>
                <th>Estado RITE</th>
                <th>Calificación Final</th>
                <th>Condición Académica</th>
                <th>Observaciones / Docente</th>
              </tr>
            </thead>
            <tbody id="trajectoryBody">
              <tr><td colspan="5" class="td-loading">Cargando trayectoria académica...</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- SIDE PANEL -->
      <aside class="right-col">

        <!-- INTENSIFICACIONES Y RECURSADAS -->
        <div class="side-card">
          <div class="side-card-header">
            <h3 class="side-card-title">Instancias de Recuperación</h3>
          </div>
          <div id="recoveryList" class="recovery-list">
            <!-- Cargando... -->
          </div>
        </div>

        <!-- RECOMENDACIONES PEDAGÓGICAS -->
        <div class="side-card recommendation-card">
          <div class="side-card-header">
            <h3 class="side-card-title">Recomendaciones RITE</h3>
          </div>
          <div class="recommendation-content">
            <p>De acuerdo con el nuevo régimen institucional de trayectorias (RITE):</p>
            <ul>
              <li>Las materias <strong>"En proceso"</strong> deberán completarse en el período de intensificación inmediato.</li>
              <li>Las materias con promedio inferior a 4 o abandonadas entran en período de <strong>Recursada</strong>.</li>
            </ul>
          </div>
        </div>

      </aside>
    </div><!-- /main-grid -->

  </div><!-- /content -->
</main>
