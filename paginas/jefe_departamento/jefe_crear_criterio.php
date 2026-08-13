<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificación de acceso para Jefe de Departamento
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 6) { 
    die("Acceso denegado."); 
}
?>

<div class="auth-panel">
    <div class="auth-form-wrap">
        <div class="form-header">
            <h2 class="form-title">Crear nueva propuesta de criterios</h2>
            <p class="form-subtitle">Redactá los criterios pedagógicos del área o materia y envialos a Dirección para su validación.</p>
        </div>

        <form action="php/procesar_criterios.php?accion=enviar_propuesta" method="POST">
            <div class="field-group">
                
                <!-- NUEVO CAMPO: Selección de Materia -->
                <div class="field">
                    <label for="id_materia">Materia / Área</label>
                    <div class="input-wrap">
                        <select class="field-input" id="id_materia" name="id_materia" required style="padding: 12px; width: 100%;">
                            <option value="">Seleccioná la materia...</option>
                            <?php
                            // Requiere que tengas tu conexión $pdo disponible en este archivo
                            require_once 'php/db.php'; // Ajustá la ruta si es necesario
                            $stmt_mat = $pdo->query("SELECT id, nombre FROM materias WHERE activo = 1 ORDER BY nombre ASC");
                            while ($materia = $stmt_mat->fetch()) {
                                echo "<option value='{$materia['id']}'>{$materia['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- FIN NUEVO CAMPO -->

                <div class="field">
                    <label for="titulo">Título de la propuesta</label>
                    <div class="input-wrap">
                        <input class="field-input" type="text" id="titulo" name="titulo" placeholder="Ej: Criterios de Evaluación - Matemática 1° Año" required>
                    </div>
                </div>

                <div class="field">
                    <label for="descripcion">Descripción detallada</label>
                    <div class="input-wrap">
                        <textarea class="field-input" id="descripcion" name="descripcion" rows="6" placeholder="Detallá los criterios pedagógicos institucionales aquí..." required style="height: auto; padding: 12px;"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                Enviar a Dirección
            </button>
        </form>
    </div>
</div>