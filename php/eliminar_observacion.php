<?php
session_start();
require_once "db.php";

// Control de acceso: solo preceptores (rol 5)
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) {
    die("Acceso denegado.");
}

// Capturar parámetros de la URL
$id_observacion = $_GET['id'] ?? null;
$id_alumno = $_GET['id_alumno'] ?? null;

// Ejecutar la baja si viene el ID
if ($id_observacion) {
    $stmt = $pdo->prepare("DELETE FROM observaciones WHERE id = ?");
    $stmt->execute([$id_observacion]);
}

// Redirección de retorno a la lista del curso
$id_curso_retorno = $_GET['id_curso'] ?? 1;
header("Location: ../index.php?p=preceptor_alumnos&id_curso=" . $id_curso_retorno);
exit;
?>