<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0f1f4b" />
  <title>RITE · Ver Calificaciones</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/calificaciones.css" />
</head>
<body>

<!-- ══════════════════════════════════════════
     PAGE LAYOUT
══════════════════════════════════════════ -->
<div class="page-layout">

  <!-- ── SIDEBAR ── -->
  <?php

  require "../partials/navbar.php";
  ?>

  <!-- ── MAIN ── -->
  <main class="main">

    <!-- TOP BAR -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="hamburger" id="hamburger" aria-label="Menú">
          <span></span><span></span><span></span>
        </button>
        <div class="page-heading">
          <h1 class="page-title">Calificaciones</h1>
          <div class="page-meta">
            <span class="meta-chip">7° 2° Grupo B</span>
            <span class="meta-dot">·</span>
            <span class="meta-text">Orientación Programacion</span>
            <span class="meta-dot">·</span>
            <span class="meta-text">Ciclo 2026</span>
          </div>
        </div>
      </div>
      <div class="topbar-right">
        <button class="btn-filter" id="btnFilter" aria-label="Filtrar">
          <svg viewBox="0 0 16 16" width="14" height="14" fill="none"><path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          Filtrar
        </button>
        <button class="btn-export" id="btnExport">
          <svg viewBox="0 0 16 16" width="13" height="13" fill="none"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M2.5 12h11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          <span>Exportar PDF</span>
        </button>
      </div>
    </header>

    <!-- ═══ CONTENT ═══ -->
    <div class="content">

      <!-- STAT CARDS -->
      <section class="stat-cards" aria-label="Resumen">
        <div class="stat-card" data-accent="blue">
          <div class="stat-icon blue">
            <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><path d="M10 2l2.2 4.5 5 .73-3.6 3.52.85 4.97L10 13.25l-4.45 2.47.85-4.97L2.8 7.23l5-.73L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">Promedio general</span>
            <span class="stat-value" id="statPromedio">7.4</span>
            <span class="stat-hint">Sobre 10 puntos</span>
          </div>
          <div class="stat-bar" style="--c:var(--accent)"></div>
        </div>

        <div class="stat-card" data-accent="green">
          <div class="stat-icon green">
            <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><path d="M4 10.5l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">Materias aprobadas</span>
            <span class="stat-value" id="statAprobadas">8</span>
            <span class="stat-hint">de 12 materias</span>
          </div>
          <div class="stat-bar" style="--c:var(--green)"></div>
        </div>

        <div class="stat-card" data-accent="red">
          <div class="stat-icon red">
            <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="7" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="13.5" r="1.1" fill="currentColor"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">Materias pendientes</span>
            <span class="stat-value" id="statPendientes">3</span>
            <span class="stat-hint">Requieren atención</span>
          </div>
          <div class="stat-bar" style="--c:var(--red)"></div>
        </div>

        <div class="stat-card" data-accent="amber">
          <div class="stat-icon amber">
            <svg viewBox="0 0 20 20" width="17" height="17" fill="none"><rect x="3" y="4" width="14" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14" stroke="currentColor" stroke-width="1.3"/><path d="M7 2v3M13 2v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          </div>
          <div class="stat-body">
            <span class="stat-label">Última evaluación</span>
            <span class="stat-value" style="font-size:18px;line-height:1.2" id="statUltEval">23 may</span>
            <span class="stat-hint">Química · Nota: 9</span>
          </div>
          <div class="stat-bar" style="--c:var(--amber)"></div>
        </div>
      </section>

      <!-- FILTER BAR -->
      <div class="filter-bar" id="filterBar">
        <div class="filter-search-wrap">
          <svg class="filter-search-ico" viewBox="0 0 16 16" width="14" height="14" fill="none"><circle cx="6.5" cy="6.5" r="4.5" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="10" x2="14" y2="14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          <input type="text" class="filter-input" id="searchInput" placeholder="Buscar materia…" />
        </div>
        <select class="filter-select" id="filterEstado">
          <option value="">Todos los estados</option>
          <option value="aprobada">Aprobada</option>
          <option value="proceso">En proceso</option>
          <option value="desaprobada">Desaprobada</option>
          <option value="intensificacion">Intensificación</option>
        </select>
        <select class="filter-select" id="filterTrimestre">
          <option value="">Todos los trimestres</option>
          <option value="1">1° Trimestre</option>
          <option value="2">2° Trimestre</option>
          <option value="3">3° Trimestre</option>
        </select>
        <button class="filter-reset" id="filterReset">Limpiar</button>
      </div>

      <!-- MAIN GRID -->
      <div class="main-grid">

        <!-- TABLE SECTION -->
        <section class="table-card">
          <div class="table-card-header">
            <div>
              <h2 class="table-card-title">Historial de evaluaciones</h2>
              <span class="table-card-count" id="rowCount">Cargando…</span>
            </div>
            <div class="trim-tabs">
              <button class="trim-tab active" data-trim="all">Todas</button>
              <button class="trim-tab" data-trim="1">1° Trim.</button>
              <button class="trim-tab" data-trim="2">2° Trim.</button>
              <button class="trim-tab" data-trim="3">3° Trim.</button>
            </div>
          </div>

          <div class="table-wrap">
            <table class="eval-table" id="evalTable">
              <thead>
                <tr>
                  <th class="th-sortable" data-col="materia">
                    Materia
                    <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none"><path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/></svg>
                  </th>
                  <th>Tipo</th>
                  <th class="th-sortable" data-col="nota">
                    Nota
                    <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none"><path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/></svg>
                  </th>
                  <th class="th-sortable" data-col="fecha">
                    Fecha
                    <svg class="sort-ico" viewBox="0 0 10 14" width="8" fill="none"><path d="M5 2v10M2 9l3 3 3-3M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/></svg>
                  </th>
                  <th>Trimestre</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody id="evalBody">
                <tr><td colspan="6" class="td-loading">Cargando datos…</td></tr>
              </tbody>
            </table>
          </div>

          <div class="table-footer">
            <span class="footer-note" id="footerNote"></span>
            <div class="pagination" id="pagination"></div>
          </div>
        </section>

        <!-- RIGHT PANEL -->
        <aside class="right-col">

          <!-- ALERTS -->
          <div class="side-card">
            <div class="side-card-header">
              <h3 class="side-card-title">Alertas académicas</h3>
              <span class="badge-count" id="alertBadge">4</span>
            </div>
            <div id="alertList" class="alert-list"></div>
          </div>

          <!-- PRÓXIMAS EVALUACIONES -->
          <div class="side-card">
            <div class="side-card-header">
              <h3 class="side-card-title">Próximas evaluaciones</h3>
            </div>
            <div id="proxList" class="prox-list"></div>
          </div>

          <!-- PROMEDIOS DESTACADOS -->
          <div class="side-card promedios-card">
            <div class="side-card-header">
              <h3 class="side-card-title">Promedios por materia</h3>
            </div>
            <div id="promediosList" class="promedios-list"></div>
          </div>

        </aside>
      </div><!-- /main-grid -->

    </div><!-- /content -->
  </main>
</div><!-- /page-layout -->

<script src="../js/calificaciones.js"></script>
</body>
</html>