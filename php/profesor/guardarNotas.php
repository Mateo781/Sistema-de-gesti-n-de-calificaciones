<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/funciones_calificaciones.php';

header('Content-Type: application/json; charset=utf-8');

$id_docente = $_SESSION['usuario_id'] ?? null;

if (!$id_docente) {
    echo json_encode(['success' => false, 'mensaje' => 'Error: Sesión docente no válida.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido.']);
    exit;
}

$id_curso_materia = intval($_POST['id_curso_materia'] ?? 0);
$id_periodo = intval($_POST['id_periodo'] ?? 0);
$id_tipo_eval = intval($_POST['id_tipo_evaluacion'] ?? 0);
$fecha_eval = $_POST['fecha_evaluacion'] ?? date('Y-m-d');
$notas = $_POST['notas'] ?? [];

$resultado = guardarNotas($pdo, $id_curso_materia, $id_periodo, $id_tipo_eval, $fecha_eval, $notas);

echo json_encode($resultado);