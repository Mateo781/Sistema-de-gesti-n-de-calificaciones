<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../paginas/login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_rol = $_SESSION['usuario_rol'];
$accion = $_GET['accion'] ?? '';

// Enviar Criterios (Solo Jefe de Departamento - Rol 6)
if ($accion === 'enviar_propuesta' && $id_rol == 6) {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    
    // Atrapamos la materia seleccionada
    $id_materia = isset($_POST['id_materia']) ? intval($_POST['id_materia']) : 0;

    // Verificamos que no esté vacío y que se haya seleccionado una materia válida
    if (!empty($titulo) && !empty($descripcion) && $id_materia > 0) {
        
        // Agregamos id_materia a la consulta SQL
        $stmt = $pdo->prepare("INSERT INTO criterios_evaluacion (id_usuario_jefe, id_materia, titulo, descripcion, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$id_usuario, $id_materia, $titulo, $descripcion]);
        
        header("Location: ../index.php?p=jefe_historial_criterios&msg=Propuesta enviada con éxito");
    } else {
        header("Location: ../index.php?p=jefe_crear_criterio&err=Campos incompletos");
    }
    exit;
}

// Aprobar o Rechazar (Solo Director - Rol 7)
if (($accion === 'aprobar' || $accion === 'rechazar') && $id_rol == 7) {
    $id_criterio = intval($_GET['id']);
    $nuevo_estado = ($accion === 'aprobar') ? 'aprobado' : 'rechazado';
    $observaciones = trim($_POST['observaciones'] ?? '');

    $stmt = $pdo->prepare("UPDATE criterios_evaluacion SET estado = ?, observaciones_director = ? WHERE id = ?");
    $stmt->execute([$nuevo_estado, $observaciones, $id_criterio]);

    header("Location: ../index.php?p=director_criterios&msg=Criterio actualizado");
    exit;
}

// Redirección por defecto si no coincide ninguna acción/rol
header("Location: ../index.php");
exit;
?>