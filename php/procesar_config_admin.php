<?php
session_start();
require_once "db.php";

// Filtro estricto de seguridad: solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    die("No autorizado.");
}

$id_admin = $_SESSION['usuario_id'];
$accion = $_GET['accion'] ?? '';

//Crear un nuevo periodo en el calendario escolar
if ($accion === 'crear_periodo' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ciclo = intval($_POST['id_ciclo']);
    $nombre_periodo = trim($_POST['nombre_periodo']);
    $tipo_periodo = trim($_POST['tipo_periodo']); 

    try {
        // Insertamos usando la fecha de hoy por defecto para inicializar las ventanas
        $stmt = $pdo->prepare("INSERT INTO periodos (id_ciclo, nombre, fecha_inicio, fecha_fin, tipo) VALUES (?, ?, CURDATE(), CURDATE(), ?)");
        $stmt->execute([$id_ciclo, $nombre_periodo, $tipo_periodo]);

        // Guardamos rastro en la auditoría interna
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Calendario Escolar', 'Creó la instancia RITE: $nombre_periodo.')");
        $stmtLog->execute([$id_admin]);

        header("Location: ../index.php?p=admin_config&msg=Periodo agregado. Configure sus plazos de entrega.");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_config&msg=Error al registrar el periodo académico.");
    }
    exit;
}

//Actualizar las ventanas de fechas de entrega de notas
if ($accion === 'guardar_fechas' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $inicios = $_POST['inicio'] ?? [];
    $fins = $_POST['fin'] ?? [];

    try {
        $pdo->beginTransaction();
        
        // Barremos el array actualizando las fechas de cada periodo activo
        foreach ($inicios as $id_periodo => $fecha_inicio) {
            $fecha_fin = $fins[$id_periodo];
            $stmt = $pdo->prepare("UPDATE periodos SET fecha_inicio = ?, fecha_fin = ? WHERE id = ?");
            $stmt->execute([$fecha_inicio, $fecha_fin, $id_periodo]);
        }

        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Calendario Escolar', 'Actualizó las ventanas de fechas límite del RITE.')");
        $stmtLog->execute([$id_admin]);

        $pdo->commit();
        header("Location: ../index.php?p=admin_config&msg=Fechas límites guardadas exitosamente.");
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../index.php?p=admin_config&msg=Error al actualizar los rangos de fechas.");
    }
    exit;
}

// Eliminar un periodo del calendario escolar
if ($accion === 'eliminar_periodo') {
    $id_periodo = intval($_GET['id']);

    try {
        // Levantamos el nombre antes de borrar para que el log de auditoría quede claro
        $p = $pdo->query("SELECT nombre FROM periodos WHERE id = $id_periodo")->fetch();

        $stmt = $pdo->prepare("DELETE FROM periodos WHERE id = ?");
        $stmt->execute([$id_periodo]);

        if ($p) {
            $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Calendario Escolar', 'Eliminó la instancia de evaluación: " . $p['nombre'] . ".')");
            $stmtLog->execute([$id_admin]);
        }

        header("Location: ../index.php?p=admin_config&msg=Periodo eliminado correctamente.");
    } catch (PDOException $e) {
        // El motor de la BD frena el DELETE si el periodo ya está amarrado a notas cargadas
        header("Location: ../index.php?p=admin_config&msg=Error: No se puede borrar (el periodo ya cuenta con notas de alumnos asociadas).");
    }
    exit;
}

//Cierre de ciclo lectivo automatizado y promoción
if ($accion === 'cerrar_ciclo' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ciclo_actual = intval($_POST['ciclo_actual_id']);

    try {
        $pdo->beginTransaction();

        $ciclo_data = $pdo->query("SELECT anio FROM ciclos_lectivos WHERE id = $id_ciclo_actual")->fetch();
        if (!$ciclo_data) { die("Ciclo inválido."); }
        $anio_cerrado = intval($ciclo_data['anio']);
        $nuevo_anio = $anio_cerrado + 1;

        // Desactivamos el ciclo viejo
        $pdo->query("UPDATE ciclos_lectivos SET activo = 0 WHERE id = $id_ciclo_actual");

        // Creamos el ciclo entrante e insertamos sus 4 instancias por defecto
        $stmtNuevoCiclo = $pdo->prepare("INSERT INTO ciclos_lectivos (anio, descripcion, activo) VALUES (?, 'Ciclo Lectivo Regular', 1)");
        $stmtNuevoCiclo->execute([$nuevo_anio]);
        $id_proximo = $pdo->lastInsertId();
        
        $periodos_base = ["1er Cuatrimestre", "2do Cuatrimestre", "Intensificación Diciembre", "Intensificación Febrero"];
        foreach ($periodos_base as $p_nombre) {
            $stmtP = $pdo->prepare("INSERT INTO periodos (id_ciclo, nombre, fecha_inicio, fecha_fin, tipo) VALUES (?, ?, CURDATE(), CURDATE(), 'regular')");
            $stmtP->execute([$id_proximo, $p_nombre]);
        }

        // Promoción automática: subimos un año a todos los cursos activos (excepto los egresados de 7mo)
        $pdo->query("UPDATE cursos SET anio_escolar = anio_escolar + 1 WHERE anio_escolar < 7 AND id_ciclo = $id_ciclo_actual");
        $pdo->query("UPDATE cursos SET id_ciclo = $id_proximo");

        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'CIERRE ANUAL', 'Archivó el año $anio_cerrado y promovió los cursos al nuevo ciclo $nuevo_anio.')");
        $stmtLog->execute([$id_admin]);

        $pdo->commit();
        header("Location: ../index.php?p=admin_config&msg=Cierre anual ejecutado. Bienvenido al ciclo $nuevo_anio.");
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../index.php?p=admin_config&msg=Error Crítico: " . $e->getMessage());
    }
    exit;
}