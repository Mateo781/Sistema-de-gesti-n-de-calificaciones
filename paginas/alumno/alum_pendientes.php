<main class="main-content">

  <!-- HEADER -->
  <div class="topbar">
    <div>
      <h1>Materias Pendientes</h1>
      <p>Seguimiento académico y regularización.</p>
    </div>
    <div class="top-buttons">
      <button class="btn btn-outline">Filtrar</button>
      <button class="btn btn-primary">Exportar PDF</button>
    </div>
  </div>

  <!-- RESUMEN -->
  <section class="summary-grid">
    <div class="summary-card border-red">
      <div class="summary-number red">5</div>
      <div>
        <h3>Materias Pendientes</h3>
        <span>Requieren regularización</span>
      </div>
    </div>
    <div class="summary-card border-blue">
      <div class="summary-number blue">2</div>
      <div>
        <h3>Intensificaciones</h3>
        <span>Actualmente activas</span>
      </div>
    </div>
    <div class="summary-card border-yellow">
      <div class="summary-number yellow">1</div>
      <div>
        <h3>Recursadas</h3>
        <span>Inscripciones abiertas</span>
      </div>
    </div>
    <div class="summary-card border-purple">
      <div class="summary-number purple">4</div>
      <div>
        <h3>Próximas Fechas</h3>
        <span>Eventos próximos</span>
      </div>
    </div>
  </section>

  <!-- SEARCH -->
  <div class="search-bar">
    <input type="text" placeholder="Buscar materia..." id="searchInput">
  </div>

  <!-- CONTENT -->
  <div class="content-grid">

    <!-- TABLE -->
    <section class="table-section">
      <div class="table-header">
        <div>
          <h2>Detalle de materias pendientes</h2>
          <span>Seguimiento académico</span>
        </div>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Materia</th>
              <th>Estado</th>
              <th>Acción requerida</th>
              <th>Fecha</th>
              <th>Profesor</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <tr>
              <td>
                <strong>Matemática</strong>
                <span>4° Año · División A</span>
              </td>
              <td><span class="status pending">Pendiente</span></td>
              <td>Inscribirse a intensificación</td>
              <td class="red-text">25/05/2025</td>
              <td>Prof. Martín López</td>
              <td class="actions">
                <button class="btn-small primary">Inscribirse</button>
                <button class="btn-small outline">Ver detalle</button>
              </td>
            </tr>
            <tr>
              <td>
                <strong>Química</strong>
                <span>4° Año · División A</span>
              </td>
              <td><span class="status intensification">Intensificación</span></td>
              <td>Completar actividades</td>
              <td class="blue-text">02/06/2025</td>
              <td>Prof. Laura Gómez</td>
              <td class="actions">
                <button class="btn-small outline">Ver detalle</button>
              </td>
            </tr>
            <tr>
              <td>
                <strong>Historia</strong>
                <span>4° Año · División A</span>
              </td>
              <td><span class="status recursada">Recursada</span></td>
              <td>Asistir a clases</td>
              <td class="yellow-text">15/07/2025</td>
              <td>Prof. Diego Torres</td>
              <td class="actions">
                <button class="btn-small outline">Ver detalle</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- PANEL DERECHO -->
    <aside class="right-panel">
      <div class="panel-card alert-card">
        <h3>Alertas académicas</h3>
        <ul>
          <li>Tenés materias pendientes de regularización.</li>
          <li>Inscribite antes del cierre.</li>
        </ul>
      </div>

      <div class="panel-card">
        <h3>Próximas intensificaciones</h3>
        <div class="event-item">
          <strong>Química</strong>
          <span>02/06/2025</span>
        </div>
        <div class="event-item">
          <strong>Geografía</strong>
          <span>05/06/2025</span>
        </div>
      </div>

      <div class="panel-card">
        <h3>Mensajes institucionales</h3>
        <p>Las inscripciones cierran el 25/05.</p>
      </div>

      <div class="panel-card">
        <h3>Recomendaciones pedagógicas</h3>
        <ul>
          <li>Organizá tu tiempo</li>
          <li>Consultá a tus docentes</li>
          <li>Asistí a clases de apoyo</li>
        </ul>
      </div>
    </aside>

  </div>
</main>