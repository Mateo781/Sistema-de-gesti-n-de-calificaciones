<?php
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) {
    die("Acceso denegado.");
}
require_once "php/db.php";

$cursos = $pdo->query("SELECT id, nombre, division FROM cursos")->fetchAll(PDO::FETCH_ASSOC);
$id_curso = $_GET['id_curso'] ?? null;
$id_materia = $_GET['id_materia'] ?? null;
$alumnos_data = [];

if ($id_curso) {
    // Traer datos de alumnos, materias y sus estados
    $query = "SELECT u.id AS id_alumno, u.apellido, u.nombre, m.nombre AS materia, 
                     cmd.id AS id_cmd, 
                     COALESCE(em.nombre, 'Pendiente') AS estado, t.promedio_final
              FROM usuarios u
              JOIN inscripciones i ON u.id = i.id_alumno
              JOIN curso_materia_docente cmd ON i.id_curso = cmd.id_curso
              JOIN materias m ON cmd.id_materia = m.id
              LEFT JOIN trayectorias t ON u.id = t.id_alumno AND t.id_curso_materia = cmd.id
              LEFT JOIN estados_materia em ON t.id_estado_materia = em.id
              WHERE i.id_curso = ? AND u.id_rol = 3";
    
    $params = [$id_curso];
    if ($id_materia) { $query .= " AND m.id = ?"; $params[] = $id_materia; }
    $query .= " ORDER BY u.apellido, u.nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $alumnos_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container" style="padding: 20px;">
    <h2>Control de Valoraciones RITE</h2>
    
    <!-- Filtros de búsqueda -->
    <form method="GET" action="index.php" style="margin-bottom: 20px;">
        <input type="hidden" name="p" value="preceptor_control_rite">
        <select name="id_curso" onchange="this.form.submit()" style="padding: 8px;">
            <option value="">Seleccione un curso...</option>
            <?php foreach ($cursos as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $id_curso == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($id_curso): ?>
            <select name="id_materia" onchange="this.form.submit()" style="padding: 8px; margin-left: 10px;">
                <option value="">Todas las materias...</option>
                <?php 
                $stmt_m = $pdo->prepare("SELECT DISTINCT m.id, m.nombre FROM materias m JOIN curso_materia_docente cmd ON m.id = cmd.id_materia WHERE cmd.id_curso = ?");
                $stmt_m->execute([$id_curso]);
                foreach ($stmt_m as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $id_materia == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </form>

    <!-- Tabla de resultados -->
    <table style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #ddd;">
        <tr style="background: #1a2d5a; color: white;">
            <th>Alumno</th><th>Materia</th><th>Estado</th><th>Promedio</th>
        </tr>
        <?php foreach ($alumnos_data as $row): ?>
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px;"><?= htmlspecialchars($row['apellido'] . ', ' . $row['nombre']) ?></td>
            <td style="padding: 10px;"><?= htmlspecialchars($row['materia']) ?></td>
            <td style="padding: 10px;">
                <?php if ($row['estado'] == 'Pendiente'): ?>
                    <button onclick="this.nextElementSibling.style.display='inline-block'; this.style.display='none';" 
                            style="color: #d9534f; cursor:pointer; background:none; border:none; font-weight:bold;">
                        + Cargar Nota
                    </button>
                <?php else: ?>
                    <?php 
                        // Colores según el resultado
                        $color = ($row['estado'] == 'Aprobada') ? '#d4edda' : '#f8d7da';
                        $texto = ($row['estado'] == 'Aprobada') ? '#155724' : '#721c24';
                    ?>
                    <span style="background: <?= $color ?>; color: <?= $texto ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">
                        <?= htmlspecialchars($row['estado']) ?>
                    </span>
                <?php endif; ?>
            </td>
            <td style="padding: 10px;"><?= $row['promedio_final'] ?? '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>