<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de acceso: Rol 2 = Docente
if (($_SESSION['usuario_rol'] ?? 0) != 2) {
    die("Acceso denegado. Este módulo es exclusivo para docentes.");
}

// Retroceder dos niveles desde la ubicación actual de este archivo
require_once __DIR__ . '/../../php/db.php';

$id_docente = $_SESSION['usuario_id'] ?? 1; // Por defecto 1 (Eduardo Piris)

// Parámetros de URL
$curso_sel   = intval($_GET['id_curso'] ?? 0);
$materia_sel = intval($_GET['id_materia'] ?? 0);
$mensaje_ok  = "";
$mensaje_err = "";

// ==========================================
// 1. CARGAR CURSOS Y MATERIAS DEL DOCENTE
// ==========================================
try {
    $stmt = $pdo->prepare("
        SELECT cmd.id, cmd.id_curso, c.nombre AS curso_nombre, cmd.id_materia, m.nombre AS materia_nombre
        FROM curso_materia_docente cmd
        INNER JOIN cursos c ON cmd.id_curso = c.id
        INNER JOIN materias m ON cmd.id_materia = m.id
        WHERE cmd.id_docente = :id_docente
    ");
    $stmt->execute([':id_docente' => $id_docente]);
    $catedras = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de estructura: " . $e->getMessage());
}

// Agrupar relaciones para los selectores anidados
$estructura_docente = [];
foreach ($catedras as $cat) {
    $estructura_docente[$cat['id_curso']]['nombre'] = $cat['curso_nombre'];
    $estructura_docente[$cat['id_curso']]['materias'][$cat['id_materia']] = [
        'id_cmd' => $cat['id'],
        'nombre' => $cat['materia_nombre']
    ];
}

// ==========================================
// 2. PROCESAR GUARDADO DE EVALUACIÓN Y NOTAS
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_evaluacion'])) {
    $titulo       = trim($_POST['titulo'] ?? '');
    $id_tipo      = intval($_POST['id_tipo_evaluacion'] ?? 0);
    $id_periodo   = intval($_POST['id_periodo'] ?? 0);
    $fecha        = $_POST['fecha_evaluacion'] ?? '';
    $descripcion  = trim($_POST['descripcion'] ?? '');
    $id_cmd       = intval($_POST['id_cmd'] ?? 0);
    $notas_al     = $_POST['notas'] ?? []; 

    if (empty($titulo) || $id_tipo === 0 || $id_periodo === 0 || empty($fecha) || $id_cmd === 0) {
        $mensaje_err = "Faltan completar campos mandatorios de la evaluación.";
    } else {
        try {
            $pdo->beginTransaction();

            // --- LÓGICA DE SUBIDA DEL ARCHIVO PDF ---
            $ruta_pdf_final = null;
            if (isset($_FILES['documento_examen']) && $_FILES['documento_examen']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['documento_examen']['tmp_name'];
                $fileName = $_FILES['documento_examen']['name'];
                $fileSize = $_FILES['documento_examen']['size'];
                $fileType = $_FILES['documento_examen']['type'];
                
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // Validar que realmente sea un PDF
                if ($fileExtension === 'pdf') {
                    // Validar tamaño máximo (Ej: 5MB)
                    if ($fileSize <= 5242880) {
                        $uploadFileDir = __DIR__ . '/../../uploads/evaluaciones/';
                        
                        // Si la carpeta no existe, la crea de forma automática
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        // Generar un nombre único para evitar duplicados accidentales
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if(move_uploaded_file($fileTmpPath, $dest_path)) {
                            // Guardamos la ruta relativa que usaremos para los enlaces de descarga
                            $ruta_pdf_final = 'uploads/evaluaciones/' . $newFileName;
                        } else {
                            throw new Exception("Error al mover el archivo PDF cargado al directorio destino.");
                        }
                    } else {
                        throw new Exception("El archivo PDF supera el límite de tamaño permitido (5 MB).");
                    }
                } else {
                    throw new Exception("Formato no válido. Solo se permiten archivos con extensión .pdf");
                }
            }

            // A. Insertar la evaluación principal (Agregada la columna archivo_pdf)
            $stmtEv = $pdo->prepare("
                INSERT INTO evaluaciones (id_curso_materia, id_tipo_evaluacion, id_periodo, titulo, descripcion, archivo_pdf, fecha_evaluacion)
                VALUES (:cmd, :tipo, :periodo, :titulo, :descr, :pdf, :fecha)
            ");
            $stmtEv->execute([
                ':cmd'     => $id_cmd,
                ':tipo'    => $id_tipo,
                ':periodo' => $id_periodo,
                ':titulo'  => $titulo,
                ':descr'   => !empty($descripcion) ? $descripcion : null,
                ':pdf'     => $ruta_pdf_final,
                ':fecha'   => $fecha
            ]);
            $id_evaluacion_nueva = $pdo->lastInsertId();

            // Preparar inserciones de calificaciones y trayectorias
            $stmtNota = $pdo->prepare("
                INSERT INTO calificaciones (id_evaluacion, id_alumno, id_curso_materia, id_tipo_evaluacion, id_periodo, nota_numerica, nota_conceptual, fecha_evaluacion, observaciones)
                VALUES (:id_eval, :id_al, :cmd, :tipo, :periodo, :num, :concept, :fecha, :obs)
            ");

            $stmtTray = $pdo->prepare("
                INSERT INTO trayectorias (id_alumno, id_curso_materia, id_estado_materia, promedio_final)
                VALUES (:id_al, :cmd, :estado, :prom)
                ON DUPLICATE KEY UPDATE 
                    id_estado_materia = VALUES(id_estado_materia),
                    promedio_final = VALUES(promedio_final)
            ");

            foreach ($notas_al as $id_alumno => $campos) {
                $num     = $campos['num'] !== '' ? floatval($campos['num']) : null;
                $concept = !empty($campos['conceptual']) ? $campos['conceptual'] : null;
                $obs     = !empty($campos['obs']) ? $campos['obs'] : null;

                if ($num !== null || $concept !== null) {
                    $stmtNota->execute([
                        ':id_eval' => $id_evaluacion_nueva,
                        ':id_al'   => $id_alumno,
                        ':cmd'     => $id_cmd,
                        ':tipo'    => $id_tipo,
                        ':periodo' => $id_periodo,
                        ':num'     => $num,
                        ':concept' => $concept,
                        ':fecha'   => $fecha,
                        ':obs'     => $obs
                    ]);

                    $stmtCalc = $pdo->prepare("
                        SELECT AVG(nota_numerica) FROM calificaciones 
                        WHERE id_alumno = :id_al AND id_curso_materia = :cmd AND nota_numerica IS NOT NULL
                    ");
                    $stmtCalc->execute([':id_al' => $id_alumno, ':cmd' => $id_cmd]);
                    $promedio = $stmtCalc->fetchColumn();
                    $promedio_final = $promedio ? round($promedio, 2) : null;

                    $id_estado = ($promedio_final >= 7) ? 1 : 2; 

                    $stmtTray->execute([
                        ':id_al'   => $id_alumno,
                        ':cmd'     => $id_cmd,
                        ':estado'  => $id_estado,
                        ':prom'    => $promedio_final
                    ]);
                }
            }

            // B. Registrar en Auditoría
            $stmtAud = $pdo->prepare("
                INSERT INTO auditoria (id_usuario, accion, tabla_afectada, id_registro, valor_nuevo, ip_origen)
                VALUES (:usr, 'REGISTRO_EVALUACION', 'evaluaciones', :reg, :val, :ip)
            ");
            $stmtAud->execute([
                ':usr' => $id_docente,
                ':reg' => $id_evaluacion_nueva,
                ':val' => json_encode(["titulo" => $titulo, "fecha" => $fecha, "id_curso_materia" => $id_cmd, "archivo" => $ruta_pdf_final]),
                ':ip'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $pdo->commit();
            $mensaje_ok = "La evaluación '<strong>" . htmlspecialchars($titulo) . "</strong>' y su archivo adjunto se asentaron exitosamente.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje_err = "Excepción en el procesamiento: " . $e->getMessage();
        }
    }
}

// 3. RECUPERAR AUXILIARES (TIPOS Y PERIODOS)
$tipos_eval = $pdo->query("SELECT id, nombre FROM tipos_evaluacion ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$periodos   = $pdo->query("SELECT id, nombre FROM periodos WHERE id_ciclo = 1")->fetchAll(PDO::FETCH_ASSOC);

// 4. LEER ALUMNOS ASOCIADOS SI EL CURSO FUE SELECCIONADO
$alumnos = [];
$id_cmd_actual = 0;
if ($curso_sel > 0) {
    $stmtAl = $pdo->prepare("
        SELECT u.id, u.nombre, u.apellido, u.dni 
        FROM inscripciones i
        INNER JOIN usuarios u ON i.id_alumno = u.id
        WHERE i.id_curso = :curso AND i.activo = 1
        ORDER BY u.apellido, u.nombre
    ");
    $stmtAl->execute([':curso' => $curso_sel]);
    $alumnos = $stmtAl->fetchAll(PDO::FETCH_ASSOC);

    if ($materia_sel > 0 && isset($estructura_docente[$curso_sel]['materias'][$materia_sel])) {
        $id_cmd_actual = $estructura_docente[$curso_sel]['materias'][$materia_sel]['id_cmd'];
    }
}
?>

<main class="main-content main">
    <?php include_once dirname(__DIR__, 2) . '/includes/topbar.php'; ?>

    <div class="dashboard-content">

        <?php if(!empty($mensaje_ok)): ?>
            <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;"><?= $mensaje_ok ?></div>
        <?php endif; ?>
        <?php if(!empty($mensaje_err)): ?>
            <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;"><?= $mensaje_err ?></div>
        <?php endif; ?>

        <div class="selector-card">
            <form method="GET" action="index.php">
                <input type="hidden" name="p" value="prof_evaluaciones">

                <div class="selector-group">
                    <div class="selector-field">
                        <label>1. Seleccionar Curso:</label>
                        <select name="id_curso" onchange="this.form.submit()">
                            <option value="">-- Seleccione --</option>
                            <?php foreach($estructura_docente as $id_c => $datos): ?>
                                <option value="<?= $id_c ?>" <?= ($curso_sel == $id_c) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($datos['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if($curso_sel > 0): ?>
                    <div class="selector-field">
                        <label>2. Seleccionar Materia:</label>
                        <select name="id_materia" onchange="this.form.submit()">
                            <option value="">-- Seleccione --</option>
                            <?php foreach($estructura_docente[$curso_sel]['materias'] as $id_m => $m_data): ?>
                                <option value="<?= $id_m ?>" <?= ($materia_sel == $id_m) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m_data['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($curso_sel > 0 && $materia_sel > 0 && $id_cmd_actual > 0): ?>
            <form method="POST" action="index.php?p=prof_evaluaciones&id_curso=<?= $curso_sel ?>&id_materia=<?= $materia_sel ?>" enctype="multipart/form-data">
                <input type="hidden" name="id_cmd" value="<?= $id_cmd_actual ?>">

                <div class="eval-card">
                    <h4>📋 Atributos de la Evaluación</h4>
                    <div class="form-grid">
                        <div class="form-group-custom wide">
                            <label>Tema / Título del Examen o Trabajo:</label>
                            <input type="text" name="titulo" required placeholder="Ej: Evaluación escrita de Funciones / TP N° 3">
                        </div>
                        <div class="form-group-custom">
                            <label>Tipo de Instancia:</label>
                            <select name="id_tipo_evaluacion" required>
                                <?php foreach($tipos_eval as $te): ?>
                                    <option value="<?= $te['id'] ?>"><?= htmlspecialchars($te['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label>Fecha de Toma:</label>
                            <input type="date" name="fecha_evaluacion" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label>Cuatrimestre:</label>
                            <select name="id_periodo" required>
                                <?php foreach($periodos as $per): 
                                    $nombre_cuat = $per['nombre'];
                                    if (stripos($nombre_cuat, '1') !== false || stripos($nombre_cuat, 'prim') !== false) {
                                        $nombre_cuat = '1° Cuatrimestre';
                                    } elseif (stripos($nombre_cuat, '2') !== false || stripos($nombre_cuat, 'seg') !== false) {
                                        $nombre_cuat = '2° Cuatrimestre';
                                    } else {
                                        $nombre_cuat = str_ireplace(['trimestre', 'periodo'], 'Cuatrimestre', $nombre_cuat);
                                    }
                                ?>
                                    <option value="<?= $per['id'] ?>"><?= htmlspecialchars($nombre_cuat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-grid" style="margin-top: 15px;">
                        <div class="form-group-custom wide">
                            <label>Contenidos Priorizados / Descripción:</label>
                            <textarea name="descripcion" rows="2" placeholder="Opcional. Breve detalle didáctico del examen..."></textarea>
                        </div>
                        <div class="form-group-custom wide" style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px dashed #cbd5e1;">
                            <label style="color: #1e293b; font-weight: 600;">📁 Adjuntar Archivo del Examen / Consigna (Opcional):</label>
                            <input type="file" name="documento_examen" accept=".pdf" style="border: none; background: transparent; padding: 5px 0;">
                            <small style="color: #64748b; display: block; margin-top: 4px;">Solo se admiten documentos en formato .PDF (Máx. 5MB)</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="guardar_evaluacion" class="btn-submit">
                            Confirmar y Guardar Evaluación Completa
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>