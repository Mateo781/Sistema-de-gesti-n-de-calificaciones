<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de seguridad para el rol de Jefe de Departamento
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 6) { 
    die("Acceso denegado."); 
}

$nombre = $_SESSION['usuario_nombre'] ?? 'Jefe';
$apellido = $_SESSION['usuario_apellido'] ?? 'Departamento';
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        <div class="form-header">
            <h2 class="form-title">Panel de Jefatura de Departamento</h2>
            <p class="form-subtitle">Hola, <strong><?= htmlspecialchars($nombre . " " . $apellido) ?></strong>. Gestioná los criterios pedagógicos y pautas institucionales de tu área.</p>
        </div>

        <div class="field-group">
            <h3>Acciones disponibles:</h3>
            <ul>
                <li>Utilizá el menú lateral para proponer nuevos criterios pedagógicos basados en las pautas RITE.</li>
                <li>Monitoreá el historial de estados para verificar si Dirección aprobó tus propuestas o solicitó correcciones.</li>
                <li>Mantené comunicación activa con tus docentes para unificar los criterios aprobados.</li>
            </ul>
        </div>
    </div>
</div>