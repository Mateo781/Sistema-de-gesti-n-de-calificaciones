<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificación de acceso
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 5) { 
    die("Acceso denegado."); 
}

require_once "php/db.php";

// Obtener lista de cursos
$stmt_cursos = $pdo->query("SELECT id, nombre FROM cursos ORDER BY nombre ASC");
$cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);

// Manejo de filtro
$curso_seleccionado = $_GET['id_curso'] ?? null;
$alumnos = [];

// Obtener alumnos si hay un curso elegido
if ($curso_seleccionado) {
    $stmt_alumnos = $pdo->prepare("
        SELECT u.id, u.nombre, u.apellido, u.dni 
        FROM usuarios u
        JOIN inscripciones i ON u.id = i.id_alumno
        WHERE u.id_rol = 3 AND i.id_curso = ? 
        ORDER BY u.apellido, u.nombre ASC
    ");
    $stmt_alumnos->execute([$curso_seleccionado]);
    $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="main-content" style="padding: 20px; font-family: sans-serif;">
    <h2>Situación Académica y Observaciones</h2>
    <p style="color: #666;">Seleccioná un curso para ver los estudiantes y registrar observaciones.</p>
    
    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <!-- Selector de cursos -->
    <div style="background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <form method="GET" action="index.php" style="display: flex; gap: 15px; align-items: center;">
            <input type="hidden" name="p" value="preceptor_alumnos">
            
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-weight: bold; color: #333;">Seleccionar Curso / División:</label>
                <select name="id_curso" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; min-width: 250px;">
                    <option value="">-- Elegir un curso --</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($curso_seleccionado == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Resultados -->
    <?php if ($curso_seleccionado): ?>
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px; align-items: start;">
        
        <!-- Listado de alumnos -->
        <div style="background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="background: #1a2d5a; color: white; padding: 12px 15px; font-weight: bold;">
                Estudiantes Matriculados (<?= count($alumnos) ?>)
            </div>
            <?php if (empty($alumnos)): ?>
                <p style="padding: 15px; color: #777; margin: 0;">No hay alumnos registrados.</p>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($alumnos as $al): ?>
                        <li style="border-bottom: 1px solid #eee;">
                            <button type="button" onclick="cargarFichaEstudiante(<?= $al['id'] ?>)" style="width: 100%; text-align: left; padding: 12px 15px; background: none; border: none; cursor: pointer; display: flex; flex-direction: column; gap: 2px; transition: background 0.2s;">
                                <strong style="color: #3b6fd4;"><?= htmlspecialchars($al['apellido'] . ", " . $al['nombre']) ?></strong>
                                <span style="font-size: 0.85em; color: #666;">DNI: <?= htmlspecialchars($al['dni']) ?></span>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Ficha del estudiante -->
        <div id="contenedor-ficha" style="background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e0e0e0; min-height: 200px; padding: 20px; display: flex; align-items: center; justify-content: center; color: #888;">
            <div style="text-align: center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 10px; color: #aaa;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" /></svg>
                <p style="margin: 0;">Selecciona un estudiante para ver su información.</p>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div style="background: #f0f4f9; color: #1a2d5a; padding: 20px; border-radius: 6px; text-align: center; border: 1px dashed #bcccff;">
            Elige un curso para comenzar.
        </div>
    <?php endif; ?>
</div>

<script>
function cargarFichaEstudiante(idAlumno) {
    const contenedor = document.getElementById('contenedor-ficha');
    contenedor.innerHTML = '<p style="color: #666; text-align:center;">Cargando datos...</p>';

    // Pedir datos del estudiante
    fetch('./php/obtener_ficha_alumno.php?id=' + idAlumno)
        .then(response => response.text())
        .then(html => {
            contenedor.innerHTML = html;
        })
        .catch(err => {
            contenedor.innerHTML = '<p style="color: red; text-align:center;">Error al cargar datos.</p>';
            console.error(err);
        });
}
</script>