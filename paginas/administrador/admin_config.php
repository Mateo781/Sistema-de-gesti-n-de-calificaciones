<?php
require_once "php/db.php";

// Verificación de permisos de administrador
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 1) { die("Acceso denegado."); }

// Obtener ciclo lectivo activo
$ciclo_actual = $pdo->query("SELECT * FROM ciclos_lectivos WHERE activo = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Obtener periodos del ciclo actual
$periodos = [];
if ($ciclo_actual) {
    $stmt = $pdo->prepare("SELECT * FROM periodos WHERE id_ciclo = ? ORDER BY id ASC");
    $stmt->execute([$ciclo_actual['id']]);
    $periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        
        <!-- Header Principal unificado -->
        <div class="form-header dashboard-hero-header">
            <div class="hero-intro">
                <h2 class="form-title">Configuración del Calendario RITE</h2>
                <p class="form-subtitle">Administración de periodos de evaluación, cuatrimestres e intensificaciones académicas.</p>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): 
            $es_error = (strpos($_GET['msg'], 'Error') !== false);
            $class_msg = $es_error ? 'alert alert-danger' : 'alert alert-success';
        ?>
            <div class="<?= $class_msg ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <!-- Grilla Principal (Formulario y Listado de Periodos) -->
        <div class="users-layout" style="margin-bottom: 24px;">
            
            <!-- Formulario de creación de periodos -->
            <div class="card-section">
                <h3 class="section-title">Registrar Periodo</h3>
                <p class="form-hint" style="margin-bottom: 16px;">Agregá un nuevo bloque al RITE.</p>
                
                <?php if ($ciclo_actual): ?>
                    <form action="php/procesar_config_admin.php?accion=crear_periodo" method="POST" class="user-form">
                        <input type="hidden" name="id_ciclo" value="<?= $ciclo_actual['id'] ?>">
                        
                        <div class="field-group">
                            <label class="form-label">Nombre del Periodo:</label>
                            <input type="text" name="nombre_periodo" placeholder="Ej: 1er Cuatrimestre" required class="form-input">
                        </div>
                        
                        <div class="field-group">
                            <label class="form-label">Tipo de Instancia:</label>
                            <select name="tipo_periodo" class="form-select">
                                <option value="regular">Cursada Regular</option>
                                <option value="intensificacion">Período de Intensificación / Examen</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            Añadir al Calendario
                        </button>
                    </form>
                <?php else: ?>
                    <p class="config-error-text">No hay un ciclo lectivo activo.</p>
                <?php endif; ?>
            </div>

            <!-- Gestión de fechas y listado -->
            <div class="card-section" style="background: var(--card, #ffffff);">
                <h3 class="section-title">Control de Fechas Límites</h3>
                <p class="form-hint" style="margin-bottom: 16px;">Configurá los rangos para carga de notas.</p>

                <?php if (count($periodos) > 0): ?>
                    <form action="php/procesar_config_admin.php?accion=guardar_fechas" method="POST" class="user-form">
                        <div class="config-scroll-box">
                            
                            <?php foreach($periodos as $per): ?>
                                <div class="period-item-card">
                                    <div class="period-item-header">
                                        <span class="period-item-title">
                                            <?= htmlspecialchars($per['nombre']) ?>
                                            <span class="badge badge-default">
                                                <?= $per['tipo'] ?>
                                            </span>
                                        </span>
                                        <a href="php/procesar_config_admin.php?accion=eliminar_periodo&id=<?= $per['id'] ?>" 
                                           onclick="return confirm('¿Confirmar eliminación?')" 
                                           class="action-link action-danger">❌ Eliminar</a>
                                    </div>
                                    
                                    <div class="period-dates-grid">
                                        <div class="field-group">
                                            <label class="form-label">Inicio:</label>
                                            <input type="date" name="inicio[<?= $per['id'] ?>]" value="<?= $per['fecha_inicio'] ?>" required class="form-input">
                                        </div>
                                        <div class="field-group">
                                            <label class="form-label">Fin:</label>
                                            <input type="date" name="fin[<?= $per['id'] ?>]" value="<?= $per['fecha_fin'] ?>" required class="form-input">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 16px;">
                            Guardar Cambios
                        </button>
                    </form>
                <?php else: ?>
                    <div class="config-empty-box">
                        No hay periodos cargados.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cierre de ciclo anual -->
        <div class="config-danger-card">
            <h3 class="danger-title">Cierre del Ciclo Lectivo</h3>
            <p class="danger-desc">
                Finaliza el año, congela planillas y promueve a los alumnos al siguiente ciclo.
            </p>

            <?php if ($ciclo_actual): ?>
                <form action="php/procesar_config_admin.php?accion=cerrar_ciclo" method="POST" onsubmit="return confirm('ATENCIÓN: Esta acción es irreversible.')">
                    <input type="hidden" name="ciclo_actual_id" value="<?= $ciclo_actual['id'] ?>">
                    <button type="submit" class="config-btn-danger">
                        Archivar Año <?= $ciclo_actual['anio'] ?> e Iniciar Siguiente
                    </button>
                </form>
            <?php endif; ?>
        </div>

    </div>
</div>