<?php
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) {
    die("Acceso denegado.");
}
require_once "php/db.php";

$busqueda = $_GET['busqueda'] ?? '';
$id_alumno_seleccionado = $_GET['id_alumno'] ?? null;
$alumnos_busqueda = [];
$alumno_info = null;
$trayectorias = [];

// 1. Buscar alumnos
if (!empty($busqueda)) {
    $stmt = $pdo->prepare("SELECT id, nombre, apellido FROM usuarios WHERE id_rol = 3 AND (nombre LIKE ? OR apellido LIKE ?)");
    $stmt->execute(["%$busqueda%", "%$busqueda%"]);
    $alumnos_busqueda = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 2. Cargar trayectoria del alumno seleccionado
if ($id_alumno_seleccionado) {
    $stmt = $pdo->prepare("SELECT nombre, apellido FROM usuarios WHERE id = ?");
    $stmt->execute([$id_alumno_seleccionado]);
    $alumno_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT m.nombre AS materia, em.nombre AS estado, t.promedio_final
              FROM trayectorias t
              JOIN curso_materia_docente cmd ON t.id_curso_materia = cmd.id
              JOIN materias m ON cmd.id_materia = m.id
              LEFT JOIN estados_materia em ON t.id_estado_materia = em.id
              WHERE t.id_alumno = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_alumno_seleccionado]);
    $trayectorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container" style="padding: 20px;">
    <h2>Trayectoria Académica del Alumno</h2>

    <!-- Buscador -->
    <form method="GET" action="index.php" style="margin-bottom: 20px;">
        <input type="hidden" name="p" value="preceptor_trayectoria">
        <input type="text" name="busqueda" placeholder="Buscar por nombre o apellido..." value="<?= htmlspecialchars($busqueda) ?>" style="padding: 8px; width: 300px;">
        <button type="submit" style="padding: 8px 15px;">Buscar</button>
    </form>

    <!-- Resultados -->
    <?php if (!empty($alumnos_busqueda)): ?>
        <div style="margin-bottom: 20px;">
            <strong>Resultados:</strong><br>
            <?php foreach ($alumnos_busqueda as $a): ?>
                <a href="index.php?p=preceptor_trayectoria&id_alumno=<?= $a['id'] ?>&busqueda=<?= urlencode($busqueda) ?>" 
                   style="display: block; padding: 5px; color: #1a2d5a; text-decoration: none;">
                   <?= htmlspecialchars($a['apellido'] . ', ' . $a['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Tabla -->
    <?php if ($alumno_info): ?>
        <hr>
        <h3>Alumno: <?= htmlspecialchars($alumno_info['apellido'] . ', ' . $alumno_info['nombre']) ?></h3>
        <table style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #ddd;">
            <tr style="background: #1a2d5a; color: white;">
                <th style="padding: 10px; text-align: left;">Materia</th>
                <th style="padding: 10px; text-align: left;">Estado</th>
                <th style="padding: 10px; text-align: left;">Promedio</th>
            </tr>
            <?php if (empty($trayectorias)): ?>
                <tr><td colspan="3" style="padding: 10px;">No hay registros para este alumno.</td></tr>
            <?php else: ?>
                <?php foreach ($trayectorias as $t): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;"><?= htmlspecialchars($t['materia']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($t['estado']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($t['promedio_final'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</div>