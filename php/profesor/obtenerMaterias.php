<?php
session_start();
require_once '../db.php';
require_once '../funciones_calificaciones.php';

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;
$id_docente = isset($_GET['id_docente']) ? intval($_GET['id_docente']) : 0;

if ($id_curso <= 0 || $id_docente <= 0) {
    echo '<option value="">-- Parámetros inválidos --</option>';
    exit;
}

$materias = obtenerMateriasPorCursoDocente($pdo, $id_docente, $id_curso);

echo '<option value="">-- Seleccionar Materia --</option>';
foreach ($materias as $materia) {
    echo '<option value="' . $materia['id_curso_materia'] . '">' . htmlspecialchars($materia['nombre']) . '</option>';
}