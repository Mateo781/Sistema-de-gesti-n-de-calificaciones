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