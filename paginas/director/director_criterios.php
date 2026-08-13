<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/db.php"; 

// Solo acceso al Director
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 7) { 
    die("Acceso denegado."); 
}

// Obtener propuestas pendientes de Jefaturas
$stmt = $pdo->query("SELECT c.*, u.nombre, u.apellido FROM criterios_evaluacion c JOIN usuarios u ON c.id_usuario_jefe = u.id WHERE c.estado = 'pendiente' ORDER BY c.fecha_creacion DESC");
$pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        <div class="form-header">
            <h2 class="form-title">Autorización de Criterios</h2>
            <p class="form-subtitle">Revisión y validación de criterios pedagógicos enviados por Jefaturas.</p>
        </div>

        <?php if(empty($pendientes)): ?>
            <div class="alert alert-info">
                No hay propuestas pendientes de revisión en este momento.
            </div>
        <?php else: ?>
            <div class="proposals-list">
                <?php foreach($pendientes as $p): ?>
                    <div class="proposal-card">
                        <div class="proposal-info">
                            <h4 class="proposal-title"><?= htmlspecialchars($p['titulo']) ?></h4>
                            <p class="proposal-meta"><strong>De:</strong> <?= htmlspecialchars($p['nombre'] . " " . $p['apellido']) ?> &bull; <span class="proposal-date"><?= date('d/m/Y H:i', strtotime($p['fecha_creacion'])) ?></span></p>
                            
                            <div class="proposal-description">
                                <?= nl2br(htmlspecialchars($p['descripcion'])) ?>
                            </div>
                        </div>

                        <!-- Formulario de gestión -->
                        <form action="" method="POST" id="form-<?= $p['id'] ?>" class="proposal-form">
                            <div class="field-group">
                                <label for="observaciones-<?= $p['id'] ?>" class="form-label">Observaciones:</label>
                                <textarea id="observaciones-<?= $p['id'] ?>" name="observaciones" class="form-textarea" rows="3" placeholder="Ingrese las observaciones o motivos..." required></textarea>
                            </div>
                            
                            <div class="proposal-actions">
                                <button type="button" class="btn btn-success" onclick="enviarAccion(<?= $p['id'] ?>, 'aprobar')">Aprobar</button>
                                <button type="button" class="btn btn-danger" onclick="enviarAccion(<?= $p['id'] ?>, 'rechazar')">Rechazar / Observar</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function enviarAccion(id, accion) {
    const form = document.getElementById('form-' + id);
    const textarea = form.querySelector('textarea');
    
    // Validación básica antes de enviar
    if (!textarea.value.trim()) {
        alert('Por favor, ingrese observaciones antes de aprobar o rechazar la propuesta.');
        textarea.focus();
        return;
    }

    form.action = 'php/procesar_criterios.php?id=' + id + '&accion=' + accion;
    form.submit();
}
</script>