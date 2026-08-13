<?php
session_start();
require_once "db.php"; 

// Cortamos si no entran por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido.");
}

// Filtro estricto: solo preceptores (rol 5)
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) {
    die("No tienes permisos para realizar esta acción.");
}

$id_alumno = $_POST['id_alumno'] ?? null;
$observacion = $_POST['observacion'] ?? '';
$id_preceptor = $_SESSION['usuario_id']; 

if ($id_alumno && !empty(trim($observacion))) {
    
    // Metemos la nueva observación con la fecha actual del servidor
    $sql = "INSERT INTO observaciones (id_alumno, id_preceptor, observacion, fecha) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id_alumno, $id_preceptor, $observacion])) {
        // Volvemos a la pantalla de alumnos arrastrando el curso para no perder el foco
        header("Location: ../index.php?p=preceptor_alumnos&id_curso=" . ($_GET['id_curso'] ?? 1) . "&status=success");
        exit;
    } else {
        echo "Error al guardar la observación.";
    }

} else {
    echo "El campo de observación no puede estar vacío.";
}
?>