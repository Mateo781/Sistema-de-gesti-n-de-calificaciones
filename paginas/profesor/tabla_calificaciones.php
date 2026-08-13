<form method="POST" action="" id="formCalificaciones">
    <input type="hidden" name="id_curso_materia" value="<?= htmlspecialchars($materia_seleccionada) ?>">

    <!-- Filtros de Cabecera -->
    <section class="table-card" style="padding: 20px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Período</label>
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
            <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--t2);">Fecha</label>
            <input type="date" name="fecha_evaluacion" class="filter-input" value="<?= date('Y-m-d') ?>" required>
        </div>
    </section>

    <!-- Tabla de Alumnos -->
    <section class="table-card">
        <div class="table-card-header">
            <h2 class="table-card-title">Listado de Alumnos</h2>
            <span class="table-card-count"><?= count($alumnos) ?> alumnos</span>
        </div>

        <div class="table-wrap">
            <table class="eval-table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th style="width: 130px;">Nota Numérica</th>
                        <th style="width: 180px;">RITE Conceptual</th>
                        <th>Observaciones</th>
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
                                <!-- Se inyecta el valor existente si existe -->
                                <input type="number" name="notas[<?= $al['id'] ?>][numeric]" class="filter-input"
                                       style="width: 90px; text-align: center;" 
                                       value="<?= htmlspecialchars($al['nota_numerica'] ?? '') ?>" 
                                       min="1" max="10" step="0.5" placeholder="1-10">
                            </td>
                            <td>
                                <select name="notas[<?= $al['id'] ?>][conceptual]" class="filter-select" style="width: 100%;">
                                    <option value="">-- Sin Nota --</option>
                                    <option value="TEA" <?= ($al['nota_conceptual'] ?? '') == 'TEA' ? 'selected' : '' ?>>TEA (Avance Destacado)</option>
                                    <option value="TEP" <?= ($al['nota_conceptual'] ?? '') == 'TEP' ? 'selected' : '' ?>>TEP (En Proceso)</option>
                                    <option value="TED" <?= ($al['nota_conceptual'] ?? '') == 'TED' ? 'selected' : '' ?>>TED (Discontinuo)</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="notas[<?= $al['id'] ?>][obs]" class="filter-input"
                                       style="width: 100%;" value="<?= htmlspecialchars($al['observaciones'] ?? '') ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer" style="justify-content: flex-end; padding: 15px;">
            <button type="submit" class="btn-export" style="background: var(--accent); color: white; padding: 10px 24px; border-radius: 5px; border: none; cursor: pointer;">
                Guardar Cambios
            </button>
        </div>
    </section>
</form>