<?php
?>
<form method="POST" action="" id="formCalificaciones">
    <input type="hidden" name="action" value="guardar_notas">
    <input type="hidden" name="id_curso_materia" value="<?= htmlspecialchars($materia_seleccionada) ?>">

    <section class="table-card" style="padding: 20px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Instancia / Período</label>
            <select name="id_periodo" class="filter-select" required>
                <?php foreach ($periodos as $per): ?>
                    <option value="<?= $per['id'] ?>"><?= htmlspecialchars($per['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Tipo de Evaluación</label>
            <select name="id_tipo_evaluacion" class="filter-select" required>
                <?php foreach ($tipos_eval as $te): ?>
                    <option value="<?= $te['id'] ?>"><?= htmlspecialchars($te['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Fecha Evaluativa</label>
            <input type="date" name="fecha_evaluacion" class="filter-input" style="padding: 0 12px;" value="<?= date('Y-m-d') ?>" required>
        </div>
    </section>

    <section class="table-card">
        <div class="table-card-header">
            <div>
                <h2 class="table-card-title">Listado de Alumnos Asignados</h2>
                <span class="table-card-count"><?= count($alumnos) ?> alumnos encontrados</span>
            </div>
        </div>

        <div class="table-wrap">
            <table class="eval-table">
                <thead>
                    <tr>
                        <th>Alumno (Apellido y Nombre)</th>
                        <th style="width: 130px;">Nota Numérica</th>
                        <th style="width: 180px;">RITE Conceptual</th>
                        <th>Observaciones pedagógicas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $al): ?>
                        <tr>
                            <td class="td-materia">
                                <strong><?= htmlspecialchars($al['apellido'] . ", " . $al['nombre']) ?></strong>
                                <div style="font-size: 11px; color: var(--t2);">DNI: <?= htmlspecialchars($al['dni']) ?></div>
                            </td>
                            <td>
                                <input type="number" name="notas[<?= $al['id'] ?>][numeric]" class="filter-input"
                                       style="width: 90px; text-align: center; padding: 0;" min="1" max="10" step="0.5" placeholder="1-10">
                            </td>
                            <td>
                                <select name="notas[<?= $al['id'] ?>][conceptual]" class="filter-select" style="width: 100%;">
                                    <option value="">-- Sin Nota --</option>
                                    <option value="TEA">TEA (Avance Destacado)</option>
                                    <option value="TEP">TEP (En Proceso)</option>
                                    <option value="TED">TED (Discontinuo)</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="notas[<?= $al['id'] ?>][obs]" class="filter-input"
                                       style="width: 100%;" placeholder="Ej: Cumplió en tiempo y forma...">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer" style="justify-content: flex-end;">
            <button type="submit" class="btn-export" style="background: var(--accent); color: white; padding: 10px 24px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Confirmar y Guardar Notas
            </button>
        </div>
    </section>
</form>