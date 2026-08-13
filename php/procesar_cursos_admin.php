<?php
session_start();
require_once "db.php";

// Filtro de seguridad: solo administradores (rol 1)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    die("Acceso no autorizado.");
}

$id_admin = $_SESSION['usuario_id'];
$accion = $_GET['accion'] ?? '';

//  Crear un nuevo curso en el ciclo lectivo actual
if ($accion === 'crear_curso' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $anio_escolar = intval($_POST['anio_escolar']);
    $division = trim($_POST['division']);
    $id_ciclo = intval($_POST['id_ciclo']); 

    try {
        $stmt = $pdo->prepare("INSERT INTO cursos (id_ciclo, nombre, anio_escolar, division) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_ciclo, $nombre, $anio_escolar, $division]);

        $detalle_log = "Creó el curso: $nombre.";
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Estructura Escolar', ?)");
        $stmtLog->execute([$id_admin, $detalle_log]);

        header("Location: ../index.php?p=admin_cursos&msg=Curso creado con éxito");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error al crear curso.");
    }
    exit;
}

//  Registrar una nueva materia con su descripción
if ($accion === 'crear_materia' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_materia = trim($_POST['nombre_materia']);
    $descripcion_materia = trim($_POST['descripcion_materia']); 

    try {
        $stmt = $pdo->prepare("INSERT INTO materias (nombre, descripcion, activo) VALUES (?, ?, 1)");
        $stmt->execute([$nombre_materia, $descripcion_materia]);

        $detalle_log = "Agregó la materia: '$nombre_materia' con su descripción correspondiente.";
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Plan de Estudios', ?)");
        $stmtLog->execute([$id_admin, $detalle_log]);

        header("Location: ../index.php?p=admin_cursos&msg=Materia registrada correctamente");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error al registrar materia.");
    }
    exit;
}

//  Asignar un docente a una materia dentro de un curso
if ($accion === 'asignar_profesor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_curso = intval($_POST['id_curso']);
    $id_materia = intval($_POST['id_materia']);
    $id_profesor = intval($_POST['id_profesor']);

    try {
        $stmt = $pdo->prepare("INSERT INTO curso_materia_docente (id_curso, id_materia, id_docente) VALUES (?, ?, ?)");
        $stmt->execute([$id_curso, $id_materia, $id_profesor]);

        // Buscamos los nombres para armar un log de auditoría detallado
        $c = $pdo->query("SELECT nombre FROM cursos WHERE id = $id_curso")->fetch();
        $m = $pdo->query("SELECT nombre FROM materias WHERE id = $id_materia")->fetch();
        $p = $pdo->query("SELECT nombre, apellido FROM usuarios WHERE id = $id_profesor")->fetch();

        $detalle_log = "Asignó al Profesor " . $p['apellido'] . " en la materia '" . $m['nombre'] . "' para el curso " . $c['nombre'] . ".";
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Asignación de Cátedra', ?)");
        $stmtLog->execute([$id_admin, $detalle_log]);

        header("Location: ../index.php?p=admin_cursos&msg=Profesor asignado con éxito");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error: Ese curso ya tiene esa materia asignada.");
    }
    exit;
}

//  Romper el vínculo entre un docente y una materia
if ($accion === 'eliminar_asignacion') {
    $id = intval($_GET['id']);
    try {
        $info = $pdo->query("SELECT m.nombre as mat, c.nombre as cur, u.apellido FROM curso_materia_docente cmd JOIN materias m ON cmd.id_materia = m.id JOIN cursos c ON cmd.id_curso = c.id JOIN usuarios u ON cmd.id_docente = u.id WHERE cmd.id = $id")->fetch();
        
        $stmt = $pdo->prepare("DELETE FROM curso_materia_docente WHERE id = ?");
        $stmt->execute([$id]);

        if ($info) {
            $detalle_log = "Removió al Profesor " . $info['apellido'] . " de la materia '" . $info['mat'] . "' en " . $info['cur'] . ".";
            $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Baja de Cátedra', ?)");
            $stmtLog->execute([$id_admin, $detalle_log]);
        }
        header("Location: ../index.php?p=admin_cursos&msg=Cátedra removida");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error al eliminar.");
    }
    exit;
}

//  Eliminar un curso completo
if ($accion === 'eliminar_curso') {
    $id_curso = intval($_GET['id']);

    try {
        $c = $pdo->query("SELECT nombre FROM cursos WHERE id = $id_curso")->fetch();
        
        $stmt = $pdo->prepare("DELETE FROM cursos WHERE id = ?");
        $stmt->execute([$id_curso]);

        if ($c) {
            $detalle_log = "Eliminó el curso completo: '" . $c['nombre'] . "' junto con sus asignaciones de cátedras.";
            $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Baja de Curso', ?)");
            $stmtLog->execute([$id_admin, $detalle_log]);
        }

        header("Location: ../index.php?p=admin_cursos&msg=Curso eliminado correctamente");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error: No se pudo eliminar el curso.");
    }
    exit;
}

//  Eliminar una materia del plan de estudios
if ($accion === 'eliminar_materia') {
    $id_materia = intval($_GET['id']);

    try {
        $m = $pdo->query("SELECT nombre FROM materias WHERE id = $id_materia")->fetch();

        $stmt = $pdo->prepare("DELETE FROM materias WHERE id = ?");
        $stmt->execute([$id_materia]);

        if ($m) {
            $detalle_log = "Eliminó la materia '" . $m['nombre'] . "' del plan de estudios institucional.";
            $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Baja de Materia', ?)");
            $stmtLog->execute([$id_admin, $detalle_log]);
        }

        header("Location: ../index.php?p=admin_cursos&msg=Materia eliminada correctamente");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error: No se pudo eliminar la materia.");
    }
    exit;
}

//  MATRÍCULA: Inscribir un alumno a un curso (evitando duplicados)
if ($accion === 'asignar_alumno' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_alumno = intval($_POST['id_alumno']);
    $id_curso = intval($_POST['id_curso']);

    $stmt = $pdo->prepare("SELECT id FROM inscripciones WHERE id_alumno = ? AND id_curso = ?");
    $stmt->execute([$id_alumno, $id_curso]);
    
    if ($stmt->rowCount() == 0) {
        $insert = $pdo->prepare("INSERT INTO inscripciones (id_alumno, id_curso) VALUES (?, ?)");
        $insert->execute([$id_alumno, $id_curso]);
        header("Location: ../index.php?p=admin_cursos&msg=Alumno asignado correctamente");
    } else {
        header("Location: ../index.php?p=admin_cursos&msg=Error: El alumno ya está en este curso");
    }
    exit;
}

//  Dar de baja la inscripción de un alumno
if ($accion === 'eliminar_inscripcion') {
    $id_inscripcion = intval($_GET['id']);
    
    try {
        $del = $pdo->prepare("DELETE FROM inscripciones WHERE id = ?");
        $del->execute([$id_inscripcion]);
        header("Location: ../index.php?p=admin_cursos&msg=Alumno dado de baja del curso");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_cursos&msg=Error al dar de baja al alumno.");
    }
    exit;
}