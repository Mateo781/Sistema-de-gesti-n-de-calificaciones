<main class="main">
  <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>
  <header class="topbar">
    <div class="topbar-left">
      <button class="hamburger" id="hamburger" aria-label="Menú">
        <span></span><span></span><span></span>
      </button>
    </div>

    <div class="header-meta">
      <span class="ciclo-badge">Ciclo 2026</span>
    </div>
  </header>

  <div class="content">
    <div class="main-grid">
      
      <section class="table-card">
        
        <div class="table-card-header">
          <div>
            <h2 class="table-card-title">Historial de evaluaciones</h2>
            <span class="table-card-count" id="rowCount">Cargando…</span>
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
                  Fecha
                  <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none">
                    <path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/>
                  </svg>
                </th>
                <th>Cuatrimestre</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody id="evalBody">
              <tr>
                <td colspan="6" class="td-loading">Cargando datos…</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-footer">
          <span class="footer-note" id="footerNote"></span>
          <div class="pagination" id="pagination"></div>
        </div>

      </section>
    </div>
  </div>
</main>