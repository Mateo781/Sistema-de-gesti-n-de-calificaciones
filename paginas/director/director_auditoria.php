<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/db.php"; 

// Solo acceso al Director
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 7) { 
    die("Acceso denegado."); 
}

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_accion = isset($_GET['filtro_accion']) ? trim($_GET['filtro_accion']) : '';

// Consulta con JOIN para vincular el nombre del admin al log
$sql = "SELECT h.*, u.nombre as admin_nombre, u.apellido as admin_apellido 
        FROM historial_cambios h 
        INNER JOIN usuarios u ON h.id_admin = u.id WHERE 1=1";

$params = [];

if ($buscar !== '') {
    $sql .= " AND (h.detalle LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ?)";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

if ($filtro_accion !== '') {
    $sql .= " AND h.accion = ?";
    $params[] = $filtro_accion;
}

$sql .= " ORDER BY h.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Acciones únicas para el selector
$tipos_acciones = $pdo->query("SELECT DISTINCT accion FROM historial_cambios ORDER BY accion ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        <div class="form-header">
            <h2 class="form-title">Auditoría de Cambios</h2>
            <p class="form-subtitle">Supervisión y control de modificaciones efectuadas por administración en el sistema.</p>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="filter-card">
            <form method="GET" action="index.php" class="filter-form">
                <input type="hidden" name="p" value="director_auditoria">
                
                <div class="filter-group filter-group-grow">
                    <label for="buscar" class="form-label">Buscar:</label>
                    <input type="text" id="buscar" name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Detalle o administrador..." class="form-input">
                </div>

                <div class="filter-group">
                    <label for="filtro_accion" class="form-label">Categoría:</label>
                    <select id="filtro_accion" name="filtro_accion" class="form-select">
                        <option value="">-- Todas --</option>
                        <?php foreach($tipos_acciones as $act): ?>
                            <option value="<?= htmlspecialchars($act) ?>" <?= $filtro_accion === $act ? 'selected' : '' ?>><?= htmlspecialchars($act) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <?php if($buscar !== '' || $filtro_accion !== ''): ?>
                        <a href="index.php?p=director_auditoria" class="btn btn-secondary">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabla de Auditoría -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Administrador</th>
                        <th>Categoría</th>
                        <th>Operación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($logs) > 0): ?>
                        <?php foreach ($logs as $log): 
                            $fecha_formateada = date("d/m/Y H:i", strtotime($log['fecha']));
                            
                            // Determinación de clases según tipo de acción
                            $badge_class = 'badge-default';
                            if (preg_match('/baja|eliminar/i', $log['accion'])) {
                                $badge_class = 'badge-danger';
                            } elseif (strpos($log['accion'], 'cierre') !== false) {
                                $badge_class = 'badge-warning';
                            } elseif (preg_match('/plan|calendario/i', $log['accion'])) {
                                $badge_class = 'badge-success';
                            }
                        ?>
                            <tr>
                                <td class="table-date"><?= $fecha_formateada ?> hs</td>
                                <td class="table-admin"><?= htmlspecialchars($log['admin_apellido'] . ", " . $log['admin_nombre']) ?></td>
                                <td>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= htmlspecialchars($log['accion']) ?>
                                    </span>
                                </td>
                                <td class="table-detail"><?= htmlspecialchars($log['detalle']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="table-empty">No hay registros encontrados con los filtros seleccionados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>