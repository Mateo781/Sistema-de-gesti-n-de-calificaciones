<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_rol = $_SESSION['usuario_rol'] ?? null;

if ($id_rol == 2) {
    // ==========================================
    // VISTA DE INICIO PARA EL DOCENTE
    // ==========================================
    ?>
    <div style="padding: 20px; font-family: sans-serif;">
        <h2>¡Bienvenido al Panel Docente!</h2>
        <p>Desde el menú lateral izquierdo podés seleccionar <strong>Cargar Calificaciones</strong> para gestionar tus cursos y registrar nuevas evaluaciones.</p>
    </div>
    <?php

} else {
    ?>
    <main class="main-content">
      <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>
        <div class="header-left">
          <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <svg viewBox="0 0 20 20" width="20" height="20"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          </button>
        </div>

      <div class="content-body">

        <section class="summary-cards" aria-label="Resumen académico">
          <div class="card summary-card" data-color="blue">
            <div class="card-icon blue">
              <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M10 2l2.5 5 5.5.8-4 3.9.94 5.5L10 14.5 5.06 17.2 6 11.7 2 7.8l5.5-.8L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
            </div>
            <div class="card-body">
              <span class="card-label">Promedio general</span>
              <span class="card-value">7.4</span>
              <span class="card-sub">Sobre 10 puntos</span>
            </div>
            <div class="card-bar blue"></div>
          </div>

          <div class="card summary-card" data-color="green">
            <div class="card-icon green">
              <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="card-body">
              <span class="card-label">Materias aprobadas</span>
              <span class="card-value">8</span>
              <span class="card-sub">de 12 materias totales</span>
            </div>
            <div class="card-bar green"></div>
          </div>

          <div class="card summary-card" data-color="red">
            <div class="card-icon red">
              <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="7" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="13.5" r="1" fill="currentColor"/></svg>
            </div>
            <div class="card-body">
              <span class="card-label">Materias pendientes</span>
              <span class="card-value">3</span>
              <span class="card-sub">Requieren atención</span>
            </div>
            <div class="card-bar red"></div>
          </div>

          <div class="card summary-card" data-color="cyan">
            <div class="card-icon cyan">
              <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M10 4v12M6 8l4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="4" y="12" width="12" height="5" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
            </div>
            <div class="card-body">
              <span class="card-label">Intensificación activa</span>
              <span class="card-value">1</span>
              <span class="card-sub">Matemática — dic 2025</span>
            </div>
            <div class="card-bar cyan"></div>
          </div>
        </section>

        <div class="content-grid">

          <section class="table-section" aria-label="Estado por materia">
            <div class="section-header">
              <h2 class="section-title">Estado académico por materia</h2>
              <div class="table-filters">
                <button class="filter-btn active" data-filter="all">Todas</button>
                <button class="filter-btn" data-filter="aprobada">Aprobadas</button>
                <button class="filter-btn" data-filter="pendiente">Pendientes</button>
                <button class="filter-btn" data-filter="intensificacion">Intensificación</button>
              </div>
            </div>
            <div class="table-wrapper">
              <table class="grades-table" id="gradesTable">
                <thead>
                  <tr>
                    <th>Materia</th>
                    <th>Docente</th>
                    <th>1° Cuatrimestre</th>
                    <th>2° Cuatrimestre</th>
                    <th>Prom.</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody id="gradesBody">
                </tbody>
              </table>
            </div>
          </section>

        </div>

      </div>
    </main>
    <?php
}
?>