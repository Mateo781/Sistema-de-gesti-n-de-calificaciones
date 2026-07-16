<main class="content-body"> <!-- Usamos la clase del contenedor que ya tienes -->
    
    <section class="summary-cards" aria-label="Resumen de mis criterios">
        <div class="summary-card">
            <div class="card-icon blue">
                <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M10 4v12M6 8l4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="card-body">
                <span class="card-label">Criterios Enviados</span>
                <span class="card-value">5</span>
                <span class="card-sub">Total histórico</span>
            </div>
            <div class="card-bar blue"></div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon red">
                <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="7" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="13.5" r="1" fill="currentColor"/></svg>
            </div>
            <div class="card-body">
                <span class="card-label">Rechazados</span>
                <span class="card-value">1</span>
                <span class="card-sub">Requieren corrección</span>
            </div>
            <div class="card-bar red"></div>
        </div>
    </section>

    <!-- Sección para crear un nuevo criterio (Ahora integrada como table-section) -->
    <section class="table-section" aria-label="Nuevo Criterio">
        <div class="section-header">
            <h2 class="section-title">Establecer Nuevo Criterio de Evaluación</h2>
        </div>
        
        <form action="guardar_criterio.php" method="POST" style="display: flex; flex-direction: column; gap: 20px; max-width: 700px;">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-weight: 600; font-size: 14px; color: var(--text);">Título del Criterio</label>
                <input type="text" name="titulo" placeholder="Ej. Uso de real_escape_string en PHP" required>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label style="font-weight: 600; font-size: 14px; color: var(--text);">Descripción General</label>
                <textarea name="descripcion" placeholder="Explique brevemente el propósito de este criterio..." required></textarea>
            </div>
            
            <div id="contenedorDetalles" style="display: flex; flex-direction: column; gap: 10px;">
                <label style="font-weight: 600; font-size: 14px; color: var(--text);">Puntos Específicos a Evaluar (Detalles)</label>
                <input type="text" name="detalle[]" placeholder="Punto 1..." required>
                <input type="text" name="detalle[]" placeholder="Punto 2...">
            </div>

            <button type="button" class="filter-btn" style="align-self: flex-start;" onclick="agregarPunto()">+ Agregar otro punto</button>
            
            <button type="submit" class="filter-btn active" style="align-self: flex-start; padding: 12px 24px;">Enviar Criterio a Dirección</button>
        </form>
    </section>

    <!-- Historial -->
    <section class="table-section" aria-label="Estado de Criterios">
        <div class="section-header">
            <h2 class="section-title">Historial de Mis Criterios</h2>
        </div>
        <div class="table-wrapper">
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha de Envío</th>
                        <th>Estado</th>
                        <th>Observación del Director</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Seguridad en Formularios Web</td>
                        <td>10-07-2026</td>
                        <td><span class="filter-btn" style="color:var(--red); border-color:var(--red); background:#fef2f2; cursor:default;">Rechazado</span></td>
                        <td style="color: var(--text-muted);">Falta especificar el protocolo para inyecciones SQL.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>