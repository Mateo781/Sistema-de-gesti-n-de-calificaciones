<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['usuario_rol'] ?? 0) != 3) {
    die("Acceso denegado. Este módulo es exclusivo para alumnos inscritos.");
}

require_once __DIR__ . '/../../php/db.php';
$id_alumno = $_SESSION['usuario_id'] ?? 0;

$materia_sel = intval($_GET['id_materia'] ?? 0);
$mensaje_ok  = "";
$mensaje_err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_entrega'])) {
    $id_evaluacion = intval($_POST['id_evaluacion'] ?? 0);

    if ($id_evaluacion > 0 && isset($_FILES['archivo_entrega']) && $_FILES['archivo_entrega']['error'] === UPLOAD_ERR_OK) {
        
        $upload_dir = __DIR__ . '/../../uploads/entregas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['archivo_entrega']['name'], PATHINFO_EXTENSION);
        $filename = "entrega_" . $id_evaluacion . "_" . $id_alumno . "_" . time() . "." . $file_extension;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['archivo_entrega']['tmp_name'], $target_file)) {
            $final_path = "uploads/entregas/" . $filename;

            try {
                $stmtEnt = $pdo->prepare("
                    INSERT INTO entrega_alumnos (id_evaluacion, id_alumno, enlace_resolucion, fecha_entrega)
                    VALUES (:id_eval, :id_al, :enlace, NOW())
                ");
                $stmtEnt->execute([
                    ':id_eval' => $id_evaluacion,
                    ':id_al'   => $id_alumno,
                    ':enlace'  => $final_path
                ]);
                $mensaje_ok = "¡Tarea entregada con éxito! Ya no aparecerá en tu lista de pendientes.";
            } catch (PDOException $e) {
                $mensaje_err = "Error al registrar la entrega: " . $e->getMessage();
            }
        } else {
            $mensaje_err = "Error al guardar el archivo en el servidor.";
        }
    } else {
        $mensaje_err = "Por favor, selecciona un archivo válido para subir.";
    }
}

try {
    $stmtMat = $pdo->prepare("
        SELECT DISTINCT m.id, m.nombre 
        FROM inscripciones i
        INNER JOIN curso_materia_docente cmd ON i.id_curso = cmd.id_curso
        INNER JOIN materias m ON cmd.id_materia = m.id
        WHERE i.id_alumno = :id_alumno AND i.activo = 1
        ORDER BY m.nombre ASC
    ");
    $stmtMat->execute([':id_alumno' => $id_alumno]);
    $materias_alumno = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar materias: " . $e->getMessage());
}

$actividades = [];
if ($materia_sel > 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                e.id AS id_evaluacion,
                e.titulo AS evaluacion_titulo,
                e.descripcion AS evaluacion_descripcion,
                e.archivo_pdf,
                e.fecha_evaluacion,
                e.fecha_fin,
                m.nombre AS materia_nombre,
                p.nombre AS periodo_nombre,
                te.nombre AS tipo_instancia
            FROM inscripciones i
            INNER JOIN curso_materia_docente cmd ON i.id_curso = cmd.id_curso
            INNER JOIN materias m ON cmd.id_materia = m.id
            INNER JOIN evaluaciones e ON cmd.id = e.id_curso_materia
            INNER JOIN tipos_evaluacion te ON e.id_tipo_evaluacion = te.id
            INNER JOIN periodos p ON e.id_periodo = p.id
            LEFT JOIN entrega_alumnos ea ON e.id = ea.id_evaluacion AND ea.id_alumno = :id_alumno
            WHERE i.id_alumno = :id_alumno2 
              AND i.activo = 1 
              AND cmd.id_materia = :id_materia
              AND ea.id_evaluacion IS NULL
            ORDER BY e.fecha_fin ASC
        ");
        $stmt->execute([
            ':id_alumno'  => $id_alumno,
            ':id_alumno2' => $id_alumno,
            ':id_materia' => $materia_sel
        ]);
        $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al cargar actividades pendientes: " . $e->getMessage());
    }
}
?>

<main class="main-content">
    <?php include_once 'includes/topbar.php'; ?>

    <div class="dashboard-content">

        <?php if(!empty($mensaje_ok)): ?>
            <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;"><?= $mensaje_ok ?></div>
        <?php endif; ?>
        <?php if(!empty($mensaje_err)): ?>
            <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;"><?= $mensaje_err ?></div>
        <?php endif; ?>

        <div class="selector-card" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
            <form method="GET" action="index.php">
                <input type="hidden" name="p" value="alum_actividades">
                
                <div style="display: flex; flex-direction: column; max-width: 350px;">
                    <label style="font-weight: 600; margin-bottom: 8px; color: #334155;">Seleccionar Materia:</label>
                    <select name="id_materia" onchange="this.form.submit()" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: #fff;">
                        <option value="">-- Seleccione una Materia --</option>
                        <?php foreach ($materias_alumno as $mat): ?>
                            <option value="<?= $mat['id'] ?>" <?= ($materia_sel == $mat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if ($materia_sel === 0): ?>
            <div style="background: #f8fafc; color: #64748b; padding: 30px; border-radius: 8px; text-align: center; border: 1px dashed #cbd5e1;">
                <p style="margin: 0; font-size: 15px;">🔍 Por favor, selecciona una materia para ver tus tareas pendientes de entrega.</p>
            </div>
        <?php elseif (empty($actividades)): ?>
            <div style="background: #f0fdf4; color: #166534; padding: 24px; border-radius: 8px; text-align: center; border: 1px solid #bbf7d0;">
                <p style="margin: 0; font-size: 16px; font-weight: 600;">🎉 ¡Al día! No tienes tareas pendientes de entrega para esta materia.</p>
            </div>
        <?php else: ?>
            
            <div class="table-card">
                <h4>⏳ Tareas Pendientes de Entrega</h4>
                <div style="overflow-x: auto; width: 100%;">
                    <table class="custom-table" style="width: 100%; border-collapse: collapse; min-width: 900px;">
                        <thead>
                            <tr style="background: #f1f5f9; text-align: left;">
                                <th style="padding: 12px; width: 140px;">Fechas</th>
                                <th style="padding: 12px; width: 150px;">Instancia</th>
                                <th style="padding: 12px; width: 280px;">Detalles</th>
                                <th style="padding: 12px; width: 220px;">Subir Entrega</th>
                                <th style="padding: 12px; width: 160px; text-align: center;">Calificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actividades as $act): 
                                $periodo_ui = $act['periodo_nombre'];
                                if (stripos($periodo_ui, '1') !== false || stripos($periodo_ui, 'prim') !== false) {
                                    $periodo_ui = '1° Cuatrimestre';
                                } elseif (stripos($periodo_ui, '2') !== false || stripos($periodo_ui, 'seg') !== false) {
                                    $periodo_ui = '2° Cuatrimestre';
                                }
                                
                                $hoy = date('Y-m-d');
                                $fecha_limite = $act['fecha_fin'];
                                $expirado = ($hoy > $fecha_limite);
                            ?>
                                <tr style="border-bottom: 1px solid #e2e8f0; vertical-align: top;">
                                    <td style="padding: 12px; font-size: 13px;">
                                        <span style="display:block; color: #475569;"><strong>Asignado:</strong> <?= date('d/m/Y', strtotime($act['fecha_evaluacion'])) ?></span>
                                        <span style="display:block; margin-top: 4px; color: <?= $expirado ? '#b91c1c' : '#15803d' ?>; font-weight: 600;">
                                            <strong>Límite:</strong> <?= date('d/m/Y', strtotime($fecha_limite)) ?>
                                        </span>
                                    </td>
                                    
                                    <td style="padding: 12px;">
                                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block;">
                                            <?= htmlspecialchars($act['tipo_instancia']) ?>
                                        </span>
                                        <small style="display: block; color: #64748b; font-size: 11px; margin-top: 4px;">
                                            <?= htmlspecialchars($periodo_ui) ?>
                                        </small>
                                    </td>
                                    
                                    <td style="padding: 12px;">
                                        <strong style="color: #1e293b; font-size: 14px; display: block; margin-bottom: 4px;">
                                            <?= htmlspecialchars($act['evaluacion_titulo']) ?>
                                        </strong>
                                        <?php if (!empty($act['evaluacion_descripcion'])): ?>
                                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #64748b; line-height: 1.4;">
                                                <?= nl2br(htmlspecialchars($act['evaluacion_descripcion'])) ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if (!empty($act['archivo_pdf'])): ?>
                                            <a href="<?= htmlspecialchars($act['archivo_pdf']) ?>" target="_blank" style="color: #d32f2f; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                📄 Descargar Consigna (.PDF)
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td style="padding: 12px; background: #f8fafc;">
                                        <?php if (!$expirado): ?>
                                            <form method="POST" action="" enctype="multipart/form-data" style="margin-top: 5px;">
                                                <input type="hidden" name="id_evaluacion" value="<?= $act['id_evaluacion'] ?>">
                                                
                                                <input type="file" name="archivo_entrega" required style="font-size:12px; margin-bottom:8px; width:100%;">
                                                
                                                <button type="submit" name="enviar_entrega" style="background: #1e293b; color: #fff; border: none; padding: 7px; border-radius: 4px; font-size: 11px; cursor: pointer; width: 100%; font-weight:600;">
                                                    Enviar Entrega
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #b91c1c; font-size: 12px; font-style: italic; font-weight: 500;">Plazo vencido.</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td style="padding: 12px; text-align: center; vertical-align: middle;">
                                        <span style="color: #94a3b8; font-size: 12px; font-style: italic;">Pendiente</span>
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