<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/db.php";

// Verificación de acceso
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 1) { 
    die("Acceso denegado."); 
}

// Consultas iniciales de configuración
$cursos = $pdo->query("SELECT id, nombre, anio_escolar, division FROM cursos ORDER BY anio_escolar ASC, division ASC")->fetchAll(PDO::FETCH_ASSOC);
$materias = $pdo->query("SELECT id, nombre, descripcion FROM materias WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$profesores = $pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE id_rol = 2 AND activo = 1 ORDER BY apellido ASC")->fetchAll(PDO::FETCH_ASSOC);

// Consulta de distribución de cátedras
$sqlAsignaciones = "SELECT cmd.id, c.nombre as curso_nombre, m.nombre as materia, u.nombre as prof_n, u.apellido as prof_a 
                    FROM curso_materia_docente cmd
                    JOIN cursos c ON cmd.id_curso = c.id
                    JOIN materias m ON cmd.id_materia = m.id
                    JOIN usuarios u ON cmd.id_docente = u.id
                    ORDER BY c.nombre ASC, m.nombre ASC";
$asignaciones = $pdo->query($sqlAsignaciones)->fetchAll(PDO::FETCH_ASSOC);

// Obtención de alumnos y lista de inscripciones
$alumnos_disponibles = $pdo->query("SELECT id, nombre, apellido, dni FROM usuarios WHERE id_rol = 3 ORDER BY apellido ASC")->fetchAll(PDO::FETCH_ASSOC);

$sqlInscripciones = "SELECT i.id, c.nombre as curso_nombre, u.nombre as alu_n, u.apellido as alu_a 
                     FROM inscripciones i
                     JOIN cursos c ON i.id_curso = c.id
                     JOIN usuarios u ON i.id_alumno = u.id
                     ORDER BY c.nombre ASC, u.apellido ASC";
$inscripciones = $pdo->query($sqlInscripciones)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        <div class="form-header">
            <h2 class="form-title">Estructura Académica y Cursos</h2>
            <p class="form-subtitle">Configuración de divisiones, materias, asignación de equipos docentes e inscripciones.</p>
        </div>

        <?php if(isset($_GET['msg'])): 
            $es_error = (strpos($_GET['msg'], 'Error') !== false);
        ?>
            <div class="alert <?= $es_error ? 'alert-danger' : 'alert-success' ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <!-- Sección Superior: Grilla de 3 columnas (Cursos, Materias, Asignaciones) -->
        <div class="academic-grid-top">
            <!-- Gestión de Cursos -->
            <div class="card-section">
                <h3 class="section-title">Crear Curso / División</h3>
                <form action="php/procesar_cursos_admin.php?accion=crear_curso" method="POST" class="form-stack">
                    <div class="field-group">
                        <label for="nombre_curso" class="form-label">Nombre Completo</label>
                        <input type="text" id="nombre_curso" name="nombre" placeholder="Ej: 7° 1°" required class="form-input">
                    </div>
                    
                    <div class="field-group">
                        <label for="anio_escolar" class="form-label">Año Escolar</label>
                        <input type="number" id="anio_escolar" name="anio_escolar" min="1" max="7" required class="form-input">
                    </div>
                    
                    <div class="field-group">
                        <label for="division" class="form-label">División</label>
                        <input type="text" id="division" name="division" placeholder="Ej: 1" required class="form-input">
                    </div>
                    
                    <input type="hidden" name="id_ciclo" value="1">
                    <button type="submit" class="btn btn-primary">Guardar Curso</button>
                </form>

                <div class="sublist-container">
                    <h4 class="sublist-title">Cursos Registrados:</h4>
                    <ul class="sublist">
                        <?php foreach($cursos as $c): ?>
                            <li class="sublist-item">
                                <span><?= htmlspecialchars($c['nombre']) ?></span>
                                <a href="php/procesar_cursos_admin.php?accion=eliminar_curso&id=<?= $c['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este curso?')" class="action-delete" title="Borrar Curso">❌ Borrar</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Gestión de Materias -->
            <div class="card-section">
                <h3 class="section-title">Registrar Materia</h3>
                <form action="php/procesar_cursos_admin.php?accion=crear_materia" method="POST" class="form-stack">
                    <div class="field-group">
                        <label for="nombre_materia" class="form-label">Nombre</label>
                        <input type="text" id="nombre_materia" name="nombre_materia" placeholder="Ej: Matemática" required class="form-input">
                    </div>
                    
                    <div class="field-group">
                        <label for="descripcion_materia" class="form-label">Descripción</label>
                        <textarea id="descripcion_materia" name="descripcion_materia" rows="3" placeholder="Breve detalle..." class="form-textarea" style="resize: none;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Guardar Materia</button>
                </form>

                <div class="sublist-container">
                    <h4 class="sublist-title">Materias Activas:</h4>
                    <ul class="sublist">
                        <?php foreach($materias as $m): ?>
                            <li class="sublist-item">
                                <span title="<?= htmlspecialchars($m['descripcion'] ?? '') ?>"><?= htmlspecialchars($m['nombre']) ?></span>
                                <a href="php/procesar_cursos_admin.php?accion=eliminar_materia&id=<?= $m['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta materia?')" class="action-delete" title="Borrar Materia">❌ Borrar</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Asignación de docentes -->
            <div class="card-section">
                <h3 class="section-title">Asignar Profesor</h3>
                <form action="php/procesar_cursos_admin.php?accion=asignar_profesor" method="POST" class="form-stack">
                    <div class="field-group">
                        <label for="asig_curso" class="form-label">Curso</label>
                        <select id="asig_curso" name="id_curso" required class="form-select">
                            <?php foreach($cursos as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="asig_materia" class="form-label">Materia</label>
                        <select id="asig_materia" name="id_materia" required class="form-select">
                            <?php foreach($materias as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="asig_profesor" class="form-label">Profesor</label>
                        <select id="asig_profesor" name="id_profesor" required class="form-select">
                            <?php foreach($profesores as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['apellido'] . ", " . $p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-info" style="margin-top: 4px;">Vincular Cátedra</button>
                </form>
            </div>
        </div>

        <!-- Tabla de Distribución de Cátedras -->
        <div class="card-section" style="margin-top: 24px; padding: 24px;">
            <h3 class="section-title" style="margin-bottom: 16px;">Distribución de Cátedras</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Materia</th>
                            <th>Profesor</th>
                            <th style="text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($asignaciones)): ?>
                            <tr><td colspan="4" class="table-empty">No hay configuraciones de cátedras registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach($asignaciones as $asig): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($asig['curso_nombre']) ?></strong></td>
                                    <td><?= htmlspecialchars($asig['materia']) ?></td>
                                    <td><?= htmlspecialchars($asig['prof_a'] . ", " . $asig['prof_n']) ?></td>
                                    <td style="text-align: center;">
                                        <a href="php/procesar_cursos_admin.php?accion=eliminar_asignacion&id=<?= $asig['id'] ?>" onclick="return confirm('¿Desvincular esta cátedra?')" class="action-link action-danger">Quitar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inscripción de alumnos -->
        <div class="card-section" style="margin-top: 24px; padding: 24px;">
            <h3 class="section-title" style="margin-bottom: 16px;">Inscribir Alumnos</h3>
            <form action="php/procesar_cursos_admin.php?accion=asignar_alumno" method="POST" class="enroll-form">
                <div class="field-group" style="flex: 1;">
                    <label for="enr_alumno" class="form-label">Alumno</label>
                    <select id="enr_alumno" name="id_alumno" required class="form-select">
                        <?php foreach($alumnos_disponibles as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['apellido'] . ", " . $a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-group" style="flex: 1;">
                    <label for="enr_curso" class="form-label">Curso</label>
                    <select id="enr_curso" name="id_curso" required class="form-select">
                        <?php foreach($cursos as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" style="align-self: flex-end;">Inscribir Alumno</button>
            </form>
        </div>

    </div>
</div>