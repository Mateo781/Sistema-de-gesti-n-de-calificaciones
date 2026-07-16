<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cambiá el número para que valide correctamente contra el ID 3 de tu base de datos
if (($_SESSION['usuario_rol'] ?? 0) != 3) {
    die("Acceso denegado. Este módulo es exclusivo para alumnos inscritos.");
}
// Retroceder dos niveles desde la ubicación actual para conectar la base de datos
require_once __DIR__ . '/../../php/db.php';

$id_alumno = $_SESSION['usuario_id'] ?? 0;

// ==========================================
// 1. OBTENER LAS EVALUACIONES Y MATERIALES DEL ALUMNO
// ==========================================
try {
    // Esta consulta busca los cursos donde está inscrito el alumno de forma activa, 
    // mapea las materias de ese curso y extrae las evaluaciones subidas por los docentes.
    $stmt = $pdo->prepare("
        SELECT 
            e.id AS id_evaluacion,
            e.titulo AS evaluacion_titulo,
            e.descripcion AS evaluacion_descripcion,
            e.archivo_pdf,
            e.fecha_evaluacion,
            m.nombre AS materia_nombre,
            p.nombre AS periodo_nombre,
            te.nombre AS tipo_instancia,
            CONCAT(u.apellido, ', ', u.nombre) AS docente_nombre
        FROM inscripciones i
        INNER JOIN curso_materia_docente cmd ON i.id_curso = cmd.id_curso
        INNER JOIN materias m ON cmd.id_materia = m.id
        INNER JOIN evaluaciones e ON cmd.id = e.id_curso_materia
        INNER JOIN tipos_evaluacion te ON e.id_tipo_evaluacion = te.id
        INNER JOIN periodos p ON e.id_periodo = p.id
        INNER JOIN usuarios u ON cmd.id_docente = u.id
        WHERE i.id_alumno = :id_alumno AND i.activo = 1
        ORDER BY e.fecha_evaluacion DESC, m.nombre ASC
    ");
    $stmt->execute([':id_alumno' => $id_alumno]);
    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar el panel de actividades: " . $e->getMessage());
}
?>

<main class="main-content">
    <?php include_once 'includes/topbar.php'; ?>

    <div class="dashboard-content">
        
        <?php if (empty($actividades)): ?>
            <div style="background: #e2e8f0; color: #334155; padding: 24px; border-radius: 8px; text-align: center; border: 1px solid #cbd5e1;">
                <p style="margin: 0; font-size: 16px; font-weight: 600;">👋 Todavía no se registraron actividades o evaluaciones para tu curso.</p>
                <small style="color: #64748b; display: block; margin-top: 4px;">Cuando tus profesores suban trabajos prácticos o consignas en PDF, los vas a ver listados acá.</small>
            </div>
        <?php else: ?>
            
            <div class="table-card">
                <h4>📂 Cronograma de Actividades y Material Adjunto</h4>
                <div style="overflow-x: auto; width: 100%;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Fecha</th>
                                <th style="width: 200px;">Materia</th>
                                <th style="width: 150px;">Instancia / Período</th>
                                <th>Detalles de la Actividad</th>
                                <th style="width: 180px; text-align: center;">Archivo Adjunto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actividades as $act): 
                                // Normalización visual de periodos a Cuatrimestres
                                $periodo_ui = $act['periodo_nombre'];
                                if (stripos($periodo_ui, '1') !== false || stripos($periodo_ui, 'prim') !== false) {
                                    $periodo_ui = '1° Cuatrimestre';
                                } elseif (stripos($periodo_ui, '2') !== false || stripos($periodo_ui, 'seg') !== false) {
                                    $periodo_ui = '2° Cuatrimestre';
                                }
                            ?>
                                <tr>
                                    <td style="font-weight: 600; color: #475569;">
                                        <?= date('d/m/Y', strtotime($act['fecha_evaluacion'])) ?>
                                    </td>
                                    
                                    <td style="font-weight: 700; color: #1a2d5a;">
                                        <?= htmlspecialchars($act['materia_nombre']) ?>
                                        <small style="display: block; color: #64748b; font-weight: 400; margin-top: 2px;">
                                            Prof: <?= htmlspecialchars($act['docente_nombre']) ?>
                                        </small>
                                    </td>
                                    
                                    <td>
                                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; display: inline-block; margin-bottom: 4px;">
                                            <?= htmlspecialchars($act['tipo_instancia']) ?>
                                        </span>
                                        <small style="display: block; color: #64748b; font-size: 11px;">
                                            <?= htmlspecialchars($periodo_ui) ?>
                                        </small>
                                    </td>
                                    
                                    <td>
                                        <strong style="color: #1e293b; font-size: 14px; display: block;">
                                            <?= htmlspecialchars($act['evaluacion_titulo']) ?>
                                        </strong>
                                        <?php if (!empty($act['evaluacion_descripcion'])): ?>
                                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b; line-height: 1.4;">
                                                <?= nl2br(htmlspecialchars($act['evaluacion_descripcion'])) ?>
                                            </p>
                                        <?php else: ?>
                                            <span style="font-style: italic; color: #94a3b8; font-size: 12px;">Sin descripción adicional provista.</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td style="text-align: center;">
                                        <?php if (!empty($act['archivo_pdf'])): ?>
                                            <a href="<?= htmlspecialchars($act['archivo_pdf']) ?>" 
                                               target="_blank" 
                                               style="background-color: #d32f2f; color: #ffffff; text-decoration: none; padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(211, 47, 47, 0.2); transition: all 0.2s;"
                                               onmouseover="this.style.backgroundColor='#b71c1c'" 
                                               onmouseout="this.style.backgroundColor='#d32f2f'">
                                                📄 Ver PDF Consigna
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 12px; font-style: italic;">No requiere archivo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </div>
</main>