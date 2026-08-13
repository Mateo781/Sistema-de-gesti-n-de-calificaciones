<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificación de acceso
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 1) { 
    die("Acceso denegado."); 
}

$nombre_admin = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Administrador';
$rol_texto = "Administrador General";
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        
        <!-- Header Principal idéntico a las demás pantallas -->
        <div class="form-header dashboard-hero-header">
            <div class="hero-intro">
                <h2 class="form-title">Bienvenido, <?= htmlspecialchars($nombre_admin) ?></h2>
                <p class="form-subtitle">Este es el centro neurálgico del Sistema de Gestión de Calificaciones. Desde aquí puedes supervisar la estructura institucional, gestionar accesos y auditar el flujo académico.</p>
            </div>
        </div>

        <!-- Tarjetas de Acceso Rápido organizadas en grilla -->
        <div class="dashboard-quick-grid">
            <div class="quick-card">
                <div class="quick-icon">👥</div>
                <div class="quick-info">
                    <h3 class="quick-title">Gestión de Usuarios</h3>
                    <p class="quick-desc">Altas, bajas, control de roles institucionales y blanqueo de credenciales por DNI.</p>
                    <a href="index.php?p=admin_usuarios" class="quick-link">Administrar usuarios →</a>
                </div>
            </div>

            <div class="quick-card">
                <div class="quick-icon">📚</div>
                <div class="quick-info">
                    <h3 class="quick-title">Estructura y Cursos</h3>
                    <p class="quick-desc">Configuración de divisiones, altas de materias, asignación de cátedras y matrículas.</p>
                    <a href="index.php?p=admin_cursos" class="quick-link">Ver cursos y cátedras →</a>
                </div>
            </div>
        </div>

    </div>
</div>