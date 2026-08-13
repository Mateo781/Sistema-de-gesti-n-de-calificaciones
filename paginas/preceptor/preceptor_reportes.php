<?php
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) {
    die("Acceso denegado.");
}
require_once "php/db.php";

$id_curso = $_GET['id_curso'] ?? null;
$cursos = $pdo->query("SELECT id, nombre, division FROM cursos")->fetchAll(PDO::FETCH_ASSOC);
$reporte = [];

if ($id_curso) {
    // Obtener resumen de estados por materia
    $query = "SELECT m.nombre AS materia, em.nombre AS estado, COUNT(t.id) AS cantidad
              FROM curso_materia_docente cmd
              JOIN materias m ON cmd.id_materia = m.id
              LEFT JOIN trayectorias t ON cmd.id = t.id_curso_materia
              LEFT JOIN estados_materia em ON t.id_estado_materia = em.id
              WHERE cmd.id_curso = ?
              GROUP BY m.id, em.id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_curso]);
    $reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container" style="padding: 20px;">
    <h2>Reporte de Estados por Curso</h2>

    <!-- Filtro de curso -->
    <form method="GET" action="index.php" style="margin-bottom: 20px;">
        <input type="hidden" name="p" value="preceptor_reportes">
        <select name="id_curso" onchange="this.form.submit()" style="padding: 8px;">
            <option value="">Seleccione un curso para generar reporte...</option>
            <?php foreach ($cursos as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $id_curso == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Tabla de reporte -->
    <?php if ($id_curso && !empty($reporte)): ?>
        <table style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #ddd;">
            <tr style="background: #1a2d5a; color: white;">
                <th style="padding: 10px; text-align: left;">Materia</th>
                <th style="padding: 10px; text-align: left;">Estado</th>
                <th style="padding: 10px; text-align: left;">Cant. Alumnos</th>
            </tr>
            <?php foreach ($reporte as $row): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px;"><?= htmlspecialchars($row['materia']) ?></td>
                <td style="padding: 10px;"><?= htmlspecialchars($row['estado'] ?? 'Sin Carga') ?></td>
                <td style="padding: 10px; font-weight: bold;"><?= $row['cantidad'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($id_curso): ?>
        <p>No hay datos cargados para este curso.</p>
    <?php endif; ?>
</div>