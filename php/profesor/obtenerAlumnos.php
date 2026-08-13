<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../db.php';
require_once '../funciones_calificaciones.php';

$id_curso = isset($_GET['id_curso']) ? intval($_GET['id_curso']) : 0;
$id_curso_materia = isset($_GET['id_materia']) ? intval($_GET['id_materia']) : 0;

if ($id_curso <= 0 || $id_curso_materia <= 0) {
    echo '<div style="padding: 20px; text-align: center; color: #c62828;">Selección incompleta.</div>';
    exit;
}

$alumnos = obtenerAlumnosNotas($pdo, $id_curso, $id_curso_materia);

if (empty($alumnos)) {
    echo '<div style="padding: 20px; text-align: center; color: #666;">No se encontraron alumnos inscriptos en este curso.</div>';
    exit;
}
?>

<form id="formCalificaciones">
    <input type="hidden" name="id_curso_materia" value="<?php echo $id_curso_materia; ?>">
    
    <div class="table-card" style="background: var(--bg-card, #fff); border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: var(--bg-secondary, #f8f9fa);">
                    <tr>
                        <th style="padding: 12px; text-align: left;">Alumno</th>
                        <th style="padding: 12px; font-size: 13px;">1. UI/UX</th>
                        <th style="padding: 12px; font-size: 13px;">2. Código Limpio</th>
                        <th style="padding: 12px; font-size: 13px;">3. APIs / Base de Datos</th>
                        <th style="padding: 12px; font-size: 13px;">4. Testing / Errores</th>
                        <th style="padding: 12px; font-size: 13px;">5. Defensa y Doc.</th>
                        <th style="padding: 12px; font-size: 13px; background-color: rgba(0,0,0,0.03);">Promedio Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $alumno): 
                        $id_alumno = $alumno['id'];
                        // Notas recuperadas de la base de datos (n1 a n5)
                        $nota1 = isset($alumno['n1']) ? $alumno['n1'] : '';
                        $nota2 = isset($alumno['n2']) ? $alumno['n2'] : '';
                        $nota3 = isset($alumno['n3']) ? $alumno['n3'] : '';
                        $nota4 = isset($alumno['n4']) ? $alumno['n4'] : '';
                        $nota5 = isset($alumno['n5']) ? $alumno['n5'] : '';
                        $promedio_guardado = isset($alumno['promedio_general']) ? $alumno['promedio_general'] : '';
                    ?>
                        <tr class="fila-alumno">
                            <td style="text-align: left; font-weight: 500; padding: 10px;"><?php echo htmlspecialchars($alumno['apellido'] . ', ' . $alumno['nombre']); ?></td>
                            
                            <!-- 1. UI/UX -->
                            <td>
                                <input type="number" step="0.5" min="1" max="10" placeholder="--"
                                       name="notas[<?php echo $id_alumno; ?>][1]" 
                                       value="<?php echo $nota1 !== null && $nota1 !== '' ? $nota1 : ''; ?>" 
                                       class="form-control nota-criterio" style="width: 70px; text-align: center; margin: 0 auto;">
                            </td>
                            
                            <!-- 2. Código Limpio -->
                            <td>
                                <input type="number" step="0.5" min="1" max="10" placeholder="--"
                                       name="notas[<?php echo $id_alumno; ?>][2]" 
                                       value="<?php echo $nota2 !== null && $nota2 !== '' ? $nota2 : ''; ?>" 
                                       class="form-control nota-criterio" style="width: 70px; text-align: center; margin: 0 auto;">
                            </td>

                            <!-- 3. APIs / Base de Datos -->
                            <td>
                                <input type="number" step="0.5" min="1" max="10" placeholder="--"
                                       name="notas[<?php echo $id_alumno; ?>][3]" 
                                       value="<?php echo $nota3 !== null && $nota3 !== '' ? $nota3 : ''; ?>" 
                                       class="form-control nota-criterio" style="width: 70px; text-align: center; margin: 0 auto;">
                            </td>

                            <!-- 4. Testing / Errores -->
                            <td>
                                <input type="number" step="0.5" min="1" max="10" placeholder="--"
                                       name="notas[<?php echo $id_alumno; ?>][4]" 
                                       value="<?php echo $nota4 !== null && $nota4 !== '' ? $nota4 : ''; ?>" 
                                       class="form-control nota-criterio" style="width: 70px; text-align: center; margin: 0 auto;">
                            </td>

                            <!-- 5. Defensa y Doc. -->
                            <td>
                                <input type="number" step="0.5" min="1" max="10" placeholder="--"
                                       name="notas[<?php echo $id_alumno; ?>][5]" 
                                       value="<?php echo $nota5 !== null && $nota5 !== '' ? $nota5 : ''; ?>" 
                                       class="form-control nota-criterio" style="width: 70px; text-align: center; margin: 0 auto;">
                            </td>

                            <!-- Promedio Final (Visual e Input Oculto) -->
                            <td style="background-color: rgba(0,0,0,0.01);">
                                <span class="promedio-final" style="font-weight: bold; font-size: 15px; color: var(--primary, #0d6efd);">
                                    <?php echo $promedio_guardado !== '' && $promedio_guardado !== null ? $promedio_guardado : '--'; ?>
                                </span>
                                <input type="hidden" name="promedio_final[<?php echo $id_alumno; ?>]" class="input-promedio-hidden" value="<?php echo $promedio_guardado; ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px;">Guardar Calificaciones</button>
        </div>
    </div>
</form>