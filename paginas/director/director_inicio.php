<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Seguridad: solo acceso al Director
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 7) { 
    die("Acceso denegado."); 
}

$nombre = $_SESSION['usuario_nombre'] ?? 'Director';
$apellido = $_SESSION['usuario_apellido'] ?? '';
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel" style="max-width: 800px;">
        <div class="form-header">
            <h2 class="form-title">Panel de Dirección</h2>
            <p class="form-subtitle">Hola, <strong><?= htmlspecialchars($nombre . " " . $apellido) ?></strong>. Supervisá y autorizá los procesos académicos de la E.E.S.T. N° 1.</p>
        </div>

        <div class="field-group">
            <h3>Tareas disponibles:</h3>
            <ul>
                <li>Usá el menú lateral para gestionar los criterios y auditorías.</li>
                <li>Monitoreá las acciones realizadas por el personal administrativo.</li>
            </ul>
        </div>
    </div>
</div>