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
    echo '<option value="">-- Sesión no válida --</option>';
    exit;
}

$id_curso = intval($_GET['id_curso'] ?? 0);

echo '<option value="">-- Seleccionar Materia --</option>';

if (!$id_curso) {
    exit;
}

$materias = obtenerMateriasCurso($pdo, $id_docente, $id_curso);

foreach ($materias as $m) {
    echo '<option value="' . htmlspecialchars($m['id_curso_materia']) . '">'
        . htmlspecialchars($m['materia_nombre'])
        . '</option>';
}