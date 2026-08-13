<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once '../db.php';

$id_docente = $_SESSION['usuario_id'] ?? null;
if (!$id_docente) {
    echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida.']);
    exit;
}

$id_curso_materia = isset($_POST['id_curso_materia']) ? intval($_POST['id_curso_materia']) : 0;
$id_periodo = isset($_POST['id_periodo']) ? intval($_POST['id_periodo']) : 1; 
$notas = isset($_POST['notas']) ? $_POST['notas'] : [];
$promedios = isset($_POST['promedio_final']) ? $_POST['promedio_final'] : [];

if ($id_curso_materia <= 0 || empty($notas)) {
    echo json_encode(['success' => false, 'mensaje' => 'No hay datos de notas para guardar.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO profesor_criterios_notas 
            (profesor_id, id_curso_materia, id_alumno, periodo_id, n1, n2, n3, n4, n5, promedio_general) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            n1 = VALUES(n1), n2 = VALUES(n2), n3 = VALUES(n3), n4 = VALUES(n4), n5 = VALUES(n5), promedio_general = VALUES(promedio_general)";
    
    $stmt = $pdo->prepare($sql);

    foreach ($notas as $id_alumno => $criterios) {
        $id_alumno = intval($id_alumno);

        // Extraer los 5 criterios de evaluación
        $n1 = isset($criterios[1]) && $criterios[1] !== '' ? floatval($criterios[1]) : null;
        $n2 = isset($criterios[2]) && $criterios[2] !== '' ? floatval($criterios[2]) : null;
        $n3 = isset($criterios[3]) && $criterios[3] !== '' ? floatval($criterios[3]) : null;
        $n4 = isset($criterios[4]) && $criterios[4] !== '' ? floatval($criterios[4]) : null;
        $n5 = isset($criterios[5]) && $criterios[5] !== '' ? floatval($criterios[5]) : null;

        // Calcular o recuperar el promedio final
        if (isset($promedios[$id_alumno]) && $promedios[$id_alumno] !== '') {
            $promedio_final = floatval($promedios[$id_alumno]);
        } else {
            $valores_validos = array_filter([$n1, $n2, $n3, $n4, $n5], function($v) { return $v !== null; });
            $promedio_final = count($valores_validos) > 0 ? array_sum($valores_validos) / count($valores_validos) : null;
        }

        $stmt->execute([
            $id_docente,
            $id_curso_materia,
            $id_alumno,
            $id_periodo,
            $n1,
            $n2,
            $n3,
            $n4,
            $n5,
            $promedio_final
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'mensaje' => '¡Calificaciones guardadas correctamente!']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'mensaje' => 'Error en base de datos: ' . $e->getMessage()]);
}