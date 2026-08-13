<?php
// Funciones de lógica para el módulo de calificaciones

function obtenerCursosDocente($pdo, $id_docente) {
    try {
        $sql = "SELECT DISTINCT c.id, c.nombre, c.division, c.anio_escolar 
                FROM cursos c
                INNER JOIN curso_materia_docente cmd ON cmd.id_curso = c.id
                WHERE cmd.id_docente = :id_docente
                ORDER BY c.anio_escolar, c.division";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_docente' => $id_docente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerCursosDocente: " . $e->getMessage());
        return [];
    }
}

function obtenerMateriasPorCursoDocente($pdo, $id_docente, $id_curso) {
    try {
        $sql = "SELECT cmd.id AS id_curso_materia, m.id AS id_materia, m.nombre 
                FROM materias m
                INNER JOIN curso_materia_docente cmd ON cmd.id_materia = m.id
                WHERE cmd.id_docente = :id_docente AND cmd.id_curso = :id_curso AND m.activo = 1
                ORDER BY m.nombre";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_docente' => $id_docente,
            ':id_curso'   => $id_curso
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerMateriasPorCursoDocente: " . $e->getMessage());
        return [];
    }
}

function obtenerAlumnosNotas($pdo, $id_curso, $id_curso_materia) {
    try {
        $sql = "SELECT u.id, u.apellido, u.nombre, 
                       cn.n1, cn.n2, cn.n3, cn.n4, cn.n5, cn.promedio_general
                FROM usuarios u
                INNER JOIN inscripciones i ON i.id_alumno = u.id
                LEFT JOIN profesor_criterios_notas cn ON cn.id_alumno = u.id 
                     AND cn.id_curso_materia = :id_curso_materia
                WHERE i.id_curso = :id_curso AND u.id_rol = 3 AND i.activo = 1
                ORDER BY u.apellido, u.nombre";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_curso'         => $id_curso,
            ':id_curso_materia' => $id_curso_materia
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerAlumnosNotas: " . $e->getMessage());
        return [];
    }
}