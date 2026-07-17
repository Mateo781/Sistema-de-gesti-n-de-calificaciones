<?php

function obtenerCursosDocente(PDO $pdo, int $id_docente): array {
    $queryCursos = "SELECT DISTINCT c.id, c.nombre, c.division, c.anio_escolar
                    FROM cursos c
                    JOIN curso_materia_docente cmd ON cmd.id_curso = c.id
                    WHERE cmd.id_docente = :id_docente";

    $stmtC = $pdo->prepare($queryCursos);
    $stmtC->execute([':id_docente' => $id_docente]);
    return $stmtC->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerMateriasCurso(PDO $pdo, int $id_docente, int $id_curso): array {
    $queryMaterias = "SELECT cmd.id as id_curso_materia, m.nombre as materia_nombre
                      FROM curso_materia_docente cmd
                      JOIN materias m ON cmd.id_materia = m.id
                      WHERE cmd.id_docente = :id_docente AND cmd.id_curso = :id_curso";

    $stmtM = $pdo->prepare($queryMaterias);
    $stmtM->execute([
        ':id_docente' => $id_docente,
        ':id_curso' => $id_curso
    ]);
    return $stmtM->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerAlumnosCurso(PDO $pdo, int $id_curso): array {
    $queryAlumnos = "SELECT u.id, u.apellido, u.nombre, u.dni
                     FROM usuarios u
                     JOIN inscripciones i ON i.id_alumno = u.id
                     WHERE i.id_curso = :id_curso
                     AND i.activo = 1
                     AND u.id_rol = 3
                     ORDER BY u.apellido ASC, u.nombre ASC";

    $stmtA = $pdo->prepare($queryAlumnos);
    $stmtA->execute([':id_curso' => $id_curso]);
    return $stmtA->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerPeriodos(PDO $pdo): array {
    return $pdo->query("SELECT id, nombre FROM periodos WHERE tipo = 'regular'")->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerTiposEvaluacion(PDO $pdo): array {
    return $pdo->query("SELECT id, nombre FROM tipos_evaluacion")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Guarda las calificaciones y actualiza trayectorias.
 * Lógica IDÉNTICA a la del bloque POST original (transacción, cálculo de promedio,
 * definición de estado, ON DUPLICATE KEY UPDATE). No se modificó nada de esto.
 */
function guardarNotas(PDO $pdo, int $id_curso_materia, int $id_periodo, int $id_tipo_eval, string $fecha_eval, array $notas): array {
    try {
        $pdo->beginTransaction();

        // Insertar la nota
        $stmtInsert = $pdo->prepare("INSERT INTO calificaciones
            (id_alumno, id_curso_materia, id_tipo_evaluacion, id_periodo, nota_numerica, nota_conceptual, fecha_evaluacion, observaciones)
            VALUES (:id_alumno, :id_cm, :id_tipo, :id_per, :nota_num, :nota_con, :fecha, :obs)");

        foreach ($notas as $id_alumno => $datos_nota) {

            // Si no hay nota, pasa al siguiente alumno
            if ($datos_nota['numeric'] === '' && empty($datos_nota['conceptual'])) continue;

            $stmtInsert->execute([
                ':id_alumno' => intval($id_alumno),
                ':id_cm'     => $id_curso_materia,
                ':id_tipo'   => $id_tipo_eval,
                ':id_per'    => $id_periodo,
                ':nota_num'  => ($datos_nota['numeric'] !== '') ? floatval($datos_nota['numeric']) : null,
                ':nota_con'  => !empty($datos_nota['conceptual']) ? $datos_nota['conceptual'] : null,
                ':fecha'     => $fecha_eval,
                ':obs'       => !empty($datos_nota['obs']) ? $datos_nota['obs'] : null
            ]);

            // Calcular el promedio
            $stmtProm = $pdo->prepare("SELECT AVG(nota_numerica) as prom FROM calificaciones
                                       WHERE id_alumno = :id_alumno AND id_curso_materia = :id_cm AND anulada = 0");
            $stmtProm->execute([
                ':id_alumno' => $id_alumno,
                ':id_cm' => $id_curso_materia
            ]);

            $resProm = $stmtProm->fetch(PDO::FETCH_ASSOC);
            $nuevo_promedio = $resProm['prom'] ? round($resProm['prom'], 2) : null;

            // Definir el estado de la materia
            $nuevo_estado = 2;

            if ($nuevo_promedio !== null) {
                $nuevo_estado = ($nuevo_promedio >= 7) ? 1 : 3;
            }

            // Actualizar la trayectoria
            $stmtTray = $pdo->prepare("INSERT INTO trayectorias (id_alumno, id_curso_materia, id_estado_materia, promedio_final)
                VALUES (:id_alumno, :id_cm, :id_estado, :promedio)
                ON DUPLICATE KEY UPDATE id_estado_materia = :id_estado, promedio_final = :promedio");

            $stmtTray->execute([
                ':id_alumno' => $id_alumno,
                ':id_cm'     => $id_curso_materia,
                ':id_estado' => $nuevo_estado,
                ':promedio'  => $nuevo_promedio
            ]);
        }

        $pdo->commit();
        return [
            'success' => true,
            'mensaje' => '¡Calificaciones registradas y trayectorias actualizadas con éxito!'
        ];

    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'mensaje' => 'Error al guardar: ' . $e->getMessage()
        ];
    }
}

function obtenerAlumnosTrayectoria(PDO $pdo, int $id_curso, int $id_curso_materia, int $id_materia): array {
    $query = "
        SELECT 
            u.id AS id_alumno, 
            u.apellido, 
            u.nombre, 
            u.dni,
            t.id_estado_materia,
            em.nombre AS estado_materia,
            t.promedio_final,
            t.observaciones,
            (SELECT COUNT(*) FROM intensificaciones i WHERE i.id_alumno = u.id AND i.id_curso_materia = :cmd) AS tiene_intensificacion,
            (SELECT COUNT(*) FROM recursadas r WHERE r.id_alumno = u.id AND r.id_materia = :materia) AS tiene_recursada
        FROM usuarios u
        JOIN inscripciones ins ON ins.id_alumno = u.id
        LEFT JOIN trayectorias t ON t.id_alumno = u.id AND t.id_curso_materia = :cmd
        LEFT JOIN estados_materia em ON t.id_estado_materia = em.id
        WHERE ins.id_curso = :curso 
          AND ins.activo = 1 
          AND u.id_rol = 3
        ORDER BY u.apellido ASC, u.nombre ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':cmd' => $id_curso_materia,
        ':materia' => $id_materia,
        ':curso' => $id_curso
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function guardarTrayectoriaRite(
    PDO $pdo, 
    int $id_docente, 
    int $id_alumno, 
    int $id_curso_materia, 
    int $id_estado_materia, 
    ?float $promedio, 
    string $observaciones, 
    bool $chk_intensificacion, 
    int $id_periodo_int, 
    string $motivo_int, 
    bool $chk_recursada, 
    string $motivo_rec, 
    int $id_curso_sel
): array {
    try {
        $pdo->beginTransaction();

        // 1. Guardar o actualizar la trayectoria
        $stmt = $pdo->prepare("
            INSERT INTO trayectorias (id_alumno, id_curso_materia, id_estado_materia, promedio_final, observaciones)
            VALUES (:id_al, :cmd, :estado, :prom, :obs)
            ON DUPLICATE KEY UPDATE 
                id_estado_materia = VALUES(id_estado_materia),
                promedio_final = VALUES(promedio_final),
                observaciones = VALUES(observaciones)
        ");
        $stmt->execute([
            ':id_al' => $id_alumno,
            ':cmd' => $id_curso_materia,
            ':estado' => $id_estado_materia,
            ':prom' => $promedio,
            ':obs' => !empty($observaciones) ? $observaciones : null
        ]);

        // 2. Si se marcó intensificación
        if ($chk_intensificacion && $id_periodo_int > 0) {
            $stmtCheck = $pdo->prepare("SELECT id FROM intensificaciones WHERE id_alumno = :al AND id_curso_materia = :cmd AND id_periodo = :per");
            $stmtCheck->execute([':al' => $id_alumno, ':cmd' => $id_curso_materia, ':per' => $id_periodo_int]);
            if (!$stmtCheck->fetch()) {
                $stmtInt = $pdo->prepare("
                    INSERT INTO intensificaciones (id_alumno, id_curso_materia, id_periodo, motivo, id_estado_materia)
                    VALUES (:al, :cmd, :per, :motivo, :estado)
                ");
                $stmtInt->execute([
                    ':al' => $id_alumno,
                    ':cmd' => $id_curso_materia,
                    ':per' => $id_periodo_int,
                    ':motivo' => !empty($motivo_int) ? $motivo_int : 'Período de intensificación académica',
                    ':estado' => $id_estado_materia
                ]);
            }
        }

        // 3. Si se marcó recursada
        if ($chk_recursada) {
            $stmtMat = $pdo->prepare("SELECT id_materia FROM curso_materia_docente WHERE id = :cmd");
            $stmtMat->execute([':cmd' => $id_curso_materia]);
            $id_materia = $stmtMat->fetchColumn();

            if ($id_materia) {
                $ciclo_actual = $pdo->query("SELECT id FROM ciclos_lectivos WHERE activo = 1 LIMIT 1")->fetchColumn() ?: 1;
                
                $stmtCheckRec = $pdo->prepare("SELECT id FROM recursadas WHERE id_alumno = :al AND id_materia = :mat AND id_ciclo_original = :cic");
                $stmtCheckRec->execute([':al' => $id_alumno, ':mat' => $id_materia, ':cic' => $ciclo_actual]);
                if (!$stmtCheckRec->fetch()) {
                    $stmtRec = $pdo->prepare("
                        INSERT INTO recursadas (id_alumno, id_materia, id_ciclo_original, id_ciclo_recursada, id_curso_recursada, motivo)
                        VALUES (:al, :mat, :cic, :cic_rec, :cur_rec, :motivo)
                    ");
                    $stmtRec->execute([
                        ':al' => $id_alumno,
                        ':mat' => $id_materia,
                        ':cic' => $ciclo_actual,
                        ':cic_rec' => $ciclo_actual,
                        ':cur_rec' => $id_curso_sel,
                        ':motivo' => !empty($motivo_rec) ? $motivo_rec : 'Debe recursar la materia'
                    ]);
                }
            }
        }

        // 4. Registrar en Auditoría
        $stmtAud = $pdo->prepare("
            INSERT INTO auditoria (id_usuario, accion, tabla_afectada, id_registro, ip_origen)
            VALUES (:usr, 'ACTUALIZAR_TRAYECTORIA', 'trayectorias', :reg, :ip)
        ");
        $stmtAud->execute([
            ':usr' => $id_docente,
            ':reg' => $id_alumno,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        $pdo->commit();
        return [
            'success' => true,
            'mensaje' => 'La trayectoria del alumno fue actualizada con éxito.'
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'mensaje' => 'Error al actualizar trayectoria: ' . $e->getMessage()
        ];
    }
}