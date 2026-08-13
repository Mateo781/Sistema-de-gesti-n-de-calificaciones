<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/db.php"; 

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 6) { 
    die("Acceso denegado."); 
}

$stmt = $pdo->prepare("SELECT * FROM criterios_evaluacion WHERE id_usuario_jefe = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$mis_criterios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        <div class="form-header">
            <h2 class="form-title">Historial de propuestas</h2>
            <p class="form-subtitle">Seguimiento en tiempo real de los criterios pedagógicos enviados a Dirección.</p>
        </div>

        <?php if(empty($mis_criterios)): ?>
            <div class="alert alert-info">No se encontraron propuestas enviadas hasta el momento.</div>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Propuesta</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mis_criterios as $crit): ?>
                            <tr>
                                <td>
                                    <div class="table-title"><?= htmlspecialchars($crit['titulo']) ?></div>
                                    <div class="table-subtitle"><?= htmlspecialchars(substr($crit['descripcion'], 0, 100)) ?>...</div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($crit['fecha_creacion'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $crit['estado'] ?>">
                                        <?= ucfirst($crit['estado']) ?>
                                    </span>
                                </td>
                                <td><?= !empty($crit['observaciones_director']) ? htmlspecialchars($crit['observaciones_director']) : '<span class="text-muted">Sin observaciones</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>