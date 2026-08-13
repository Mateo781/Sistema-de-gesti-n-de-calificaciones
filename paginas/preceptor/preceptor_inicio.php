<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificación de acceso
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) { 
    die("Acceso denegado."); 
}

require_once "php/db.php";

$nombre = $_SESSION['usuario_nombre'] ?? 'Preceptor/a';
$apellido = $_SESSION['usuario_apellido'] ?? '';

// --- ESTADÍSTICAS ---
// Total de alumnos
$stmt_alum = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE id_rol = 3");
$total_alumnos = $stmt_alum->fetchColumn() ?: 0;

// Criterios aprobados
$stmt_rite = $pdo->query("SELECT COUNT(*) FROM criterios_evaluacion WHERE estado = 'aprobado'");
$criterios_aprobados = $stmt_rite->fetchColumn() ?: 0;
?>

<div class="main-content" style="padding: 30px 20px; font-family: sans-serif; max-width: 1000px; margin: 0 auto;">
    
    <!-- Bienvenida -->
    <div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #3b6fd4; margin-bottom: 30px;">
        <h2 style="color: #1a2d5a; margin-top: 0;">¡Bienvenido al Panel de Preceptoría!</h2>
        <p style="font-size: 1.1em; color: #555;">
            Hola, <strong><?= htmlspecialchars($nombre . " " . $apellido) ?></strong>. Desde aquí podrás realizar el seguimiento académico, supervisar el RITE y gestionar informes.
        </p>
    </div>

    <!-- Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <!-- Alumnos -->
        <div style="background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border-left: 4px solid #3b6fd4;">
            <span style="color: #888; font-size: 0.9em; font-weight: bold; text-transform: uppercase;">Alumnos a Cargo</span>
            <h3 style="margin: 10px 0 0 0; font-size: 2em; color: #333;"><?= $total_alumnos ?></h3>
        </div>

        <!-- Criterios -->
        <div style="background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border-left: 4px solid #28a745;">
            <span style="color: #888; font-size: 0.9em; font-weight: bold; text-transform: uppercase;">Criterios Aprobados</span>
            <h3 style="margin: 10px 0 0 0; font-size: 2em; color: #28a745;"><?= $criterios_aprobados ?></h3>
        </div>

        <!-- Periodo actual -->
        <div style="background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border-left: 4px solid #ffc107;">
            <span style="color: #888; font-size: 0.9em; font-weight: bold; text-transform: uppercase;">Periodo Actual</span>
            <h3 style="margin: 10px 0 0 0; font-size: 1.5em; color: #333; padding-top: 5px;">1° Informe RITE</h3>
        </div>
    </div>

    <!-- Tareas -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 6px;">
        <h4 style="color: #1a2d5a; margin-top: 0; margin-bottom: 10px;">Tareas sugeridas del día:</h4>
        <ul style="margin: 0; padding-left: 20px; color: #4a5568; line-height: 1.8;">
            <li>Revisar <strong>Situación Académica</strong> para ver alumnos con dificultades.</li>
            <li>Monitorear el <strong>Control RITE</strong> para ver el avance de notas.</li>
            <li>Emitir alertas o descargar boletines en <strong>Reportes</strong>.</li>
        </ul>
    </div>

</div>