<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once dirname(__DIR__, 2) . '/php/db.php'; 
    $id_tutor = $_SESSION['usuario_id'] ?? null;
    $mis_alumnos = [];
    $alumno_actual = null;
    $evaluaciones = [];
    $stats = [
        'promedio' => '0.0',
        'aprobadas' => 0,
        'total_materias' => 0,
        'pendientes' => 0,
        'intensificaciones' => 0,
        'materia_intensificacion' => 'Ninguna'
    ];

    if ($id_tutor) {
        try {
            // Obtienen los alumnos vinculados al tutor
            $stmt_alumnos = $pdo->prepare("
                SELECT u.id, u.nombre, u.apellido, u.dni
                FROM usuarios u
                INNER JOIN tutores_alumnos ta ON u.id = ta.id_alumno
                WHERE ta.id_tutor = :tutor
            ");
            $stmt_alumnos->execute([':tutor' => $id_tutor]);
            $mis_alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
            // Determinamos el alumno seleccionado(el primero en la lista o el que se selecciono)
            $id_alumno_activo = $_GET['alumno_id'] ?? ($mis_alumnos[0]['id'] ?? null);
            if ($id_alumno_activo) {
                $stmt_alumno = $pdo->prepare("
                    SELECT id, nombre, apellido, dni 
                    FROM usuarios 
                    WHERE id = :id LIMIT 1
                ");
                $stmt_alumno->execute([':id' => $id_alumno_activo]);
                $alumno_actual = $stmt_alumno->fetch(PDO::FETCH_ASSOC);

                if ($alumno_actual) {
                    // Promedio general y materias aprobadas
                    $stmt_trayectoria = $pdo->prepare("
                        SELECT 
                            IFNULL(AVG(promedio_final), 0) as promedio, 
                            COUNT(*) as total_aprobadas 
                        FROM trayectorias 
                        WHERE id_alumno = :alumno AND id_estado_materia = 1
                    ");
                    $stmt_trayectoria->execute([':alumno' => $id_alumno_activo]);
                    $res_trayectoria = $stmt_trayectoria->fetch(PDO::FETCH_ASSOC);
                    $stats['promedio'] = number_format($res_trayectoria['promedio'], 1);
                    $stats['aprobadas'] = $res_trayectoria['total_aprobadas'];
                    $stmt_total_mat = $pdo->prepare("SELECT COUNT(*) as total FROM trayectorias WHERE id_alumno = :alumno");
                    $stmt_total_mat->execute([':alumno' => $id_alumno_activo]);
                    $stats['total_materias'] = $stmt_total_mat->fetch(PDO::FETCH_ASSOC)['total'];
                    // Materias pendientes
                    $stmt_pendientes = $pdo->prepare("SELECT COUNT(*) as total FROM v_materias_pendientes WHERE id_alumno = :alumno");
                    $stmt_pendientes->execute([':alumno' => $id_alumno_activo]);
                    $stats['pendientes'] = $stmt_pendientes->fetch(PDO::FETCH_ASSOC)['total'];
                    // Intensificaciones activas
                    $stmt_intensif = $pdo->prepare("
                        SELECT i.id_materia, m.nombre as nombre_materia 
                        FROM intensificaciones i
                        LEFT JOIN materias m ON i.id_materia = m.id
                        WHERE i.id_alumno = :alumno LIMIT 1
                    ");
                    $stmt_intensif->execute([':alumno' => $id_alumno_activo]);
                    $res_intensif = $stmt_intensif->fetch(PDO::FETCH_ASSOC);

                    if ($res_intensif) {
                        $stats['intensificaciones'] = 1;
                        $stats['materia_intensificacion'] = $res_intensif['nombre_materia'] ?? 'Activa';
                    }
                    // Obtener Historial de Evaluaciones
                    $stmt_eval = $pdo->prepare("
                        SELECT 
                            m.nombre as materia,
                            t.tipo_evaluacion,
                            t.promedio_final as nota,
                            t.fecha_entrega,
                            t.cuatrimestre,
                            em.nombre as estado
                        FROM trayectorias t
                        INNER JOIN materias m ON t.id_materia = m.id
                        INNER JOIN estados_materias em ON t.id_estado_materia = em.id
                        WHERE t.id_alumno = :alumno
                        ORDER BY t.fecha_entrega DESC
                    ");
                    $stmt_eval->execute([':alumno' => $id_alumno_activo]);
                    $evaluaciones = $stmt_eval->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        } catch (PDOException $e) {
            $error_bd = $e->getMessage();
        }
    }
?>

<main class="main-content">
    <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>
    
    <div class="content-body">
        <?php if (!empty($mis_alumnos)): ?>
            <div style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <label for="selectAlumno" style="font-weight: 600; margin-right: 10px;">Seleccionar Alumno:</label>
                <select id="selectAlumno" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;" onchange="location = this.value;">
                    <?php foreach ($mis_alumnos as $al): ?>
                        <option value="index.php?p=tutor_calificaciones&alumno_id=<?= $al['id'] ?>" 
                            <?= ($alumno_actual && $alumno_actual['id'] == $al['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($al['nombre'] . ' ' . $al['apellido']) ?> (DNI: <?= htmlspecialchars($al['dni']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <section class="alumno-header">
                <div class="alumno-header-info">
                    <span class="section-label">Historial académico</span>
                    <h1><?= htmlspecialchars($alumno_actual['nombre'] . ' ' . $alumno_actual['apellido']) ?></h1>
                    <span class="alumno-curso">Educación Secundaria</span>
                </div>

                <div class="alumno-id">
                    <span>Alumno</span>
                    <strong>N.º <?= str_pad($alumno_actual['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                </div>
            </section>

            <!-- Estado academico -->
            <section class="summary-cards">
                <div class="summary-card blue">
                    <span class="card-label">Promedio general</span>
                    <strong class="card-value"><?= $stats['promedio'] ?></strong>
                    <span class="card-sub">Sobre 10 puntos</span>
                </div>

                <div class="summary-card green">
                    <span class="card-label">Materias aprobadas</span>
                    <strong class="card-value"><?= $stats['aprobadas'] ?></strong>
                    <span class="card-sub">De <?= $stats['total_materias'] ?> materias</span>
                </div>

                <div class="summary-card red">
                    <span class="card-label">Materias pendientes</span>
                    <strong class="card-value"><?= $stats['pendientes'] ?></strong>
                    <span class="card-sub">Requieren atención</span>
                </div>

                <div class="summary-card cyan">
                    <span class="card-label">Intensificación activa</span>
                    <strong class="card-value"><?= $stats['intensificaciones'] ?></strong>
                    <span class="card-sub"><?= htmlspecialchars($stats['materia_intensificacion']) ?></span>
                </div>
            </section>

            <!-- Historial Evaluaciones -->
            <section class="table-card">
                <div class="table-card-header">
                    <div>
                        <h2>Historial de evaluaciones</h2>
                        <span><?= count($evaluaciones) ?> evaluaciones registradas</span>
                    </div>

                    <!-- Filtros -->
                    <div class="cuatri-tabs">
                        <button type="button" class="cuatri-tab active"> Todas </button>
                        <button type="button" class="cuatri-tab"> 1° Cuatrimestre </button>
                        <button type="button" class="cuatri-tab"> 2° Cuatrimestre </button>
                    </div>
                </div>

                <!-- Tabla de Notas -->
                <div class="table-wrapper">
                    <table class="grades-table">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Tipo</th>
                                <th>Nota</th>
                                <th>Fecha de entrega</th>
                                <th>Cuatrimestre</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($evaluaciones)): ?>
                                <?php foreach ($evaluaciones as $eval): ?>
                                    <?php 
                                        $es_aprobado = strtolower($eval['estado']) === 'aprobado' || $eval['nota'] >= 6; 
                                        $clase_badge = $es_aprobado ? 'aprobada' : 'pendiente';
                                        $clase_estado = $es_aprobado ? 'aprobado' : 'pendiente';
                                        $fecha_fmtd = !empty($eval['fecha_entrega']) ? date('d/m/Y', strtotime($eval['fecha_entrega'])) : 'N/A';
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($eval['materia']) ?></strong></td>
                                        <td><?= htmlspecialchars($eval['tipo_evaluacion'] ?? 'Evaluación') ?></td>
                                        <td>
                                            <span class="nota <?= $clase_badge ?>">
                                                <?= number_format($eval['nota'], 1) ?>
                                            </span>
                                        </td>
                                        <td><?= $fecha_fmtd ?></td>
                                        <td><?= htmlspecialchars($eval['cuatrimestre'] ?? '1° Cuatrimestre') ?></td>
                                        <td>
                                            <span class="estado <?= $clase_estado ?>">
                                                <?= htmlspecialchars($eval['estado']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">
                                        No hay evaluaciones registradas para este alumno.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <span>Mostrando las evaluaciones registradas del alumno.</span>
                    <div class="pagination">
                        <button type="button">Anterior</button>
                        <span>1</span>
                        <button type="button">Siguiente</button>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <!-- Estado vacio en caso de no tener ningún alumno vinculado -->
            <div style="text-align: center; padding: 50px; background: #fff; border-radius: 8px;">
                <h2>No hay alumnos vinculados</h2>
                <p>Para ver las calificaciones, primero debes vincular un alumno desde la sección de Inicio.</p>
            </div>
        <?php endif; ?>
    </div>
</main>