<?php
/**
 * Funciones de datos para el Dashboard del Docente.
 * TODAS las queries usan datos reales de sgc_db (usuarios, cursos, curso_materia_docente,
 * materias, inscripciones, calificaciones, evaluaciones, trayectorias, periodos,
 * tipos_evaluacion, auditoria). No hay datos simulados.
 *
 * Nota: 'Asistencia promedio' y 'Comunicaciones' se excluyeron a pedido: sgc_db no tiene
 * tablas de asistencias ni de comunicados todavía.
 */

/**
 * Tarjetas de estadísticas principales.
 */
function obtenerEstadisticasGenerales(PDO $pdo, int $id_docente): array {
    // Cursos asignados
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT id_curso) AS total
                            FROM curso_materia_docente
                            WHERE id_docente = :id_docente");
    $stmt->execute([':id_docente' => $id_docente]);
    $cursos_asignados = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Materias asignadas
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT id_materia) AS total
                            FROM curso_materia_docente
                            WHERE id_docente = :id_docente");
    $stmt->execute([':id_docente' => $id_docente]);
    $materias_asignadas = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Alumnos a cargo (distintos, en cursos donde el docente dicta)
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT i.id_alumno) AS total
                            FROM inscripciones i
                            JOIN curso_materia_docente cmd ON cmd.id_curso = i.id_curso
                            WHERE cmd.id_docente = :id_docente AND i.activo = 1");
    $stmt->execute([':id_docente' => $id_docente]);
    $alumnos_a_cargo = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Evaluaciones pendientes (fecha futura o de hoy)
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total
                            FROM evaluaciones e
                            JOIN curso_materia_docente cmd ON cmd.id = e.id_curso_materia
                            WHERE cmd.id_docente = :id_docente AND e.fecha_evaluacion >= CURDATE()");
    $stmt->execute([':id_docente' => $id_docente]);
    $evaluaciones_pendientes = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calificaciones cargadas (no anuladas)
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total
                            FROM calificaciones c
                            JOIN curso_materia_docente cmd ON cmd.id = c.id_curso_materia
                            WHERE cmd.id_docente = :id_docente AND c.anulada = 0");
    $stmt->execute([':id_docente' => $id_docente]);
    $calificaciones_cargadas = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Promedio general (a partir de las trayectorias ya calculadas)
    $stmt = $pdo->prepare("SELECT AVG(t.promedio_final) AS promedio
                            FROM trayectorias t
                            JOIN curso_materia_docente cmd ON cmd.id = t.id_curso_materia
                            WHERE cmd.id_docente = :id_docente AND t.promedio_final IS NOT NULL");
    $stmt->execute([':id_docente' => $id_docente]);
    $promedio_raw = $stmt->fetch(PDO::FETCH_ASSOC)['promedio'];
    $promedio_general = $promedio_raw !== null ? round((float) $promedio_raw, 2) : null;

    return [
        'alumnos_a_cargo'         => $alumnos_a_cargo,
        'cursos_asignados'        => $cursos_asignados,
        'materias_asignadas'      => $materias_asignadas,
        'evaluaciones_pendientes' => $evaluaciones_pendientes,
        'calificaciones_cargadas' => $calificaciones_cargadas,
        'promedio_general'        => $promedio_general,
    ];
}

/**
 * Gráfico 1: Promedio por curso (barras).
 */
function obtenerPromedioPorCurso(PDO $pdo, int $id_docente): array {
    $stmt = $pdo->prepare("SELECT CONCAT(cu.nombre, ' ', cu.division) AS curso,
                                   AVG(t.promedio_final) AS promedio
                            FROM trayectorias t
                            JOIN curso_materia_docente cmd ON cmd.id = t.id_curso_materia
                            JOIN cursos cu ON cu.id = cmd.id_curso
                            WHERE cmd.id_docente = :id_docente AND t.promedio_final IS NOT NULL
                            GROUP BY cu.id
                            ORDER BY cu.nombre, cu.division");
    $stmt->execute([':id_docente' => $id_docente]);
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];
    foreach ($filas as $f) {
        $labels[] = $f['curso'];
        $data[] = round((float) $f['promedio'], 2);
    }

    return ['labels' => $labels, 'data' => $data];
}

/**
 * Gráfico 2: Estado de carga de calificaciones (doughnut).
 * Se calcula por evaluación real: compara alumnos inscriptos en el curso
 * contra calificaciones ya cargadas para esa evaluación.
 */
function obtenerEstadoCargaCalificaciones(PDO $pdo, int $id_docente): array {
    $stmt = $pdo->prepare("SELECT e.id, e.fecha_evaluacion, cmd.id_curso,
                                   (SELECT COUNT(*) FROM calificaciones c
                                     WHERE c.id_evaluacion = e.id AND c.anulada = 0) AS cargadas
                            FROM evaluaciones e
                            JOIN curso_materia_docente cmd ON cmd.id = e.id_curso_materia
                            WHERE cmd.id_docente = :id_docente");
    $stmt->execute([':id_docente' => $id_docente]);
    $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cache de alumnos activos por curso para no repetir queries
    $alumnosPorCurso = [];
    $stmtAlumnos = $pdo->prepare("SELECT COUNT(*) AS total FROM inscripciones WHERE id_curso = :id_curso AND activo = 1");

    $completadas = 0;
    $pendientes = 0;
    $sin_comenzar = 0;

    foreach ($evaluaciones as $ev) {
        $idCurso = $ev['id_curso'];

        if (!isset($alumnosPorCurso[$idCurso])) {
            $stmtAlumnos->execute([':id_curso' => $idCurso]);
            $alumnosPorCurso[$idCurso] = (int) $stmtAlumnos->fetch(PDO::FETCH_ASSOC)['total'];
        }

        $totalAlumnos = $alumnosPorCurso[$idCurso];
        $cargadas = (int) $ev['cargadas'];

        if ($ev['fecha_evaluacion'] > date('Y-m-d')) {
            $sin_comenzar++;
        } elseif ($totalAlumnos > 0 && $cargadas >= $totalAlumnos) {
            $completadas++;
        } else {
            $pendientes++;
        }
    }

    return [
        'completadas'   => $completadas,
        'pendientes'    => $pendientes,
        'sin_comenzar'  => $sin_comenzar,
    ];
}

/**
 * Gráfico 3: Evolución del promedio (línea), agrupado por mes real de evaluación.
 */
function obtenerEvolucionPromedio(PDO $pdo, int $id_docente): array {
    $stmt = $pdo->prepare("SELECT DATE_FORMAT(c.fecha_evaluacion, '%Y-%m') AS mes,
                                   AVG(c.nota_numerica) AS promedio
                            FROM calificaciones c
                            JOIN curso_materia_docente cmd ON cmd.id = c.id_curso_materia
                            WHERE cmd.id_docente = :id_docente
                              AND c.anulada = 0
                              AND c.nota_numerica IS NOT NULL
                            GROUP BY mes
                            ORDER BY mes ASC");
    $stmt->execute([':id_docente' => $id_docente]);
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $meses = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun',
              '07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];

    $labels = [];
    $data = [];
    foreach ($filas as $f) {
        [$anio, $mes] = explode('-', $f['mes']);
        $labels[] = ($meses[$mes] ?? $mes) . ' ' . $anio;
        $data[] = round((float) $f['promedio'], 2);
    }

    return ['labels' => $labels, 'data' => $data];
}

/**
 * Próximas actividades: evaluaciones reales con fecha futura o de hoy.
 */
function obtenerProximasEvaluaciones(PDO $pdo, int $id_docente, int $limite = 6): array {
    $stmt = $pdo->prepare("SELECT e.id, e.titulo, e.fecha_evaluacion,
                                   m.nombre AS materia, CONCAT(cu.nombre, ' ', cu.division) AS curso,
                                   te.nombre AS tipo_evaluacion
                            FROM evaluaciones e
                            JOIN curso_materia_docente cmd ON cmd.id = e.id_curso_materia
                            JOIN materias m ON m.id = cmd.id_materia
                            JOIN cursos cu ON cu.id = cmd.id_curso
                            JOIN tipos_evaluacion te ON te.id = e.id_tipo_evaluacion
                            WHERE cmd.id_docente = :id_docente AND e.fecha_evaluacion >= CURDATE()
                            ORDER BY e.fecha_evaluacion ASC
                            LIMIT :limite");
    $stmt->bindValue(':id_docente', $id_docente, PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hoy = new DateTime(date('Y-m-d'));

    foreach ($filas as &$f) {
        $fechaEval = new DateTime($f['fecha_evaluacion']);
        $diff = (int) $hoy->diff($fechaEval)->format('%r%a');

        if ($diff <= 2) {
            $f['prioridad'] = 'alta';
        } elseif ($diff <= 7) {
            $f['prioridad'] = 'media';
        } else {
            $f['prioridad'] = 'baja';
        }
        $f['dias_restantes'] = $diff;
    }
    unset($f);

    return $filas;
}

/**
 * Notificaciones reales: alumnos sin calificar, cierre de período próximo,
 * y últimas evaluaciones registradas (desde auditoria).
 */
function obtenerNotificaciones(PDO $pdo, int $id_docente): array {
    $notificaciones = [];

    // 1) Evaluaciones pasadas con alumnos sin calificar todavía
    $stmt = $pdo->prepare("SELECT e.id, e.titulo, e.fecha_evaluacion, cmd.id_curso,
                                   m.nombre AS materia,
                                   (SELECT COUNT(*) FROM calificaciones c
                                     WHERE c.id_evaluacion = e.id AND c.anulada = 0) AS cargadas
                            FROM evaluaciones e
                            JOIN curso_materia_docente cmd ON cmd.id = e.id_curso_materia
                            JOIN materias m ON m.id = cmd.id_materia
                            WHERE cmd.id_docente = :id_docente AND e.fecha_evaluacion <= CURDATE()");
    $stmt->execute([':id_docente' => $id_docente]);
    $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtAlumnos = $pdo->prepare("SELECT COUNT(*) AS total FROM inscripciones WHERE id_curso = :id_curso AND activo = 1");

    foreach ($evaluaciones as $ev) {
        $stmtAlumnos->execute([':id_curso' => $ev['id_curso']]);
        $totalAlumnos = (int) $stmtAlumnos->fetch(PDO::FETCH_ASSOC)['total'];
        $faltan = $totalAlumnos - (int) $ev['cargadas'];

        if ($faltan > 0) {
            $notificaciones[] = [
                'icono'  => 'fa-user-clock',
                'color'  => 'warning',
                'texto'  => "Faltan cargar {$faltan} calificaciones de \"{$ev['titulo']}\" ({$ev['materia']})",
                'fecha'  => $ev['fecha_evaluacion'],
            ];
        }
    }

    // 2) Cierre de período próximo (dentro de 14 días)
    $stmt = $pdo->query("SELECT nombre, fecha_fin FROM periodos
                          WHERE tipo = 'regular' AND fecha_fin >= CURDATE()
                          AND fecha_fin <= DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                          ORDER BY fecha_fin ASC");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $per) {
        $notificaciones[] = [
            'icono' => 'fa-calendar-check',
            'color' => 'info',
            'texto' => "Se aproxima el cierre de \"{$per['nombre']}\"",
            'fecha' => $per['fecha_fin'],
        ];
    }

    // 3) Últimas evaluaciones registradas por este docente (auditoría real)
    $stmt = $pdo->prepare("SELECT accion, valor_nuevo, fecha
                            FROM auditoria
                            WHERE id_usuario = :id_docente AND accion = 'REGISTRO_EVALUACION'
                            ORDER BY fecha DESC LIMIT 3");
    $stmt->execute([':id_docente' => $id_docente]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aud) {
        $detalle = json_decode($aud['valor_nuevo'], true);
        $titulo = $detalle['titulo'] ?? 'evaluación';
        $notificaciones[] = [
            'icono' => 'fa-file-circle-check',
            'color' => 'success',
            'texto' => "Registraste la evaluación \"{$titulo}\"",
            'fecha' => $aud['fecha'],
        ];
    }

    // Ordenar todas por fecha descendente (más reciente primero)
    usort($notificaciones, fn($a, $b) => strcmp($b['fecha'], $a['fecha']));

    return array_slice($notificaciones, 0, 8);
}

/**
 * Tabla de cursos: resumen por curso/materia asignados al docente.
 */
function obtenerCursosResumen(PDO $pdo, int $id_docente): array {
    $stmt = $pdo->prepare("SELECT cmd.id AS id_curso_materia,
                                   CONCAT(cu.nombre, ' ', cu.division) AS curso,
                                   m.nombre AS materia,
                                   (SELECT COUNT(*) FROM inscripciones i WHERE i.id_curso = cu.id AND i.activo = 1) AS alumnos,
                                   (SELECT AVG(t.promedio_final) FROM trayectorias t WHERE t.id_curso_materia = cmd.id) AS promedio
                            FROM curso_materia_docente cmd
                            JOIN cursos cu ON cu.id = cmd.id_curso
                            JOIN materias m ON m.id = cmd.id_materia
                            WHERE cmd.id_docente = :id_docente
                            ORDER BY cu.nombre, cu.division, m.nombre");
    $stmt->execute([':id_docente' => $id_docente]);
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($filas as &$f) {
        $f['promedio'] = $f['promedio'] !== null ? round((float) $f['promedio'], 2) : null;

        if ($f['promedio'] === null) {
            $f['estado'] = 'sin-datos';
        } elseif ($f['promedio'] >= 7) {
            $f['estado'] = 'aprobado';
        } else {
            $f['estado'] = 'en-proceso';
        }
    }
    unset($f);

    return $filas;
}

/**
 * Actividad reciente real, desde la tabla de auditoría.
 */
function obtenerActividadReciente(PDO $pdo, int $id_docente, int $limite = 8): array {
    $stmt = $pdo->prepare("SELECT accion, tabla_afectada, valor_nuevo, fecha
                            FROM auditoria
                            WHERE id_usuario = :id_docente
                            ORDER BY fecha DESC
                            LIMIT :limite");
    $stmt->bindValue(':id_docente', $id_docente, PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $descripciones = [
        'LOGIN'                => 'Iniciaste sesión en el sistema',
        'REGISTRO_EVALUACION'  => 'Registraste una nueva evaluación',
        'REGISTRO'             => 'Se registró tu cuenta',
    ];

    foreach ($filas as &$f) {
        $f['descripcion'] = $descripciones[$f['accion']] ?? ('Acción: ' . $f['accion']);
    }
    unset($f);

    return $filas;
}

/**
 * Eventos reales para el calendario (evaluaciones con fecha).
 */
function obtenerEventosCalendario(PDO $pdo, int $id_docente): array {
    $stmt = $pdo->prepare("SELECT e.fecha_evaluacion AS fecha, e.titulo,
                                   m.nombre AS materia, CONCAT(cu.nombre, ' ', cu.division) AS curso
                            FROM evaluaciones e
                            JOIN curso_materia_docente cmd ON cmd.id = e.id_curso_materia
                            JOIN materias m ON m.id = cmd.id_materia
                            JOIN cursos cu ON cu.id = cmd.id_curso
                            WHERE cmd.id_docente = :id_docente");
    $stmt->execute([':id_docente' => $id_docente]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}