<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/funciones_calificaciones.php';

header('Content-Type: text/html; charset=utf-8');

$id_docente = $_SESSION['usuario_id'] ?? null;

if (!$id_docente) {
    http_response_code(403);
    echo '<div class="table-card" style="padding: 30px; text-align: center; color: #c62828;">Sesión docente no válida.</div>';
    exit;
}

$id_curso = intval($_GET['id_curso'] ?? 0);
$materia_seleccionada = intval($_GET['id_materia'] ?? 0);

if (!$id_curso || !$materia_seleccionada) {
    exit;
}

$alumnos = obtenerAlumnosCurso($pdo, $id_curso);

if (empty($alumnos)) {
    echo '<div class="table-card" style="padding: 30px; text-align: center; color: var(--t2);">
            No se encontraron alumnos activos inscriptos en este curso.
          </div>';
    exit;
}

$periodos = obtenerPeriodos($pdo);
$tipos_eval = obtenerTiposEvaluacion($pdo);

require __DIR__ . '/../../paginas/profesor/tabla_calificaciones.php';