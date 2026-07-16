<main class="main-content">
    <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <svg viewBox="0 0 20 20" width="20" height="20"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>

    <div class="content-body">
        <section class="summary-cards" aria-label="Resumen de Criterios">
            <div class="card summary-card" data-color="blue">
                <div class="card-icon blue">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M10 2l2.5 5 5.5.8-4 3.9.94 5.5L10 14.5 5.06 17.2 6 11.7 2 7.8l5.5-.8L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                </div>
                <div class="card-body">
                    <span class="card-label">Criterios Pendientes</span>
                    <span class="card-value">4</span> <!-- Aquí luego harás un COUNT() a la BD -->
                    <span class="card-sub">Esperando revisión</span>
                </div>
                <div class="card-bar blue"></div>
            </div>

            <div class="card summary-card" data-color="green">
                <div class="card-icon green">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="card-body">
                    <span class="card-label">Aprobados este mes</span>
                    <span class="card-value">12</span> <!-- Aquí luego harás un COUNT() a la BD -->
                    <span class="card-sub">Listos para docentes</span>
                </div>
                <div class="card-bar green"></div>
            </div>
        </section>

        <div class="content-grid">
            <section class="table-section" aria-label="Revisión de Criterios">
                <div class="section-header">
                    <h2 class="section-title">Revisión de Criterios de Evaluación</h2>
                </div>
                <div class="table-wrapper">
                    <table class="grades-table" id="criteriosTable">
                        <thead>
                            <tr>
                                <th>Título del Criterio</th>
                                <th>Jefe de Área</th>
                                <th>Fecha Envío</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ejemplo de fila estática (luego lo reemplazarás con un foreach de BD) -->
                            <tr>
                                <td>Criterios de Programación Web</td>
                                <td>Juan Pérez</td>
                                <td>16-07-2026</td>
                                <td><span style="color: #d97706; font-weight: bold;">Pendiente</span></td>
                                <td>
                                    <button class="filter-btn active" onclick="abrirModalDirector(1, 'Aprobado')">Aprobar</button>
                                    <button class="filter-btn" style="border-color: #dc2626; color: #dc2626;" onclick="abrirModalDirector(1, 'Rechazado')">Rechazar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Modal HTML y CSS para justificar aprobación/rechazo -->
<style>
    .modal-justificacion { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
    .modal-content-dir { background: white; padding: 25px; border-radius: 8px; width: 100%; max-width: 450px; font-family: 'Inter', sans-serif; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .modal-content-dir h3 { margin-top: 0; color: #1e293b; }
    .modal-content-dir textarea { width: 100%; height: 120px; margin-top: 15px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; resize: vertical; box-sizing: border-box; font-family: inherit;}
    .modal-content-dir textarea:focus { outline: none; border-color: #2563eb; }
    .modal-actions { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; }
</style>

<div id="modalDirector" class="modal-justificacion">
    <div class="modal-content-dir">
        <h3 id="modalTitulo">Justificación</h3>
        <p style="font-size: 14px; color: #64748b;">Añada una observación para el jefe de área (obligatorio si rechaza):</p>
        <form action="procesar_criterio.php" method="POST">
            <input type="hidden" name="id_criterio" id="criterio_id" value="">
            <input type="hidden" name="estado_nuevo" id="criterio_estado" value="">
            <textarea name="observacion_director" id="observacion_director" placeholder="Escriba su justificación aquí..."></textarea>
            <div class="modal-actions">
                <button type="button" class="filter-btn" onclick="cerrarModalDirector()">Cancelar</button>
                <button type="submit" class="filter-btn active" id="btnConfirmar">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalDirector(id, estado) { 
        document.getElementById('modalDirector').style.display = 'flex'; 
        document.getElementById('criterio_id').value = id; 
        document.getElementById('criterio_estado').value = estado; 
        
        const modalTitulo = document.getElementById('modalTitulo');
        const btnConfirmar = document.getElementById('btnConfirmar');
        
        if(estado === 'Aprobado') {
            modalTitulo.innerText = 'Aprobar Criterio';
            modalTitulo.style.color = '#16a34a';
            btnConfirmar.style.backgroundColor = '#16a34a';
            btnConfirmar.style.borderColor = '#16a34a';
            btnConfirmar.innerText = 'Confirmar Aprobación';
        } else {
            modalTitulo.innerText = 'Rechazar Criterio';
            modalTitulo.style.color = '#dc2626';
            btnConfirmar.style.backgroundColor = '#dc2626';
            btnConfirmar.style.borderColor = '#dc2626';
            btnConfirmar.innerText = 'Confirmar Rechazo';
        }
    }
    
    function cerrarModalDirector() {
        document.getElementById('modalDirector').style.display = 'none';
        document.getElementById('observacion_director').value = '';
    }
</script>